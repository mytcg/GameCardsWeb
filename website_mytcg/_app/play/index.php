<?php
require_once("../../config.php");
require('../../func.php');
$sCRLF="\r\n";
$sTab=chr(9);
$uID = 11;

$pre = $Conf["database"]["table_prefix"];

if(isset($_GET['img_server']))
{
	 $query='SELECT description '
            .'FROM '.$pre.'_imageserver '
            .'WHERE imageserver_id = 1';
            
	$aImgServer=myqu($query);
    
	echo '<response>'.$sCRLF;
	echo $sTab.'<data>'.$sCRLF;
	echo $sTab.$sTab.'<fullimageserver>'.$aImgServer[0]['description'].'</fullimageserver>'.$sCRLF;
	echo $sTab.'</data>'.$sCRLF;
	echo '</response>'.$sCRLF;
}

function getPrize($userID,$gameID,$choice,$win){
  $query = 'UPDATE mytcg_games_played '
          .'SET rewarded = 1 '          
          .'WHERE game_id = "'.$gameID.'" '
          .'AND joomla_user_id = "'.$userID.'"';
  myqu($query);

  if ($choice == 0){
    $query = 'UPDATE mytcg_user '
            .'SET credits = credits + 30, xp = xp + '.$win       
            .' WHERE joomla_user_id = "'.$userID.'"';
    myqu($query);
  }
  else{
    $query = 'UPDATE mytcg_user '
            .'SET xp = xp + 40 + '.$win       
            .' WHERE joomla_user_id = "'.$userID.'"';
    myqu($query);
  }
}

function getDeckCards($userID,$catID){
  $sCRLF="\r\n";
  $sTab=chr(9);
  $cardList = "";
  $query = 'SELECT C.card_id, UC.usercard_id, C.description, S.value, C.image_id '
          .'FROM mytcg_usercard UC '
          .'INNER JOIN mytcg_card C ON UC.card_id = C.card_id '
          .'INNER JOIN mytcg_system S ON S.parameter = C.server_image_thumb '
          .'WHERE UC.joomla_user_id="'.$userID.'" '
          .'AND UC.status="0" AND C.category_id = "'.$catID.'" '
          .'GROUP BY UC.usercard_id '
          .'ORDER BY RAND(NOW()) '
          .'LIMIT 10 ';
  $aCardData=myqu($query);
  $iCount = 0;
  $cardList .=  '<cards>'.$sCRLF;
  while ($iCardID=$aCardData[$iCount]['usercard_id']){
    $cardList .=  $sTab.'<card_'.$iCount.' val="'.$iCardID.'">'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<image val="'.$aCardData[$iCount]['value'].$aCardData[$iCount]['image_id'].'" />'.$sCRLF;
    $cardList .=  $sTab.'</card_'.$iCount.'>'.$sCRLF;
    $iCount++;
  }
  $cardList .=  '<iCount val="'.$iCount.'" />'.$sCRLF;
  $cardList .=  '</cards>'.$sCRLF;
  return $cardList;
}

function getAICards($catID){
  $sCRLF="\r\n";
  $sTab=chr(9);
  $cardList = "";
  $query = 'SELECT C.card_id, S.value, C.image_id '
          .'FROM mytcg_card C '
          .'INNER JOIN mytcg_system S ON S.parameter = C.server_image_thumb '
          .'WHERE C.category_id = "'.$catID.'" '
          .'GROUP BY C.card_id '
          .'ORDER BY RAND() LIMIT 10; ';

  $aCardData=myqu($query);
  $iCount = 0;
  $cardList .=  '<cards>'.$sCRLF;
  while ($iCardID=$aCardData[$iCount]['card_id']){
     $cardList .=  $sTab.'<card_'.$iCount.' val="'.$iCardID.'">'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<image val="'.$aCardData[$iCount]['value'].$aCardData[$iCount]['image_id'].'" />'.$sCRLF;
    $cardList .=  $sTab.'</card_'.$iCount.'>'.$sCRLF;
    $iCount++;
  }
  $cardList .=  '<iCount val="'.$iCount.'" />'.$sCRLF;
  $cardList .=  '</cards>'.$sCRLF;
  return $cardList;
}


if (cleanInput($_GET['decks'])){
	$userID = $_SESSION['userDetails']['user_id'];     
  $query = 'SELECT JC.category_name,JC.category_id '
          .'FROM mytcg_usercard UC '
          .'INNER JOIN mytcg_card C ON C.card_id = UC.card_id '
          .'INNER JOIN mytcg_category JC ON JC.category_id = C.category_id '
          .'WHERE UC.joomla_user_id="'.$userID.'" '
          .'GROUP BY JC.category_id '
          .'HAVING COUNT(UC.usercard_id) > 1 '
          .'ORDER BY JC.category_name';
  $aCardData=myqu($query);
  echo '<categories>'.$sCRLF;
  $iCount = 0;
  while ($iCatID=$aCardData[$iCount]['category_id']){
    echo $sTab.'<cat_'.$iCount.' val="'.$iCatID.'">'.$sCRLF;    
    echo $sTab.$sTab.'<title val="'.$aCardData[$iCount]['category_name'].'" />'.$sCRLF;
    echo $sTab.'</cat_'.$iCount.'>'.$sCRLF;
    $iCount++;
  }
  echo $sTab.'<iCount val="'.$iCount.'" />'.$sCRLF;
  echo '</categories>'.$sCRLF;
  
  exit;
}

if ($iCatID = cleanInput($_GET['startgame'])){     
  $query = 'INSERT INTO mytcg_games_played (user_id,category_id) VALUES ("'.$uID.'","'.$iCatID.'") ';
  myqu($query);
  $query = 'SELECT game_id FROM mytcg_games_played ORDER BY game_id DESC LIMIT 1';
  $aCardData=myqu($query);
  echo $aCardData[0]['game_id']; 
  exit;
}

if ($gameID = cleanInput($_GET['getPrize'])){
  
  $query = 'SELECT won,result,rewarded '
          .'FROM mytcg_games_played '          
          .'WHERE game_id = "'.$gameID.'" '
          .'AND joomla_user_id = "'.$uID.'" '
          .'LIMIT 1';
  $aYourData=myqu($query);
  $win = $aYourData[0]['won'];
  $result = $aYourData[0]['result'];
  $rewarded = $aYourData[0]['rewarded'];
  
  if (($result == "Victory")&&($rewarded == 0)){
    getPrize($uID,$gameID,$_GET['choice'],$win);
  }
  exit;
}

if (cleanInput($_GET['init'])){
  exit;
}
  
if ($catID = cleanInput($_GET['getplaydeck'])){
  echo getDeckCards($uID,$catID);
  exit;
}
if ($catID = cleanInput($_GET['getaideck'])){
  echo getAICards($catID);
  exit;
}

if ($usercardID = cleanInput($_GET['getStatsList'])){
  $cardList = "";
  $query = 'SELECT UC.usercard_id,S.stat_id,CS.stat_value, C.card_id, CS.stat_display_value,CS.top,CS.left '
          .'FROM mytcg_usercard UC '
          .'INNER JOIN mytcg_card C ON UC.card_id = C.card_id '
          .'INNER JOIN mytcg_stats S ON C.category_id = S.category_id '
          .'INNER JOIN mytcg_card_stats CS ON S.stat_id = CS.stat_id '
          .'WHERE UC.usercard_id = "'.$usercardID.'" AND CS.card_id = C.card_id';
  $aCardData=myqu($query);
  $iCount = 0;
  $cardList .=  '<stats>'.$sCRLF;
  while ($iCardID=$aCardData[$iCount]['card_id']){
    $cardList .=  $sTab.'<stat_'.$iCount.' val="'.$iCardID.'">'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<statid val="'.$aCardData[$iCount]['stat_id'].'" />'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<value val="'.$aCardData[$iCount]['stat_value'].'" />'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<display val="'.$aCardData[$iCount]['stat_display_value'].'" />'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<top val="'.$aCardData[$iCount]['top'].'" />'.$sCRLF;
    $cardList .=  $sTab.$sTab.'<left val="'.$aCardData[$iCount]['left'].'" />'.$sCRLF;
    $cardList .=  $sTab.'</stat_'.$iCount.'>'.$sCRLF;
    $iCount++;
  }
  $cardList .=  '<iCount val="'.$iCount.'" />'.$sCRLF;
  $cardList .=  '</stats>'.$sCRLF;
  echo $cardList;
  exit;
}

if (cleanInput($_GET['playerID'])){
  $playerCardID = $_GET['playerID'];
  $aiCardID = $_GET['aiID'];
  $statID = $_GET['stat'];
  $gameID = $_GET['gameid'];
  $return = "";
  $query = 'SELECT CS.stat_value '
          .'FROM mytcg_usercard UC '
          .'INNER JOIN mytcg_card C ON UC.card_id = C.card_id '
          .'INNER JOIN mytcg_stats S ON C.category_id = S.category_id '
          .'INNER JOIN mytcg_card_stats CS ON S.stat_id = CS.stat_id '
          .'WHERE UC.usercard_id = "'.$playerCardID.'" AND CS.card_id = C.card_id '
          .'AND CS.card_id = C.card_id '
          .'AND S.stat_id = '.$statID;
  $aYourData=myqu($query);
  $yourValue = $aYourData[0]['stat_value'];  
  
  $query = 'SELECT CS.stat_value '
          .'FROM mytcg_card C '
          .'INNER JOIN mytcg_stats S ON C.category_id = S.category_id '
          .'INNER JOIN mytcg_card_stats CS ON S.stat_id = CS.stat_id '
          .'WHERE C.card_id = "'.$aiCardID.'" AND CS.card_id = C.card_id '
          .'AND CS.card_id = C.card_id '
          .'AND S.stat_id = '.$statID;
  $aAiData=myqu($query);
  $aiValue = $aAiData[0]['stat_value'];    
  
  if ($yourValue > $aiValue){
    myqu("UPDATE mytcg_games_played SET win = win + 1 WHERE game_id = ".$gameID);
    $return .= "Win";
  }
  elseif ($yourValue < $aiValue){
    myqu("UPDATE mytcg_games_played SET lose = lose + 1 WHERE game_id = ".$gameID);
    $return .= "Loss";
  }
  else{
    myqu("UPDATE mytcg_games_played SET tied = tied + 1 WHERE game_id = ".$gameID);
    $return .= "Tied";
  }
  
  echo $return;
  exit;
}

if ($gameID = cleanInput($_GET['getResult'])){

  $query = 'SELECT win,tied,lose '
          .'FROM mytcg_games_played '          
          .'WHERE game_id = "'.$gameID.'" '
          .'LIMIT 1';
  $aYourData=myqu($query);
  $win = $aYourData[0]['win'];
  $loss = $aYourData[0]['lose'];
    
  if ($win > $loss)
    $result = "Victory";
  elseif ($win < $loss)
    $result = "Defeat";
  else
    $result = "Draw";
  
  $query = 'UPDATE mytcg_games_played '
          .'SET result = "'.$result.'" '          
          .'WHERE game_id = "'.$gameID.'" ';
  myqu($query);
  echo($result."||".$win."||".$gameID);
  exit;
}
?>