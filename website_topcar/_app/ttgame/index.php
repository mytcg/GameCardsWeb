<?php

  //GET REQUIRED FILES
  require_once("../../config.php");
  require_once("../../func.php");
  $sCRLF="\r\n";
  $sTab=chr(9);
  
  //main setup
  $required_deck_size = 20;
  $cpuplayer = false;
  $cardcategory = 15;
  
  //cards probabilities
  //rarity (1,2,3) = (Common,Uncommon,Rare) repectively
  $cpulevel = array();
  $cpulevel[1] = array();
  $cpulevel[1][1] = 60; //[AI level][rarity] = Percentage
  $cpulevel[1][2] = 30; //[AI level][rarity] = Percentage
  $cpulevel[1][3] = 10; //[AI level][rarity] = Percentage
  
  
  //SETUP PREFIX FOR TABLES
  $pre = $Conf["database"]["table_prefix"];

  $userID = $_SESSION["user"]["id"];
  

      
  
  if($userID){
    
  //Initialization of the game with the oponent (0 = CPU), and deck of the user
  if($_GET['init'] == 1){
         
    //get the user's deck id
    //TODO: Sanitize these plzktnx
    $deckid = $_GET['deckid'];
    $opponentid = $_GET['oponentid'];
    
    //little bit of validation
    if(!is_numeric(intval($deckid))){
      send_error(1,"Invalid deck id.");
    }
    
    //get user's deck cards
     $deck = myqu('SELECT * from '.$pre.'_usercard where user_id = '.$userID.' and deck_id = '.$deckid);
     
     //check if deck is correct size
     if(count($deck) > $required_deck_size){
       send_error(2,"Selected deck has too many cards. Requered size is ".$required_deck_size." cards. (Current :".count($deck).")");
     }
     if(count($deck) < $required_deck_size){
       send_error(3,"Selected deck has too few cards. Requered size is ".$required_deck_size." cards. (Current :".count($deck).")");
     }
     
     //are we playing against the CPU?
     if($opponentid == 0){
       $cpuplayer = true;
     }
     
     //check if we have a valid oponent
     if(!$cpuplayer){
       $user = myqu('SELECT * from '.$pre.'_user where user_id = '.$opponentid);
       
       if(count($user) != 1){
         send_error(4,"Invalid user selected.");
       }
     }
     
     //it seems like everything is ok. Lets setup the game
     
     //greate game session
     $gameid = myqu('INSERT INTO '.$pre.'_ttgame (player1,player2,player1_score,player2_score,last_activity) VALUES ('.$userID.','.$opponentid.',0,0,NOW())');
     
     if(!$gameid){
         send_error(5,"An error occured while creating the game session.");
     }

     //setup user stacks
     //player1
     /// Slight alteration. we will eval the stats form a columns in the stack table
     
     foreach($deck as $card){
         
       
       $attribs = myqu('SELECT * from '.$pre.'_cardstat where card_id = '.$card['card_id']);
       $cardstats = array();
       
       //foreach($attribs as $attrib){
       //  $attrib
       //}
       
       $attribcode = serialize($attribs);
       
       $attribcode = str_replace("\"","&quot;",$attribcode);
       
       $stackcardid = myqu('INSERT INTO '.$pre.'_ttstack (game_id,player_id,card_id,used,stats) VALUES ('.$gameid.','.$userID.','.$card['card_id'].',0,"'.$attribcode.'")');
       if(!$stackcardid){
           send_error(6,"An error occured while setting up the deck of Player 1.");
       }
     }
     
     //player2 || CPU
      if(!$cpuplayer){
       //another user 
       // this should be left empty, to allow hosting and joining of different games.
       // these entries is best made after a game has been accepted
       
      }else{
       //build the CPU deck
       //common cards
       $commoncards = myqu('SELECT * FROM '.$pre.'_card WHERE category_id = '.$cardcategory.' AND cardquality_id = 1');
       //uncommon
       $uncommoncards = myqu('SELECT * FROM '.$pre.'_card WHERE category_id = '.$cardcategory.' AND cardquality_id = 2');
       
       //rare
       $rarecards = myqu('SELECT * FROM '.$pre.'_card WHERE category_id = '.$cardcategory.' AND cardquality_id = 3');
       
       //ok, lets load the cards, commons first, then uncommons, lastly the rares
       $cpudeck = array();
       
       for($i = 0;$i < intval($required_deck_size * ($cpulevel[1][1] / 100));$i++){
         $randomcard = rand(0,count($commoncards) - 1);
         if($commoncards[$randomcard]){
          $cpudeck[] = $commoncards[$randomcard];
         }
       }
       
       for($i = 0;$i < intval($required_deck_size * ($cpulevel[1][2] / 100));$i++){
         $randomcard = rand(0,count($uncommoncards) - 1);
         if($uncommoncards[$randomcard]){
          $cpudeck[] = $uncommoncards[$randomcard];
         }
       }
       
       for($i = 0;$i < intval($required_deck_size * ($cpulevel[1][3] / 100));$i++){
         $randomcard = rand(0,count($rarecards) - 1);
         if($rarecards[$randomcard]){
          $cpudeck[] = $rarecards[$randomcard];
         }
       }
       
       $cardname = array();
       $cardnamerarity = array();
       $cardnamerarity[1] = "Common";
       $cardnamerarity[2] = "Uncommon";
       $cardnamerarity[3] = "Rare";
       
       if(count($cpudeck) != 20){
         send_error(8,"The CPU was unable to construct a deck of the correct size. (Required:".$required_deck_size.", Current:".count($cpudeck).")");
       }
       
       foreach($cpudeck as $card){
         $attribs = myqu('SELECT * from '.$pre.'_cardstat where card_id = '.$card['card_id']);
         $attribcode = serialize($attribs);
       
         $attribcode = str_replace("\"","&quot;",$attribcode);
       
         $stackcardid = myqu('INSERT INTO '.$pre.'_ttstack (game_id,player_id,card_id,used,stats) VALUES ('.$gameid.',0,'.$card['card_id'].',0,"'.$attribcode.'")');
         if(!$stackcardid){
             send_error(6,"An error occured while setting up the deck of the CPU.");
         }
       }
       
       //ok, everything is setup, lets send back the game data
       // we need the game id, oponent?,the user's deck with available attributes... nasty
       
       //get a frash copy of the user's deck in use in the game.
       $returndeck = myqu('SELECT * from '.$pre.'_ttstack where player_id = '.$userID.' and game_id = '.$gameid);

       $counter = 0;
       $returndata = array();
       
       $returndata['game_id'] = $gameid;
       
       foreach($returndeck as $card){
         $returndata['gamecard_id_'.$counter] = $card['id'];
         $returndata['card_id_'.$counter] = $card['card_id'];
         $counter++;
       }
       
       $returndata['gamecard_count'] = $counter;

       //get the attributes of the cards...
       $returnattributes = myqu('SELECT * from '.$pre.'_categorystat where category_id = '.$cardcategory);
     
       
       $counter = 0;
       foreach($returnattributes as $attribute){
         $returndata['categorystat_id_'.$counter] = $attribute['categorystat_id'];
         $returndata['description_'.$counter] = $attribute['description'];
         $counter++;
       }
       
       $returndata['attribute_count'] = $counter;
       echo '<root>'.xmlize($returndata).'</root>';    
       
      }


   
  }
  
  
      if($_GET['compare'] == 1){
        
        $gameid = $_GET['gameid'];
        $gamecardid = $_GET['gamecardid'];
        $statid = $_GET['statid'];
        
         $attribcode = serialize($attribs);
       
         $attribcode = str_replace("\"","&quot;",$attribcode);
       
         $stackcard = myqu('SELECT * FROM '.$pre.'_ttstack WHERE id = '.$gamecardid.' and game_id = '.$gameid.' and used = 0;');
       
         if(!$stackcard){
           send_error(9,"Nu uh, you already used that card.");
         }
         
         $cpucards = myqu('SELECT * FROM '.$pre.'_ttstack WHERE game_id = '.$gameid.' and player_id = 0 and used = 0;');
         
         $cpucount = count($cpucards);
         
         $randomcard = rand(0,$cpucount);
         
         //retrieve stats
         $playerstats = unserialize(str_replace("&quot;","\"",$stackcard[0]['stats']));
         $cpustats = unserialize(str_replace("&quot;","\"",$cpucards[$randomcard]['stats']));
         
         $pvalue = 0;
         
         foreach($playerstats as $playerstat){
           if($playerstat['categorystat_id'] == $statid){
             $pvalue = $playerstat['statvalue'];
           }
           
         }
         
         $cvalue = 0;
         
         foreach($cpustats as $cpustat){
           if($cpustat['categorystat_id'] == $statid){
             $cvalue = $cpustat['statvalue'];
           }
           
         }
         
         //results 0 = lose, 1 = win , 2 = draw
         $roundresult = 0;
         if($pvalue == $cvalue){
           $roundresult = 2;
         }else{
           if($pvalue > $cvalue){
             $roundresult = 1;
           }else{
             $roundresult = 0;
           }
         }
         
         //mark cards used
         $updated = myqu('UPDATE '.$pre.'_ttstack SET used = 1 where id IN ('.$stackcard[0]['id'].','.$cpucards[$randomcard]['id'].');');
         
         //set scores
         if($roundresult == 1){
          $updated = myqu('UPDATE '.$pre.'_ttgame SET player1_score = player1_score+1 where id = '.$gameid); 
         }
         
         if($roundresult == 0){
          $updated = myqu('UPDATE '.$pre.'_ttgame SET player2_score = player2_score+1 where id = '.$gameid); 
         }
         
         $currentscore = myqu('SELECT * FROM '.$pre.'_ttgame where id = '.$gameid); 
         
         
         
         //return winner,cpu card id,score?
         echo "<root>".$sCRLF;
          echo $sTab."<result>".$roundresult."</result>".$sCRLF;
          echo $sTab."<cardid>".$cpucards[$randomcard]['card_id']."</cardid>".$sCRLF;
          echo $sTab."<score>".$currentscore[0]['player1_score']."/".$currentscore[0]['player2_score']."</score>".$sCRLF;
          echo "</root>";
          exit;

      }

    
    
  }else{

    send_error(7,"Nice try, now bugger off.");
    
  }

function send_error($errorcode,$errormessage){

echo "<root>".$sCRLF;
echo $sTab."<error>".$errorcode."</error>".$sCRLF;
echo $sTab."<message>".$errormessage."</message>".$sCRLF;
echo "</root>";
exit;

}

// generates xml from an array object
function xmlize($object){
  $xml = "";
  global $sTab;
  global $sCRLF;
  
  foreach($object as $key => $value){
      if(!is_numeric($key)){
        $xml .= $sTab.$sTab."<$key>$value</$key>".$sCRLF;
      }
  }
  return $xml;
    
}
  
?>