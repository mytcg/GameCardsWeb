<?php
require_once("config.php");

  //GENERATES THE CONTENTS OF A BOOSTER PACK AND GIVES IT TO THE USER
function openBooster($pre,$userID,$packID){
  $iReleasedBuffer = 1;
  
  //CARD COUNT OF PACK
  $iPackCount = myqu("SELECT no_of_cards FROM mytcg_product WHERE product_id={$packID}");
  $iPackCount = $iPackCount[0]['no_of_cards'];
  
  $aQuality = myqu("SELECT distinct cq.cardquality_id,((cq.booster_probability)*{$iPackCount}) AS bp 
    FROM mytcg_cardquality cq 
    INNER JOIN mytcg_card c 
    ON c.cardquality_id = cq.cardquality_id 
    INNER JOIN mytcg_productcard pc 
    ON pc.card_id = c.card_id 
    WHERE pc.product_id = {$packID} 
    ORDER BY booster_probability ASC");
  $iQualityID = 0;
  $cards = array();
  
  //GET CARDS
  for ($i = 0; $i < $iPackCount; $i++){
    //GET A RANDOM QUALITY CARD
    $iQualityID = randomQualityID($aQuality,$iPackCount);
  
    //GET STACK OF SAME QUALITY CARDS
    $aGetCards = myqu(" SELECT c.card_id, c.cardquality_id, cq.booster_probability
      FROM mytcg_card c
      INNER JOIN  mytcg_productcard pc
      ON pc.card_id = c.card_id
      INNER JOIN mytcg_cardquality AS cq
      ON cq.cardquality_id = c.cardquality_id
      WHERE pc.product_id={$packID}
      AND c.cardquality_id={$iQualityID}");
    $iNumCards = sizeof($aGetCards);
    
    //PICK A RANDOM CARD FROM THE STACK
    $iRandom=rand(0,$iNumCards-1);
    $iCardID=$aGetCards[$iRandom]['card_id'];
          
    //GIVE THE CARD TO THE USER
    $aCards=myqu("INSERT mytcg_usercard (user_id, card_id, usercardstatus_id, is_new)
      SELECT {$userID}, {$iCardID}, usercardstatus_id, 1 
      FROM mytcg_usercardstatus
      WHERE description = 'Album'");
    $card;
    if ($cards[$iCardID] == null) {
      $card = array();
      $card['cardId'] = $iCardID;
      $card['quantity'] = 1;
    }
    else {
      $card = $cards[$iCardID];
      $card['quantity'] = $card['quantity']+1;
    }
    $cards[$iCardID] = $card;
  }
  
  //we can remove one of the products from stock though
  myqu("UPDATE mytcg_product SET in_stock=in_stock-1 WHERE product_id={$packID}");
  
  return $cards;
}
  
//ROLL DICE AND CHECK WHAT QUALITY CARD THE USER RECEIVES 
function randomQualityID($aQuality,$iPackCount){
  $iRandom = rand(1, $aQuality[sizeof($aQuality) - 1]['bp']);//rand(1,$iPackCount);
  $interval=0;
  for($l=0; $l < sizeof($aQuality); $l++){
      $interval += $aQuality[$l]['bp'];
        if ($iRandom <= $interval){
          $iQualityID = $aQuality[$l]['cardquality_id'];
          break;
    }
  }
  return $iQualityID;
}

function validip($ip){
  if (!empty($ip) && ip2long($ip)!=-1){
    $reserved_ips = array(
      array('0.0.0.0','2.255.255.255'), 
      array('10.0.0.0','10.255.255.255'),
      array('127.0.0.0','127.255.255.255'),
      array('169.254.0.0','169.254.255.255'),
      array('172.16.0.0','172.31.255.255'),
      array('192.0.2.0','192.0.2.255'),
      array('192.168.0.0','192.168.255.255'),
      array('255.255.255.0','255.255.255.255')
    );
   
    foreach ($reserved_ips as $r) {
      $min = ip2long($r[0]);
      $max = ip2long($r[1]);
      if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
    }
    return true;
  } else {
    return false;
  }
}
 
function getip(){
  if (validip($_SERVER["HTTP_CLIENT_IP"])) {
    return $_SERVER["HTTP_CLIENT_IP"];
  }

  foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
    if (validip(trim($ip))) {
      return $ip;
    }
  }
 
  if (validip($_SERVER["HTTP_X_FORWARDED"])) {
    return $_SERVER["HTTP_X_FORWARDED"];
  } elseif (validip($_SERVER["HTTP_FORWARDED_FOR"])) {
    return $_SERVER["HTTP_FORWARDED_FOR"];
  } elseif (validip($_SERVER["HTTP_FORWARDED"])) {
    return $_SERVER["HTTP_FORWARDED"];
  } elseif (validip($_SERVER["HTTP_X_FORWARDED"])) {
    return $_SERVER["HTTP_X_FORWARDED"];
  } else {
    return $_SERVER["REMOTE_ADDR"];
  }
}


//clears any actions that when limit is up
function updateAuctions() {
  //Select details of the auction
  $query = ('SELECT a.market_id, a.marketstatus_id, a.user_id owner, a.usercard_id, x.username ownername,
            IFNULL(b.price,0) price, IFNULL(b.user_id,-1) bidder, u.username, date_expired, d.description
            FROM mytcg_market a 
            LEFT OUTER JOIN mytcg_marketcard b 
            ON a.market_id = b.market_id 
            LEFT OUTER JOIN mytcg_user u
            ON b.user_id = u.user_id
            INNER JOIN mytcg_user x
            ON a.user_id = x.user_id
            INNER JOIN mytcg_usercard c
            ON a.usercard_id = c.usercard_id
            INNER JOIN mytcg_card d
            ON c.card_id = d.card_id
            WHERE datediff(now(), date_expired) >= 1 
            AND marketstatus_id = 1
            AND (b.price = (select max(price) 
                          from mytcg_marketcard c
                          where c.market_id = a.market_id 
                          group by market_id)
            OR ISNULL(b.price))');
  
  $auctions = myqu($query);
  
  $count = 0;
  foreach ($auctions as $auction) {
    //set the auction to expired
    $query = "update mytcg_market set marketstatus_id = '2' where market_id = ".$auction['market_id'];
    myqu($query);
    
    //add the credits to the user who was auctioning the card
    $query = "update mytcg_user set credits = credits + ".$auction['price']." where user_id = ".$auction['owner'];
    myqu($query);
    
    //set the cards status back to Album
    if ($auction['bidder'] == -1) {
      $query = "update mytcg_usercard set usercardstatus_id = (select usercardstatus_id from mytcg_usercardstatus where description = 'Album'), user_id = ".$auction['owner']." where usercard_id = ".$auction['usercard_id'];
      myqui('INSERT INTO mytcg_notifications (user_id, notification, notedate)
          VALUES ('.$auction['owner'].', "Auction ended on '.$auction['description'].' with no highest bidder.", now())');
    } else {
      $query = "update mytcg_usercard set usercardstatus_id = (select usercardstatus_id from mytcg_usercardstatus where description = 'Received'), user_id = ".$auction['bidder']." where usercard_id = ".$auction['usercard_id'];
      
      myqui('INSERT INTO mytcg_transactionlog (user_id, description, date, val)
        VALUES ('.$auction['owner'].', "Received '.$auction['price'].' credits for auctioning '.$auction['description'].' to '.$auction['username'].'.", now(), '.$auction['price'].')');
        
      myqui('INSERT INTO mytcg_notifications (user_id, notification, notedate)
        VALUES ('.$auction['owner'].', "Auctioned '.$auction['description'].' to '.$auction['username'].' for '.$auction['price'].' credits.", now())');
        
      myqui('INSERT INTO mytcg_transactionlog (user_id, description, date, val)
        VALUES ('.$auction['bidder'].', "Spent '.$auction['price'].' credits for winning the auction '.$auction['description'].' from '.$auction['ownername'].'.", now(), -'.$auction['price'].')');
        
      myqui('INSERT INTO mytcg_notifications (user_id, notification, notedate)
        VALUES ('.$auction['bidder'].', "Won auction '.$auction['description'].' from '.$auction['ownername'].' for '.$auction['price'].' credits.", now())');
    }
    
    myqu($query);
    
    $count++;
  }
}

function getUserData($prefix, $userId='')
{
	$userId = ($userId == '') ? $_SESSION['user']['id'] : $userId; 
	$sql = "SELECT user_id, username, password, date_last_visit, mobile_date_last_visit , credits, xp, freebie, completion_process_stage "
		."FROM ".$prefix."_user "
		."WHERE user_id='".$userId."' "
		."AND is_active='1'";
	return myqu($sql);
}

function getCardOwnedCount($cardID)
{
  $sql = "SELECT COUNT(card_id) AS iNr
          FROM mytcg_usercard UC
          INNER JOIN mytcg_usercardstatus UCS ON UCS.usercardstatus_id = UC.usercardstatus_id
          WHERE UC.user_id = ".$_SESSION['user']['id']." AND UC.card_id = ".$cardID." AND UCS.description = 'Album'";
    $r = myqu($sql);
  return $r[0]['iNr'];
}

function getCardCategories($iCatID){
  global $Conf;
  $pre = $Conf["database"]["table_prefix"];
  $sCats = "";
  //GET LEVEL NR
  $sql = "SELECT level FROM ".$pre."_category WHERE category_id=".$iCatID;
  $level = myqu($sql);
  $level = $level[0]['level'];
  
  if($level==3){ //No subs
    $sCats = $iCatID;
  }
  elseif($level==2){ //Subs 1 level down
    $sql = "SELECT category_id FROM ".$pre."_category WHERE parent_id=".$iCatID;
    $aCats = myqu($sql);
    foreach($aCats as $cat){
      $sCats .= $cat['category_id'].",";
    }
    $sCats = substr($sCats, 0, -1);
  }
  elseif($level==1){ //Subs 2 levels down
    $sql = "SELECT category_id FROM ".$pre."_category WHERE parent_id=".$iCatID;
    $aSub = myqu($sql);
    $aSub = $aSub[0]['category_id'];
    
    $sql = "SELECT category_id FROM ".$pre."_category WHERE parent_id=".$aSub;
    $aCats = myqu($sql);
    foreach($aCats as $cat){
      $sCats .= $cat['category_id'].",";
    }
    $sCats = substr($sCats, 0, -1);
  }
  return $sCats;
}


function sendEmail($sEmailAddress,$sFromEmailAddress,$sSubject,$sMessage){
$sHeaders='From: '.$sFromEmailAddress;
mail($sEmailAddress,$sSubject,$sMessage,$sHeaders);
}


function cleanInput($sDirtyInput){
	return $sDirtyInput;
}


function findSQLValueFromKey($aData,$sCategory,$sKey){
	$iFound=0;
	$iCount=0;
	$sOutput="";
	while ((!$iFound)&&($sValue=$aData[$iCount]["keyname"])){
		if (($sValue==$sKey)&&($sCategory==$aData[$iCount]["category"])){
			$sOutput=$aData[$iCount]["keyvalue"];
			$iFound=1;
		} else {
			$iCount++;
		}
	}
	return $sOutput;
}


function sanitize($sStringUserInput){
	$sString=htmlspecialchars($sStringUserInput);
	if (mb_detect_encoding($sString)!="UTF-8"){
		$sString=utf8_encode($sString);	
	}
	return $sString;
}
/*
function customError($errno,$errstr){
  $aFileHandle=fopen("var/errorlog.log","a+");
  fwrite($aFileHandle,"[".date("Y-m-d H:i:s")."] Err:".$errno." - ".$errstr." - ".$_SERVER['PHP_SELF']."\r\n");
  fclose($aFileHandle);
  die();
}
set_error_handler("customError");
*/

// execute mysql query and log, return in associative array 
function myqu($sQuery){	  
	global $Conf;
  $db = $Conf["database"];
  $aOutput=array();
  $pattern = '/INSERT/i';
  
	$aLink=mysqli_connect($db["host"],$db["username"],$db["password"],$db["databasename"]);
	$sQuery=str_replace("&nbsp;","",$sQuery);
	$sQueryCut=substr($sQuery,0,1500);
  
	if($aResult=@mysqli_query($aLink, $sQuery))
	{
    
		//If insert - return last insert id
		if(preg_match($pattern, $sQuery)){
			$mp = mysqli_insert_id($aLink);
			@mysqli_free_result($aResult);
      mysqli_close($aLink);
			return $mp;
		}
    //Else build return array
		while ($aRow=@mysqli_fetch_array($aResult,MYSQL_BOTH)){
			$aOutput[]=$aRow;
		}
		return $aOutput;
	}
  else{
    $aFileHandle=fopen("/usr/www/users/mytcga/var/sqlq.log","a+");
    fwrite($aFileHandle,"[".date("Y-m-d H:i:s")."] Err:".mysqli_errno($aLink)." - ".mysqli_error($aLink)." - ".$_SERVER['PHP_SELF']."\r\n");
    fclose($aFileHandle);
    $aFileHandle = null;
    @mysqli_free_result($aResult);
    mysqli_close($aLink);
  }
}

?>
