<?php  
  //GET REQUIRED FILES
  require_once("../../config.php");
  require_once("../../func.php");
  $sCRLF="\r\n";
  $sTab=chr(9);
  
  
  //SETUP PREFIX FOR TABLES
  $pre = $Conf["database"]["table_prefix"];

  $userID = $_SESSION["user"]["id"];
  
  if($_GET['init'] == 1){

    echo "<gc>".$sCRLF;
    
    
      $count = 0;
      foreach($Conf["games"] as $game){
        echo "<id_".$count.">".$game['id']."</id_".$count.">".$sCRLF;
        echo "<name_".$count.">".$game['name']."</name_".$count.">".$sCRLF;
        echo "<code_".$count."><![CDATA[".$game['code']."]]></code_".$count.">".$sCRLF;
        echo "<image_".$count.">".$game['image']."</image_".$count.">".$sCRLF;
        $count++;
      }
      echo "<game_count>".$count."</game_count>".$sCRLF;
  
    echo "</gc>".$sCRLF;
     
  }
  
    if($_GET['getdecks'] == 1){

    $catid = $_GET['catid'];
    $decks = myqu('SELECT * from '.$pre.'_deck where user_id = '.$userID.' and category_id = '.$catid);

    echo "<gc>".$sCRLF;
    
      $count = 0;
      foreach($decks as $deck){
        echo "<id_".$count.">".$deck['deck_id']."</id_".$count.">".$sCRLF;
        echo "<desc_".$count.">".$deck['description']."</name_".$count.">".$sCRLF;
        echo "<image_".$count.">".$deck['image']."</image_".$count.">".$sCRLF;
        $count++;
      }
      echo "<deck_count>".$count."</deck_count>".$sCRLF;
  
    echo "</gc>".$sCRLF;
     
  }
  
  
?>