<?php
/*
this page handles requests from the handset



GETS
----
only authenticated users may use these 
1. userdetails=1					- send userdetails
2. cards=-1								- send all card details for this user
3. cards=CSV list					-	all cards except in CSV list
4. decks=1								- deck ids and names for this user
5. cardsindeck=deck_id		- cards in this deck
6. image?????????
7. allcategories=1				- list all categories 
8. auction=1							-	details of auctions
*/

date_default_timezone_set('Africa/Johannesburg'); 

/*
$sCRLF="\r\n";
$sTab=chr(9);
*/

$sCRLF="";
$sTab="";


/** get username and password from http headers */
/*
if (!isset($_SERVER['PHP_AUTH_USER'])) {
	echo 'Text to send if user hits Cancel';
}
*/
/*
print_r($_SERVER);
exit;
*/
$sUsername = $_SERVER['HTTP_AUTH_USER'];
$sPassword = base64_decode($_SERVER['HTTP_AUTH_PW']);
$iUserID=0;

if(!$sUsername){
	$sUsername = $_POST['lusername'];
	$sPassword = $_POST['lpassword'];
}

/** first authorize our user */
/*$aUserAuth=myqu('SELECT user_id, password '
	.'FROM mytcg_user '
	.'WHERE username="'.$sUsername.'"');
$aPassword=explode(':',$aUserAuth[0]['password']);
$sCrypt=JUserHelper::getCryptedPassword($sPassword, $aPassword[1]);
$sPasswordCrypted=$sCrypt.':'.$aPassword[1];
$aTestPassword=explode(':',$sPasswordCrypted);

if ($aTestPassword[0]==$aPassword[0]){
	$iUserID=$aUserAuth[0]['user_id'];
}*/
$aValidUser=myqu(
								"SELECT user_id, username, password, date_last_visit, credits "
								."FROM mytcg_user "
								."WHERE username='".$sUsername."' "
								//."AND is_active='1'"
);
$iUserID=$aValidUser[0]["user_id"];
$iMod=(intval($iUserID) % 10)+1;
$sPassword=substr(md5($iUserID),$iMod,10).md5($sPassword);
if ($sPassword!=$aValidUser[0]['password']){
	$iUserID=0;
}


//echo '['.$sUsername.']';

/** exit if user not validated, send bye bye xml to be nice */
if ($iUserID == 0){
	$sOP='<user>'.$sCRLF;
	$sOP.=$sTab.'<error>Invalid User Details</error>'.$sCRLF;	
	//$sOP.=$sTab.'<sPassword>'.$sPassword.'</sPassword>'.$sCRLF;	
	//$sOP.=$sTab.'<aValidUser>'.$aValidUser[0]['password'].'</aValidUser>'.$sCRLF;	
	//$sOP.=$sTab.'<sUsername>'.$sUsername.'</sUsername>'.$sCRLF;	
	$sOP.='</user>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;	
}


if ($iTestVersion=$_GET['update']){
	$aVersion=myqu(
		'SELECT keyvalue FROM mytcg_system '
		.'WHERE category="softwareversion"'
	);
	$iVersion=$aVersion[0]['keyvalue'];
	if (($iTestVersion*1)<($iVersion*1)){
		echo 'http://www.mytcg.net/mobi/';
	}
	exit;
}

//GETS THE CONTENTS OF A STARTER PACK AND GIVES IT TO THE USER
//also returns the card_id of the highest quality card
function openStarter($userID,$packID){
  $aGetCards = myqu("SELECT c.card_id, c.cardquality_id, cq.booster_probability
		FROM mytcg_card c
		INNER JOIN mytcg_productcard AS pc
		ON pc.card_id = c.card_id
		INNER JOIN mytcg_cardquality AS cq
		ON cq.cardquality_id = c.cardquality_id
		WHERE pc.product_id={$packID}");
  $iNumCards = sizeof($aGetCards);
	$rarity = -1;
	$card_id = 0;
  for ($i = 0; $i < $iNumCards; $i++){
    //GET CARD FROM STACK
    $iCardID = $aGetCards[$i]['card_id'];
  
    //REMOVE THE CARD FROM THE STACK
		//this bit is removed for now, as the database doesnt support individual cards amounts
    //$iReleasedLeft=$aGetCards[$i]['released_left']-1;
    //$aReleasedLeft=myqui("UPDATE mytcg_card SET released_left={$iReleasedLeft} WHERE card_id={$iCardID}");
            
    //GIVE THE CARD TO THE USER
    $aCards=myqui("INSERT mytcg_usercard (user_id, card_id, usercardstatus_id)
			SELECT {$userID}, {$iCardID}, usercardstatus_id
			FROM mytcg_usercardstatus
			WHERE description = 'default'");
		
		if ($aGetCards[$i]['booster_probability'] < $rarity || $rarity == -1) {
			$rarity = $aGetCards[$i]['booster_probability'];
			$card_id = $aGetCards[$i]['card_id'];
		}
  }
	
	//we can remove one of the products from stock though
	myqui("UPDATE mytcg_product SET in_stock=in_stock-1 WHERE product_id={$packID}");
	
	return $card_id;
}

//GENERATES THE CONTENTS OF A BOOSTER PACK AND GIVES IT TO THE USER
function openBooster($userID,$packID){
    $iReleasedBuffer = 1;
    
    //CARD COUNT OF PACK
    $iPackCount = myqu("SELECT no_of_cards_in_pack FROM mytcg_pack WHERE vm_product_id={$packID}");
    $iPackCount = $iPackCount[0]['no_of_cards_in_pack'];
    
    $aQuality = myqu("SELECT quality_id,((booster_probability / 100)*{$iPackCount}) AS bp FROM mytcg_card_quality ORDER BY booster_probability ASC");
    $iQualityID = 0;
		$iHighestQualityID = 0;
		$iRetCardID = 0;
    
    //GET CARDS
    for ($i = 0; $i < $iPackCount; $i++){
      
      //GET A RANDOM QUALITY CARD
      $iQualityID = randomQualityID($aQuality,$iPackCount);
    
      //GET STACK OF SAME QUALITY CARDS
      $aGetCards = myqu(" SELECT card_id, quality_id, released_left 
                          FROM mytcg_card 
                          WHERE vm_product_id={$packID}
                          AND released_left > {$iReleasedBuffer}
                          and quality_id = {$iQualityID}");
      $iNumCards = sizeof($aGetCards);
      
      //PICK A RANDOM CARD FROM THE STACK
      $iRandom=rand(0,$iNumCards-1);
      $iCardID=$aGetCards[$iRandom]['card_id'];
      
      //REMOVE THE CARD FROM THE STACK
      $iReleasedLeft=$aGetCards[$iRandom]['released_left']-1;
      $aReleasedLeft=myqui("UPDATE mytcg_card SET released_left={$iReleasedLeft} WHERE card_id={$iCardID}");
            
      //GIVE THE CARD TO THE USER
      $aCards=myqui("INSERT INTO mytcg_usercard (user_id, card_id, status) VALUES  ({$userID},{$iCardID},0)");
			
			if ($iQualityID > $iHighestQualityID) {
				$iHighestQualityID = $iQualityID;
				$iRetCardID = $iCardID;
			}
    }
		
		return $iRetCardID;
}

//ROLL DICE AND CHECK WHAT QUALITY CARD THE USER RECEIVES 
function randomQualityID($aQuality,$iPackCount){
  $iRandom = rand(1,$iPackCount);
  $interval=0;
  for($l=0; $l < sizeof($aQuality); $l++){
      $interval += $aQuality[$l]['bp'];
        if ($iRandom <= $interval){
          $iQualityID = $aQuality[$l]['quality_id'];
          break;
    }
  }
  return $iQualityID;
}

if ($iUserCardID = $_GET['createauction']){
	$iCardId=$_GET['cardid'];
  $iAuctionBid=$_GET['bid'];
  $iBuyNowPrice=$_GET['buynow'];
  $iDays=$_GET['days'];
  
	//Check if card still belongs to user and is available for trading
  $aCheckCard=myqu('SELECT max(usercard_id) usercard_id '
		.'FROM mytcg_usercard '
		.'WHERE usercardstatus_id = (select usercardstatus_id from mytcg_usercardstatus where description = "default")  '
		.'AND card_id = '.$iCardId.' '
		.'AND user_id = "'.$iUserID.'"');
	
  if (sizeof($aCheckCard) == 0){
    $sOP='<user>'.$sCRLF;
    $sOP.=$sTab.'<error>Card not available anymore.</error>'.$sCRLF;  
    $sOP.='</user>'.$sCRLF;
    header('xml_length: '.strlen($sOP));
    echo $sOP;
    exit;
  }
	else {
		$iUserCardID = $aCheckCard[0]['usercard_id'];
	}
	
  $aUpdate=myqui('UPDATE mytcg_usercard SET usercardstatus_id=(select usercardstatus_id from mytcg_usercardstatus where description = "auction") '
    .'WHERE usercard_id="'.$iUserCardID.'"');
  $aInsert=myqui('INSERT INTO mytcg_auctioncard '
    .'(usercard_id, opening_bid, buy_now_price, '
    .'datetime_start, datetime_end, expired) '
    .'VALUES ("'.$iUserCardID.'", "'.$iAuctionBid.'", '
    .'"'.$iBuyNowPrice.'", "'.date('Y-m-d H:i:s').'", '
    .'"'.date('Y-m-d H:i:s',time()+$iDays*24*60*60).'", "0")');
  echo $sTab.'<response>'.$sCRLF;
  echo $sTab.$sTab.'<success>1</success>'.$sCRLF;
  echo $sTab.'</response>'.$sCRLF;
  exit;
}

//BUY ITEMS IN CART
if ($_GET['buyproduct']){
  $timestamp = time();
  
  if (!($iHeight=$_GET['height'])) {
		$iHeight = '0';
  }
  if (!($iWidth=$_GET['width'])) {
		$iWidth = '0';
  }

  //GET PRODUCT DETAILS
  $aDetails=myqu('SELECT A.product_id, A.description, A.thumbnail_image_server_id, '
		.'A.price, lower(P.description) pack_type '
		.'FROM mytcg_product A '
		.'INNER JOIN mytcg_producttype P '
		.'ON A.producttype_id=P.producttype_id '
		.'WHERE A.product_id="'.$_GET['buyproduct'].'"');
  
  $iProductID = $aDetails[0]['product_id'];
  $iReleasedBuffer=1;
  //VALIDATE USER CREDITS
  //User credits
  $iCredits=myqu("SELECT credits FROM mytcg_user WHERE user_id='{$iUserID}'");
  $iCredits=$iCredits[0]['credits'];
  
  //Total order cost
  $itemCost = $aDetails[0]['price'];
  $bValid = ($iCredits >= $itemCost);
  
  if ($bValid)
  {
    //PAY FOR PRODUCT
    $iCreditsAfterPurchase = $iCredits - $itemCost;
    $aCreditsLeft=myqui("UPDATE mytcg_user SET credits={$iCreditsAfterPurchase} WHERE user_id='{$iUserID}'");
    
		$iCardID = 0;
    //RECEIVE ITEM
    if ($aDetails[0]['pack_type'] == "starter"){
      $iCardID = openStarter($iUserID,$iProductID);
    }
    elseif($aDetails[0]['pack_type'] == "booster"){
      $iCardID = openBooster($iUserID,$iProductID);
    }
		
		$aServers=myqu('SELECT a.imageserver_id, CONCAT(b.description, a.directory) as URL '
			.'FROM mytcg_imageserversize a, mytcg_imageserver b '
			.'WHERE a.imageserver_id = b.imageserver_id '
			.'AND height<="'.$iHeight.'" '
			.'AND width<="'.$iWidth.'" '
			.'ORDER BY height DESC '
			.'LIMIT 0,2');
		
    //GET BEST CARD FROM PURCHASE
    $aCardDetails=myqu("SELECT c.card_id,c.description,c.front_phone_imageserver_id, 
			c.back_phone_imageserver_id,cq.description quality_name,c.image 
			FROM mytcg_card c 
			INNER JOIN mytcg_cardquality AS cq 
			ON (c.cardquality_id = cq.cardquality_id) 
			WHERE c.card_id=".$iCardID);
		
		echo $sTab.'<card>'.$sCRLF;
		echo $sTab.$sTab.'<id>'.$aCardDetails[0]['card_id'].'</id>'.$sCRLF;
		echo $sTab.$sTab.'<image_id>'.$aCardDetails[0]['image'].'</image_id>'.$sCRLF;
		echo $sTab.$sTab.'<description>'.$aCardDetails[0]['description'].'</description>'.$sCRLF;
		echo $sTab.$sTab.'<quality>'.$aCardDetails[0]['quality_name'].'</quality>'.$sCRLF;
		
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aCardDetails[0]['front_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		echo $sTab.$sTab.'<urlfront>'.$sFound.'cards/'.$aCardDetails[0]['image'].'_front.jpg</urlfront>'.$sCRLF;
		
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aCardDetails[0]['back_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		echo $sTab.$sTab.'<urlback>'.$sFound.'cards/'.$aCardDetails[0]['image'].'_back.jpg</urlback>'.$sCRLF;
		echo $sTab.'</card>'.$sCRLF;
  } 
  exit;
}

//BID ON AN AUCTION
if ($_GET['auctionbid']){
	$bid = $_GET['bid'];
	$username = $_GET['username'];
	
  //SELECT USERS CURRENT CREDITS
	$query = "select credits from mytcg_user where user_id = ".$iUserID;
  $result = myqu($query);
  $credits = $result[0]['credits'];
  
	if ($credits >= $bid) {
		$auctionCardId = $_GET['auctioncardid'];
	
		//the previous high bidder needs to get their credits back
		$query = "SELECT max(credits) as price, user_id "
							."from mytcg_auctionbid "
							."where auctioncard_id = ".$auctionCardId." "
							."group by user_id";
		$result = myqu($query);
		
		if ($aBid=$result[0]) {
			//if there was a previous bid
			$prevBid = $aBid['price'];
			$prevUserId = $aBid['user_id'];
			
			$query = "update mytcg_user set credits = credits + ".$prevBid." where user_id = ".$prevUserId;
			myqu($query);
		}
		
		$query = "update mytcg_user set credits = credits - ".$bid." where user_id = ".$iUserID;
		myqu($query);
		
		$query = "INSERT INTO mytcg_auctionbid (auctioncard_id, user_id, credits, datetime_bid) VALUES (".$auctionCardId
			.", ".$iUserID.", ".$bid.", now())";
		myqu($query);
		
		echo $sTab.'<result>1</result>'.$sCRLF;
	}
	else {
		echo $sTab.'<result>0</result>'.$sCRLF;
	}
  exit;
}

//BUYOUT AN AUCTION
if ($_GET['buyauctionnow']){
	$buyNowPrice = $_GET['buynowprice'];
	$username = $_GET['username'];
	
  //SELECT USERS CURRENT CREDITS
	$query = "select credits from mytcg_user where user_id = ".$iUserID;
  $result = myqu($query);
  $credits = $result[0]['credits'];
  
	if ($credits >= $buyNowPrice) {
		$auctionCardId = $_GET['auctioncardid'];
		$userCardId = $_GET['usercardid'];
		
		//the previous high bidder needs to get their credits back
		$query = "SELECT max(credits) as price, user_id "
							."from mytcg_auctionbid "
							."where auctioncard_id = ".$auctionCardId." "
							."group by user_id";
		$result = myqu($query);
		
		if ($aBid=$result[0]) {
			//if there was a previous bid
			$prevBid = $aBid['price'];
			$prevUserId = $aBid['user_id'];
			
			$query = "update mytcg_user set credits = credits + ".$prevBid." where user_id = ".$prevUserId;
			myqu($query);
		}
		
		//set the auction to expired
		$query = "update mytcg_auctioncard set expired = '1' where auctioncard_id = ".$auctionCardId;
		myqu($query);
		
		//add the credits to the user who was auctioning the card
		$query = "update mytcg_user set credits = credits + ".$buyNowPrice." where user_id = (select user_id from mytcg_usercard where usercard_id = ".$userCardId.")";
		myqu($query);
		
		//set the cards status back to default
		$query = "update mytcg_usercard set usercardstatus_id = (select usercardstatus_id from mytcg_usercardstatus where description = 'default'), user_id = ".$iUserID." where usercard_id = ".$userCardId;
		myqu($query);
		
		//take the credits from the user buying out the auction
		$query = "update mytcg_user set credits = credits - ".$buyNowPrice." where user_id = ".$iUserID;
		myqu($query);
		
		echo $sTab.'<result>1</result>'.$sCRLF;
	}
	else {
		echo $sTab.'<result>0</result>'.$sCRLF;
	}
  exit;
}

//DO TRADE
if ($_GET['tradecard']){
  //Item sent to user
  $receiveNumber = $_REQUEST['detail'];
  //Item being sent
  $cardID = $_REQUEST['cardid'];
  
  //Check if card still belongs to user and is available for trading
  $aCheckCard=myqu('SELECT usercard_id,status FROM mytcg_usercard WHERE status = 1 AND card_id = '.$cardID);
  if (sizeof($aCheckCard) == 0){
    $sOP='<user>'.$sCRLF;
    $sOP.=$sTab.'<error>Card not available anymore.</error>'.$sCRLF;  
    $sOP.='</user>'.$sCRLF;
    header('xml_length: '.strlen($sOP));
    echo $sOP;
    exit;
  }
  
  //Check Number
  if ($receiveNumber=="Vendor"){
    $return = '<response>1</response>'.$sCRLF;
  } 
  elseif (!is_numeric($receiveNumber)){
    echo '<response>-2</response>'.$sCRLF;
    exit;
  }
  elseif (strlen($receiveNumber) != 10){
    echo '<response>-1</response>'.$sCRLF;
    exit;
  }
  
  //Check if user number is already in system. If not, create new msisdn user
  $aCheckUser=myqu('SELECT id FROM jos_users WHERE username = "'.$receiveNumber.'"');
  if (sizeof($aCheckUser) == 0){

    $salt = JUserHelper::genRandomPassword(32);
    $crypt = JUserHelper::getCryptedPassword("1337", $salt);
    $register_password_crypted = $crypt.':'.$salt;    
    //$activation_code = JUtility::getHash( JUserHelper::genRandomPassword());
    
    $query = 'INSERT INTO jos_users (name,username,password,usertype,block,sendEmail,gid,registerDate) VALUES (';
    $query .= '"'.$receiveNumber.'",';
    $query .= '"'.$receiveNumber.'",';
    $query .= '"'.$register_password_crypted.'",';
    $query .= '"Registered",';
    $query .= '0,';
    $query .= '0,';
    $query .= '18,';
    $query .= '"'.date('Y-m-d H:i:s').'")';
    $aQuery = myqu($query);
    $aQuery = myqu("SELECT MAX(id) AS id FROM jos_users");
    $receive_userID = $aQuery[0]['id'];
    
    $query = 'INSERT INTO mytcg_user (joomla_user_id,parent_joomla_user_id,credits) VALUES (';    
    $query .= $receive_userID.',99,';
    $query .= '0)';   
    $aQuery = myqu($query);
    
    $query = "INSERT INTO jos_vm_cart (user_id) VALUES ($receive_userID)";
    $aQuery = myqu($query);
    
    $query = 'INSERT INTO jos_core_acl_aro (section_value,value,name) VALUES (';
    $query .= '"users",';
    $query .= '"'.$receive_userID.'",';
    $query .= '"'.$userNumber.'")';
    $aQuery = myqu($query);
    $aQuery = myqu("SELECT MAX(id) AS id FROM jos_core_acl_aro");
    $aclID = $aQuery[0]['id'];
    
    $query = 'INSERT INTO jos_core_acl_groups_aro_map (group_id,section_value,aro_id) VALUES (';
    $query .= '18,';
    $query .= '"",';
    $query .= $aclID.')';
    $aQuery = myqu($query);
    $smsMessage = ", please download app at http://m.mytcg.net";
  }
  else{
    $receive_userID = $aCheckUser[0]['id'];
    $smsMessage = ", please download app at http://m.mytcg.net";
  }
  
  //Do transaction
  //GET USERCARD ID BASED ON CARD ID SENT - NEEDS TO CHANGE IN FUTURE
  $aQuery = myqu("SELECT UC.usercard_id AS id, C.description FROM mytcg_usercard UC INNER JOIN mytcg_card C ON (UC.card_id = C.card_id) WHERE UC.joomla_user_id = {$iUserID} AND UC.card_id = ".$cardID);
  $usercardID = $aQuery[0]['id'];
  $sVoucher = $aQuery[0]['description'];
  
  $sUpdateCard=myqu('UPDATE mytcg_usercard SET joomla_user_id = '.$receive_userID.', is_new = 1 WHERE usercard_id = '.$usercardID);
  
  //Create record of transaction
  $query = 'INSERT INTO mytcg_trade (send_user_id,receive_user_id,type,date_created,status) VALUES (';
  $query .= '"'.$iUserID.'",';
  $query .= '"'.$receive_userID.'",';
  $query .= '"1",';
  $query .= '"'.date('Y-m-d H:i:s').'",';
  $query .= '"1")';
  $aQuery = myqu($query);
  $aQuery = myqu("SELECT MAX(trade_id) AS id FROM mytcg_trade");
  $tradeID = $aQuery[0]['id'];
  
  //Create record of items sent
  $query = 'INSERT INTO mytcg_trade_senditems (trade_id,item_type,item_value) VALUES (';
  $query .= '"'.$tradeID.'",';
  $query .= '"1",';
  $query .= '"'.$usercardID.'")'; 
  $aQuery = myqu($query);
  
  //SMS Notification of Trade completed
  
  if ($_REQUEST['sms']=="Yes"){
  $sms_string = "http://api.clickatell.com/http/sendmsg?user=mytcg&password=m9y7t5c3g!&api_id=3263957";
  $sms_string .= "&to={$receiveNumber}";
  $sms_string .= "&text={$sUsername} has sent you a {$sVoucher} Card".$smsMessage;
  $ch = curl_init(str_replace(" ","%20",$sms_string));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $ret = curl_exec($ch);
  curl_close($ch);
  }
  if (strpos($ret,"ERR:")===false)
    $return = '<response>1</response>'.$sCRLF;    
  else
    $return = '<response>0</response>'.$sCRLF;
  
  if ($_POST['return']){
    header("Location:".$_POST['return']);
  }
  else
  {
    echo $return;
  } 
  exit; 
}

//this needs to get a list of all the cards in the category, 
//but only display partial info for the ones the user doesnt have
if ($iCategory=$_GET['cardsincategory']){

	if (!($iHeight=$_GET['heigth'])) {
		$iHeight = '0';
	}
	if (!($iWidth=$_GET['width'])) {
		$iWidth = '0';
	}
	
	$aServers=myqu('SELECT a.imageserver_id, CONCAT(b.description, a.directory) as URL '
		.'FROM mytcg_imageserversize a, mytcg_imageserver b '
		.'WHERE a.imageserver_id = b.imageserver_id '
		.'AND height<="'.$iHeight.'" '
		.'AND width<="'.$iWidth.'" '
		.'ORDER BY height DESC '
		.'LIMIT 0,2');
	$aCards=myqu('SELECT A.card_id, count(*) quantity, B.image, A.usercard_id, '
		.'B.description, B.thumbnail_phone_imageserver_id, B.front_phone_imageserver_id, B.back_phone_imageserver_id '
		.'FROM mytcg_card B '
		.'INNER JOIN mytcg_usercard A '
		.'ON A.card_id=B.card_id '
		.'INNER JOIN mytcg_usercardstatus C '
		.'ON C.usercardstatus_id=A.usercardstatus_id '
		.'WHERE A.user_id="'.$iUserID.'" '
		.'AND (B.category_id="'.$iCategory.'" OR B.category_id IN (SELECT category_child_id FROM mytcg_category_x WHERE category_parent_id = "'.$iCategory.'")) '
		.'AND C.description="default" '
		.'GROUP BY B.card_id '
		.'UNION '
		.'SELECT B.card_id, 0 as quantity, B.image, "0" as usercard_id, '
		.'B.description, B.thumbnail_phone_imageserver_id, B.front_phone_imageserver_id, B.back_phone_imageserver_id '
		.'FROM mytcg_card B '
		.'WHERE (B.category_id="'.$iCategory.'" OR B.category_id IN (SELECT category_child_id FROM mytcg_category_x WHERE category_parent_id = "'.$iCategory.'")) '
		.'AND B.card_id NOT IN (SELECT uc.card_id from mytcg_usercard uc, mytcg_usercardstatus ucs '
		.'	where uc.user_id = "'.$iUserID.'" and uc.usercardstatus_id = ucs.usercardstatus_id and ucs.description="default") '
		.'GROUP BY B.card_id '
		.'ORDER BY description');
	$sOP='<cardsincategory>'.$sCRLF;
	$iCount=0;
	while ($aOneCard=$aCards[$iCount]){
		$sOP.=$sTab.'<cardid>'.$aOneCard['card_id'].'</cardid>'.$sCRLF;		
		$sOP.=$sTab.'<description>'.$aOneCard['description'].'</description>'.$sCRLF;
		$sOP.=$sTab.'<quantity>'.$aOneCard['quantity'].'</quantity>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aOneCard['thumbnail_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<thumburl>'.$sFound.'cards/'.$aOneCard['image'].'_thumb.jpg</thumburl>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aOneCard['front_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<fronturl>'.$sFound.'cards/'.$aOneCard['image'].'_front.jpg</fronturl>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aOneCard['back_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<backurl>'.$sFound.'cards/'.$aOneCard['image'].'_back.jpg</backurl>'.$sCRLF;
		$iCount++;
	}
	$sOP.='</cardsincategory>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

// get list of cards on auction within a category
if ($_GET['categoryauction']){
	if (!($iHeight=$_GET['heigth'])) {
		$iHeight = '0';
	}
	if (!($iWidth=$_GET['width'])) {
		$iWidth = '0';
	}
	
	$aServers=myqu('SELECT a.imageserver_id, CONCAT(b.description, a.directory) as URL '
		.'FROM mytcg_imageserversize a, mytcg_imageserver b '
		.'WHERE a.imageserver_id = b.imageserver_id '
		.'AND height<="'.$iHeight.'" '
		.'AND width<="'.$iWidth.'" '
		.'ORDER BY height DESC '
		.'LIMIT 0,2'
	);
	
	$aAuctionCards=myqu('SELECT AC.auctioncard_id, UC.usercard_id, UC.card_id, C.image, C.thumbnail_phone_imageserver_id, C.description, '
		.'AC.opening_bid, AC.buy_now_price, MAX(AB.credits) price, U.username, UB.username as last_bid_username, date_format(AC.datetime_end, "%Y-%m-%d") as end_date '
		.'FROM mytcg_usercard UC '
		.'INNER JOIN mytcg_auctioncard AC '
		.'ON UC.usercard_id=AC.usercard_id '
		.'INNER JOIN mytcg_card C '
		.'ON UC.card_id=C.card_id '
		.'INNER JOIN mytcg_user U '
		.'ON UC.user_id=U.user_id '
		.'LEFT OUTER JOIN mytcg_auctionbid AB '
		.'ON AC.auctioncard_id=AB.auctioncard_id '
		.'LEFT OUTER JOIN mytcg_user UB '
		.'ON AB.user_id=UB.user_id '
		.'WHERE AC.expired="0" '
		.'AND (C.category_id='.$_GET['category_id'].' or C.category_id in (SELECT category_child_id FROM mytcg_category_x WHERE category_parent_id = '.$_GET['category_id'].')) '
		.'GROUP BY UC.usercard_id '
		.'ORDER BY C.description, price, AC.opening_bid');
	
	$sOP='<auctionsincategory>'.$sCRLF;
	$iCount=0;
	while ($aOneCard=$aAuctionCards[$iCount]){
		$sOP.=$sTab.'<auctioncardid>'.$aOneCard['auctioncard_id'].'</auctioncardid>'.$sCRLF;
		$sOP.=$sTab.'<usercardid>'.$aOneCard['usercard_id'].'</usercardid>'.$sCRLF;
		$sOP.=$sTab.'<cardid>'.$aOneCard['card_id'].'</cardid>'.$sCRLF;
		$sOP.=$sTab.'<description>'.$aOneCard['description'].'</description>'.$sCRLF;
		$sOP.=$sTab.'<openingbid>'.$aOneCard['opening_bid'].'</openingbid>'.$sCRLF;
		$sOP.=$sTab.'<buynowprice>'.$aOneCard['buy_now_price'].'</buynowprice>'.$sCRLF;
		$sOP.=$sTab.'<price>'.$aOneCard['price'].'</price>'.$sCRLF;
		$sOP.=$sTab.'<username>'.$aOneCard['username'].'</username>'.$sCRLF;
		$sOP.=$sTab.'<endDate>'.$aOneCard['end_date'].'</endDate>'.$sCRLF;
		$sOP.=$sTab.'<lastBidUser>'.$aOneCard['last_bid_username'].'</lastBidUser>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aOneCard['thumbnail_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<thumburl>'.$sFound.'cards/'.$aOneCard['image'].'_thumb.jpg</thumburl>'.$sCRLF;
		$iCount++;
	}
	$sOP.='</auctionsincategory>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

// get list of cards on auction for the current user
if ($_GET['userauction']){
	if (!($iHeight=$_GET['heigth'])) {
		$iHeight = '0';
	}
	if (!($iWidth=$_GET['width'])) {
		$iWidth = '0';
	}
	
	$aServers=myqu('SELECT a.imageserver_id, CONCAT(b.description, a.directory) as URL '
		.'FROM mytcg_imageserversize a, mytcg_imageserver b '
		.'WHERE a.imageserver_id = b.imageserver_id '
		.'AND height<="'.$iHeight.'" '
		.'AND width<="'.$iWidth.'" '
		.'ORDER BY height DESC '
		.'LIMIT 0,2'
	);
	
	$aAuctionCards=myqu('SELECT AC.auctioncard_id, UC.usercard_id, UC.card_id, C.image, C.thumbnail_phone_imageserver_id, C.description, '
		.'AC.opening_bid, AC.buy_now_price, MAX(AB.credits) price, U.username, UB.username as last_bid_username, date_format(AC.datetime_end, "%Y-%m-%d") as end_date '
		.'FROM mytcg_usercard UC '
		.'INNER JOIN mytcg_auctioncard AC '
		.'ON UC.usercard_id=AC.usercard_id '
		.'INNER JOIN mytcg_card C '
		.'ON UC.card_id=C.card_id '
		.'INNER JOIN mytcg_user U '
		.'ON UC.user_id=U.user_id '
		.'LEFT OUTER JOIN mytcg_auctionbid AB '
		.'ON AC.auctioncard_id=AB.auctioncard_id '
		.'LEFT OUTER JOIN mytcg_user UB '
		.'ON AB.user_id=UB.user_id '
		.'WHERE AC.expired="0" '
		.'AND U.user_id='.$iUserID.' '
		.'GROUP BY UC.usercard_id '
		.'ORDER BY C.description, price, AC.opening_bid');
	
	$sOP='<auctionsincategory>'.$sCRLF;
	$iCount=0;
	while ($aOneCard=$aAuctionCards[$iCount]){
		$sOP.=$sTab.'<auctioncardid>'.$aOneCard['auctioncard_id'].'</auctioncardid>'.$sCRLF;
		$sOP.=$sTab.'<usercardid>'.$aOneCard['usercard_id'].'</usercardid>'.$sCRLF;
		$sOP.=$sTab.'<cardid>'.$aOneCard['card_id'].'</cardid>'.$sCRLF;
		$sOP.=$sTab.'<description>'.$aOneCard['description'].'</description>'.$sCRLF;
		$sOP.=$sTab.'<openingbid>'.$aOneCard['opening_bid'].'</openingbid>'.$sCRLF;
		$sOP.=$sTab.'<buynowprice>'.$aOneCard['buy_now_price'].'</buynowprice>'.$sCRLF;
		$sOP.=$sTab.'<price>'.$aOneCard['price'].'</price>'.$sCRLF;
		$sOP.=$sTab.'<username>'.$aOneCard['username'].'</username>'.$sCRLF;
		$sOP.=$sTab.'<endDate>'.$aOneCard['end_date'].'</endDate>'.$sCRLF;
		$sOP.=$sTab.'<lastBidUser>'.$aOneCard['last_bid_username'].'</lastBidUser>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$aOneCard['thumbnail_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<thumburl>'.$sFound.'cards/'.$aOneCard['image'].'_thumb.jpg</thumburl>'.$sCRLF;
		$iCount++;
	}
	$sOP.='</auctionsincategory>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** give details on auctions */
if ($_GET['auction']){
	$aAuctionCards=myqu('SELECT B.auctioncard_id '
		.'FROM mytcg_usercard A '
		.'INNER JOIN mytcg_auctioncard B '
		.'ON A.usercard_id=B.usercard_id '
		.'WHERE A.is_on_auction="1" '
		.'AND A.joomla_user_id="'.$iUserID.'" '
		.'UNION SELECT DISTINCT auctioncard_id '
		.'FROM mytcg_auctionbid '
		.' WHERE joomla_user_id="'.$iUserID.'"'
	);
	$iCount=0;
	$aCards=array();
	while ($sAuctionCard=$aAuctionCards[$iCount]['auctioncard_id']){
		$aCards[$iCount]=$sAuctionCard;
		$iCount++;
	}
	$sCards=implode(',',$aCards);
	$aAuctionCards=myqu('SELECT A.datetime_start, A.datetime_end, A.opening_bid, '
		.'B.card_id, C.image_id, A.auctioncard_id '
		.'FROM mytcg_auctioncard A '
		.'INNER JOIN mytcg_usercard B '
		.'ON A.usercard_id=B.usercard_id '
		.'INNER JOIN mytcg_card C '
		.'ON B.card_id=C.card_id '
		.'WHERE A.auctioncard_id IN ('.$sCards.')'
	);
	$sOP='<auction>'.$sCRLF;
	$iCount=0;
	while ($aDetails=$aAuctionCards[$iCount]){
		$aBid=myqu('SELECT MAX(price)AS price, timestamp '
			.'FROM mytcg_auctionbid '
			.'WHERE auctioncard_id="'.$aCards[$iCount].'"');
			
		$sOP.=$sTab.'<auctioncard_'.$iCount.' val="'.$aDetails['auctioncard_id'].'">'.$sCRLF;
		$sOP.=$sTab.$sTab.'<card_id="'.$aDetails['card_id'].'" />'.$sCRLF;
		$sOP.=$sTab.$sTab.'<image_id="'.$aDetails['image_id'].'" />'.$sCRLF;
		$sOP.=$sTab.$sTab.'<datetime_start="'.$aDetails['datetime_start'].'" />'.$sCRLF;
		$sOP.=$sTab.$sTab.'<datetime_end="'.$aDetails['datetime_end'].'" />'.$sCRLF;
		$sOP.=$sTab.$sTab.'<opening_bid="'.$aDetails['opening_bid'].'" />'.$sCRLF;
		$sOP.=$sTab.$sTab.'<price="'.$aBid[0]['price'].'" />'.$sCRLF;
		$sOP.=$sTab.$sTab.'<currentbiddatetime="'.$aBid[0]['timestamp'].'" />'.$sCRLF;
		
		$sOP.=$sTab.'</auctioncard_'.$iCount.'>'.$sCRLF;
		
		$iCount++;
	}
	$sOP.='</auction>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** returns all the categories the user has enough cards to play with */
if ($_GET['playablecategories']){
	$aCategories=myqu('SELECT sum(card_count) as playable_cards, ca.category_id, ca.category_name '
		.'from (SELECT (case when count(uc.card_id) > 4 then 4 else count(uc.card_id) end) as card_count, uc.card_id, c.description, c.category_id '
		.'from mytcg_usercard uc INNER JOIN mytcg_user U '
		.'ON uc.user_id=U.user_id, mytcg_card c '
		.'where U.user_id='.$iUserID.' '
		.'and uc.card_id = c.card_id '
		.'group by uc.card_id '
		.'order by c.category_id) as cards, mytcg_category ca '
		.'where ca.category_id = cards.category_id '
		.'group by ca.category_id '
		);
	$sOP='<categories>'.$sCRLF;
	$iCount=0;
	while ($aCategory=$aCategories[$iCount]){
		if ($aCategory['playable_cards'] >= 10) {
			$sOP.=$sTab.'<categoryid>'.trim($aCategory['category_id']).'</categoryid>'.$sCRLF;
			$sOP.=$sTab.'<categoryname>'.trim($aCategory['category_name']).'</categoryname>'.$sCRLF;
			$sOP.=$sTab.'<playablecards>'.trim($aCategory['playable_cards']).'</playablecards>'.$sCRLF;
		}
		$iCount++;
	}	
	$sOP.='</categories>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** give user categories in use */
if ($_GET['usercategories']){
	$aCategories=myqu('SELECT C.category_id, C.description 
		FROM mytcg_category C, mytcg_category_x X 
		WHERE C.CATEGORY_ID = X.CATEGORY_CHILD_ID 
		AND X.CATEGORY_PARENT_ID is null 
		AND C.is_deleted is null 
		AND (C.category_id in 
			(SELECT c.category_id FROM mytcg_usercard uc
			INNER JOIN mytcg_card c
			ON c.card_id = uc.card_id
			INNER JOIN mytcg_usercardstatus ucs
			ON ucs.usercardstatus_id = uc.usercardstatus_id
			WHERE uc.user_id = '.$iUserID.' 
			AND ucs.description = "default")
			OR (C.category_id in 
			(SELECT cx.category_parent_id FROM mytcg_usercard uc
			INNER JOIN mytcg_card c
			ON c.card_id = uc.card_id
			INNER JOIN mytcg_category_x cx
			ON cx.category_child_id = c.category_id
			INNER JOIN mytcg_usercardstatus ucs
			ON ucs.usercardstatus_id = uc.usercardstatus_id
			WHERE uc.user_id = '.$iUserID.' 
			AND ucs.description = "default")))
		ORDER BY C.description');
	$sOP='<usercategories>'.$sCRLF;
	$iCount=0;
	while ($aCategory=$aCategories[$iCount]){
		$sOP.=$sTab.'<albumid>'.trim($aCategory['category_id']).'</albumid>'.$sCRLF;
		$sOP.=$sTab.'<albumname>'.trim($aCategory['description']).'</albumname>'.$sCRLF;
		$iCount++;
	}
	$sOP.='</usercategories>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** return a list of all categories */
if ($_GET['allcategories']) {
	$aCategories=myqu('SELECT C.category_id, C.description '
			.'FROM mytcg_category C, mytcg_category_x X '
			.'WHERE C.CATEGORY_ID = X.CATEGORY_CHILD_ID '
			.'AND X.CATEGORY_PARENT_ID is null '
			.'AND C.is_deleted is null '
			.'ORDER BY C.description'
		);
	$sOP='<cardcategories>'.$sCRLF;
	$iCount=0;
	while ($aCategory=$aCategories[$iCount]){
		$sOP.=$sTab.'<albumid>'.trim($aCategory['category_id']).'</albumid>'.$sCRLF;
		$sOP.=$sTab.'<albumname>'.trim($aCategory['description']).'</albumname>'.$sCRLF;
		$iCount++;
	}	
	$sOP.='</cardcategories>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** return a list of products in a category */
if ($_GET['categoryproducts']){
	$iCategoryId= $_REQUEST['categoryId'];

	$aServers=myqu('SELECT VALUE '
		.'FROM mytcg_system '
		.'WHERE category="shop_image_server" '
		);
	
	$aProducts=myqu('SELECT DISTINCT P.PRODUCT_ID, P.DESCRIPTION, M.DESCRIPTION PACK_TYPE, '
		.'P.PRICE, CONCAT(I.DESCRIPTION , "products/" , P.IMAGE , "_thumb.png") IMAGEURL, '
		.'P.NO_OF_CARDS, (CASE WHEN SUM(P.IN_STOCK) IS NULL THEN 0 ELSE SUM(P.IN_STOCK) END) AS IN_STOCK '
		.'FROM mytcg_category C, mytcg_imageserver I, '
		.'mytcg_productcategory_x PC, '
		.'mytcg_producttype M, mytcg_product P '
		.'WHERE PC.CATEGORY_ID = C.CATEGORY_ID '
		.'AND P.PRODUCT_ID = PC.PRODUCT_ID '
		.'AND M.producttype_id = P.producttype_id '
		.'AND I.IMAGESERVER_ID = P.THUMBNAIL_IMAGE_SERVER_ID '
		.'AND C.CATEGORY_ID = "'.$iCategoryId.'" '
		.'GROUP BY P.PRODUCT_ID '
		.'ORDER BY P.DESCRIPTION');
	
	$sFound='';
	if ($aServer=$aServers[0]){
		$sFound = trim($aServer['VALUE']);
	}
	
	$sOP='<categoryproducts>'.$sCRLF;
	$iCount=0;
	while ($aProduct=$aProducts[$iCount]){
		if ($aProduct['IN_STOCK'] > 0) {
			$sOP.=$sTab.'<productid>'.trim($aProduct['PRODUCT_ID']).'</productid>'.$sCRLF;
			$sOP.=$sTab.'<productname>'.trim($aProduct['DESCRIPTION']).'</productname>'.$sCRLF;
			$sOP.=$sTab.'<producttype>'.trim($aProduct['PACK_TYPE']).'</producttype>'.$sCRLF;
			$sOP.=$sTab.'<productprice>'.trim($aProduct['PRICE']).'</productprice>'.$sCRLF;
			$sOP.=$sTab.'<productnumcards>'.trim($aProduct['NO_OF_CARDS']).'</productnumcards>'.$sCRLF;
			$sOP.=$sTab.'<productthumb>'.trim($aProduct['IMAGEURL']).'</productthumb>'.$sCRLF;
		}
		$iCount++;
	}	
	$sOP.='</categoryproducts>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}


/** give user details */
if ($_GET['userdetails']){
	global $iUserID;
	$aUserDetails=myqu('SELECT username, email_address, name, credits '
		.'FROM mytcg_user '
		.'WHERE user_id="'.$iUserID.'"');
	$sOP='<userdetails>'.$sCRLF;
	$sOP.=$sTab.'<username>'.trim($aUserDetails[0]['username']).'</username>'.$sCRLF;	
	$sOP.=$sTab.'<email>'.trim($aUserDetails[0]['email_address']).'</email>'.$sCRLF;
	$sOP.=$sTab.'<name>'.trim($aUserDetails[0]['name']).'</name>'.$sCRLF;
	$sOP.=$sTab.'<credits>'.trim($aUserDetails[0]['credits']).'</credits>'.$sCRLF;
	$sOP.=$sTab.'<status></status>'.$sCRLF;
	$sOP.='</userdetails>';
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** 
	SOME JOOMLA
	the JUserHelper class copied from libraries/joomla/user/helper.php
	stripped al unused functions
*/


class JUserHelper
{
	function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
	{
		// Get the salt to use.
		$salt = JUserHelper::getSalt($encryption, $salt, $plaintext);

		// Encrypt the password.
		switch ($encryption)
		{
			case 'plain' :
				return $plaintext;

			case 'sha' :
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));
				return ($show_encrypt) ? '{SHA}'.$encrypted : $encrypted;

			case 'crypt' :
			case 'crypt-des' :
			case 'crypt-md5' :
			case 'crypt-blowfish' :
				return ($show_encrypt ? '{crypt}' : '').crypt($plaintext, $salt);

			case 'md5-base64' :
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));
				return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;

			case 'ssha' :
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext.$salt).$salt);
				return ($show_encrypt) ? '{SSHA}'.$encrypted : $encrypted;

			case 'smd5' :
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext.$salt).$salt);
				return ($show_encrypt) ? '{SMD5}'.$encrypted : $encrypted;

			case 'aprmd5' :
				$length = strlen($plaintext);
				$context = $plaintext.'$apr1$'.$salt;
				$binary = JUserHelper::_bin(md5($plaintext.$salt.$plaintext));

				for ($i = $length; $i > 0; $i -= 16) {
					$context .= substr($binary, 0, ($i > 16 ? 16 : $i));
				}
				for ($i = $length; $i > 0; $i >>= 1) {
					$context .= ($i & 1) ? chr(0) : $plaintext[0];
				}

				$binary = JUserHelper::_bin(md5($context));

				for ($i = 0; $i < 1000; $i ++) {
					$new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
					if ($i % 3) {
						$new .= $salt;
					}
					if ($i % 7) {
						$new .= $plaintext;
					}
					$new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
					$binary = JUserHelper::_bin(md5($new));
				}

				$p = array ();
				for ($i = 0; $i < 5; $i ++) {
					$k = $i +6;
					$j = $i +12;
					if ($j == 16) {
						$j = 5;
					}
					$p[] = JUserHelper::_toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
				}

				return '$apr1$'.$salt.'$'.implode('', $p).JUserHelper::_toAPRMD5(ord($binary[11]), 3);

			case 'md5-hex' :
			default :
				$encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
				return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;
		}
	}


	function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
	{
		// Encrypt the password.
		switch ($encryption)
		{
			case 'crypt' :
			case 'crypt-des' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
				} else {
					return substr(md5(mt_rand()), 0, 2);
				}
				break;

			case 'crypt-md5' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
				} else {
					return '$1$'.substr(md5(mt_rand()), 0, 8).'$';
				}
				break;

			case 'crypt-blowfish' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
				} else {
					return '$2$'.substr(md5(mt_rand()), 0, 12).'$';
				}
				break;

			case 'ssha' :
				if ($seed) {
					return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
				} else {
					return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'smd5' :
				if ($seed) {
					return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
				} else {
					return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'aprmd5' :
				/* 64 characters that are valid for APRMD5 passwords. */
				$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

				if ($seed) {
					return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
				} else {
					$salt = '';
					for ($i = 0; $i < 8; $i ++) {
						$salt .= $APRMD5 {
							rand(0, 63)
							};
					}
					return $salt;
				}
				break;

			default :
				$salt = '';
				if ($seed) {
					$salt = $seed;
				}
				return $salt;
				break;
		}
	}

	function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		$stat = @stat(__FILE__);
		if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i ++) {
			$makepass .= $salt[mt_rand(0, $len -1)];
		}

		return $makepass;
	}

}/** end JUserHelper Class */


function myqu($sQuery){
	$sMysqlConnectString='dedi94.flk1.host-h.net,mytcg_root,g4m3c4rd98,gamecard';
	$aFileHandle=fopen('/usr/home/mytcga/sqlq.log','a+');
//	$sMysqlConnectString='localhost,root,i1m2p#i$(),gamecard';
//	$aFileHandle=fopen('/usr/local/www/mytcg/sqlq.log','a+');
	/** truncate long queries */
	$sQueryCut=substr($sQuery,0,1024);
	fwrite($aFileHandle,date('H:i:s',time()).' '.$_SERVER['REMOTE_ADDR']
		.' '.$sQueryCut."\n");
	$aString=explode(',',$sMysqlConnectString);
	$aLink=mysqli_connect($aString[0],$aString[1],$aString[2],$aString[3]);
	$aResult=@mysqli_query($aLink, $sQuery);
	if (mysqli_error($aLink)){
		fwrite($aFileHandle,mysqli_error($aLink)."\n");
	}
	fclose($aFileHandle);
	$aOutput=array();
	$f=0;
	while ($aRow=@mysqli_fetch_array($aResult,MYSQL_BOTH)){
		$aOutput[$f]=$aRow;
		++$f;
	}
	@mysqli_free_result($aResult);
	mysqli_close($aLink);
	return $aOutput;
}
function myqui($sQuery){
  $sMysqlConnectString='dedi94.flk1.host-h.net,mytcg_root,g4m3c4rd98,gamecard';
  $aFileHandle=fopen('/usr/home/mytcga/sqlq.log','a+');
  $sQueryCut=substr($sQuery,0,1024);
  fwrite($aFileHandle,date('H:i:s',time()).' '.$_SERVER['REMOTE_ADDR']
    .' '.$sQueryCut."\n");
  
  $aString=explode(',',$sMysqlConnectString);
  $aLink=mysqli_connect($aString[0],$aString[1],$aString[2],$aString[3]);
  $aResult=@mysqli_query($aLink, $sQuery);
  if (mysqli_error($aLink)){
    fwrite($aFileHandle,mysqli_error($aLink)."\n");
  }
  fclose($aFileHandle);
  @mysqli_free_result($aResult);
  mysqli_close($aLink);
}
?>
