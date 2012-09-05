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
    $iPackCount = myqu("SELECT no_of_cards FROM mytcg_product WHERE product_id={$packID}");
    $iPackCount = $iPackCount[0]['no_of_cards'];
    
    $aQuality = myqu("SELECT cardquality_id,((booster_probability)*{$iPackCount}) AS bp FROM mytcg_cardquality ORDER BY booster_probability ASC");
    $iQualityID = 0;
		$rarity = -1;
		$iRetCardID = 0;
    
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
      
      //REMOVE THE CARD FROM THE STACK
      //$iReleasedLeft=$aGetCards[$iRandom]['released_left']-1;
      //$aReleasedLeft=myqui("UPDATE mytcg_card SET released_left={$iReleasedLeft} WHERE card_id={$iCardID}");
            
      //GIVE THE CARD TO THE USER
      $aCards=myqui("INSERT mytcg_usercard (user_id, card_id, usercardstatus_id)
				SELECT {$userID}, {$iCardID}, usercardstatus_id
				FROM mytcg_usercardstatus
				WHERE description = 'default'");
			
			if ($aGetCards[$iRandom]['booster_probability'] < $rarity || $rarity == -1) {
				$rarity = $aGetCards[$iRandom]['booster_probability'];
				$iRetCardID = $aGetCards[$iRandom]['card_id'];
			}
    }
		
		//we can remove one of the products from stock though
		myqui("UPDATE mytcg_product SET in_stock=in_stock-1 WHERE product_id={$packID}");
		
		return $iRetCardID;
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
  $aDetails=myqu('SELECT A.product_id, A.description, '
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
	$aCategories=myqu('SELECT c.category_id, (case when count(uc.card_id) > 4 then 4 else count(uc.card_id) end) as card_count, cx.category_parent_id
		FROM mytcg_card c
		INNER JOIN mytcg_usercard uc
		ON uc.card_id = c.card_id
		INNER JOIN mytcg_category_x cx
		ON cx.category_child_id = c.category_id
		WHERE uc.user_id = '.$iUserID.' 
		GROUP BY c.category_id');
	
	$results = array();
	$iCount=0;
	while ($aCategory=$aCategories[$iCount]) {
		if ($aCategory['category_parent_id'] == '') {
			if ($bCategory=$results[$aCategory['category_id']]) {
				$bCategory['card_count'] = $bCategory['card_count'] + $aCategory['card_count'];
				$results[$aCategory['category_id']] = $bCategory;
			}
			else {
				$results[$aCategory['category_id']] = $aCategory;
			}
		}
		else {
			$results = getSecondLastParent($aCategory['category_parent_id'],$aCategory['category_id'], $aCategory, $results);
		}
		$iCount++;
	}
	
	$sOP='<categories>'.$sCRLF;
	foreach ($results as $category) {
    if ($category['card_count'] >= 5) {
			$catName=myqu('SELECT description FROM mytcg_category WHERE category_id = '.$category['category_id']);
			$sOP.=$sTab.'<categoryid>'.trim($category['category_id']).'</categoryid>'.$sCRLF;
			$sOP.=$sTab.'<categoryname>'.trim($catName[0]['description']).'</categoryname>'.$sCRLF;
			$sOP.=$sTab.'<playablecards>'.trim($category['card_count']).'</playablecards>'.$sCRLF;
		}
	}
	$sOP.='</categories>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

//recurring method used to get the parents of a category and add them to a results array
function getSecondLastParent($categoryParentId,$categoryChildId,$category,$results){
	$aCategory=myqu('SELECT cx.category_child_id as category_id, cx.category_parent_id
		FROM mytcg_category_x cx
		WHERE cx.category_child_id = '.$categoryParentId);
	if ($aCategory[0]['category_parent_id'] == '') {
		if ($bCategory=$results[$categoryChildId]) {
			$bCategory['card_count'] = $bCategory['card_count'] + $category['card_count'];
			$results[$categoryChildId] = $bCategory;
		}
		else {
			$category['category_id'] = $categoryChildId;
			$results[$categoryChildId] = $category;
		}
	}
	else {
		$results = getSecondLastParent($aCategory[0]['category_parent_id'], $aCategory[0]['category_id'], $category, $results);
	}
	return $results;
}

/** load the phase of the game, then send an unplayed cards list, a selected cards stats, or results accordingly */
if ($_GET['loadgame']) {
	$gameId = $_GET['gameid'];

	//sizes first
	if (!($iHeight=$_GET['heigth'])) {
		$iHeight = '0';
	}
	if (!($iWidth=$_GET['width'])) {
		$iWidth = '0';
	}
	
	//get the game phase
	$gamePhaseQuery = myqu('SELECT g.gamephase_id, lower(gp.description) as description
		FROM mytcg_game g
		INNER JOIN mytcg_gamephase gp
		ON g.gamephase_id = gp.gamephase_id
		WHERE g.game_id = '.$gameId);
		
	$gamePhase = $gamePhaseQuery[0]['description'];
	
	//we will always return the phase
	$sOP='<game>'.$sCRLF;
	$sOP.='<phase>'.$sCRLF;
	$sOP.=$gamePhase.$sCRLF;
	$sOP.='</phase>'.$sCRLF;
	
	//depending on the phase, we build different xml
	if ($gamePhase == 'card') {
		//this means the users must select cards. So we need to return xml of the unselected cards
		$sOP.=getUnplayedCards($iUserID, $gameId, $iHeight, $iWidth);
	}
	else if ($gamePhase == 'stat') {
		//for this phase we need to return a list of the stats for the users selected phase
	}
	else if ($gamePhase == 'result') {
		//results will say whether the user won or lost the round, as well as the current score
	}
	
	$sOP.='</game>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

//get a list of unplayed cards for a game for a user
function getUnplayedCards($iUserID, $gameId, $iHeight, $iWidth) {
	//get the image server stuff
	$aServers=myqu('SELECT a.imageserver_id, CONCAT(b.description, a.directory) as URL '
		.'FROM mytcg_imageserversize a, mytcg_imageserver b '
		.'WHERE a.imageserver_id = b.imageserver_id '
		.'AND height<="'.$iHeight.'" '
		.'AND width<="'.$iWidth.'" '
		.'ORDER BY height DESC '
		.'LIMIT 0,2'
	);
	
	//select card details of users deck
	$userCardDetails = myqu('SELECT c.card_id, gameplayercard_id,
		c.back_phone_imageserver_id, c.front_phone_imageserver_id,
		c.thumbnail_phone_imageserver_id, c.image, c.description
		FROM mytcg_gameplayercard gpc
		INNER JOIN mytcg_usercard uc
		ON gpc.usercard_id = uc.usercard_id
		INNER JOIN mytcg_card c
		ON c.card_id = uc.card_id
		INNER JOIN mytcg_gameplayer gp
		ON gpc.gameplayer_id = gp.gameplayer_id
		WHERE gp.user_id = '.$iUserID.'
		AND gp.game_id = '.$gameId.' 
		ORDER BY c.description');
	
	//build xml of the user's deck and send it back
	$sOP='<cards>'.$sCRLF;
	$iCount=0;
	while ($oneCard=$userCardDetails[$iCount]){
		$sOP.=$sTab.'<cardid>'.$oneCard['card_id'].'</cardid>'.$sCRLF;
		$sOP.=$sTab.'<gameplayercard_id>'.$oneCard['gameplayercard_id'].'</gameplayercard_id>'.$sCRLF;		
		$sOP.=$sTab.'<description>'.$oneCard['description'].'</description>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$oneCard['thumbnail_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<thumburl>'.$sFound.'cards/'.$oneCard['image'].'_thumb.jpg</thumburl>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$oneCard['front_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<fronturl>'.$sFound.'cards/'.$oneCard['image'].'_front.jpg</fronturl>'.$sCRLF;
		$sFound='';
		$iCountServer=0;
		while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
			if ($aOneServer['imageserver_id']==$oneCard['back_phone_imageserver_id']){
				$sFound=$aOneServer['URL'];
			} else {
				$iCountServer++;
			}
		}
		$sOP.=$sTab.'<backurl>'.$sFound.'cards/'.$oneCard['image'].'_back.jpg</backurl>'.$sCRLF;
		$iCount++;
	}
	$sOP.='</cards>'.$sCRLF;
	return $sOP;
}

/** creates a new game, against AI, and returns the gameId */
if ($_GET['newgame']) {
	//we will use the admin as the ai user
	$categoryId = $_GET['categoryid'];
	
	//create the game, get the game_id
	$gameIdQuery = myqu('SELECT (CASE WHEN MAX(game_id) IS NULL THEN 0 ELSE MAX(game_id) END) + 1 AS game_id
		FROM mytcg_game');
	$gameId = $gameIdQuery[0]['game_id'];
	myqu('INSERT INTO mytcg_game (game_id, gamestatus_id, gamephase_id, category_id, date_start) 
		SELECT '.$gameId.', (SELECT gamestatus_id FROM mytcg_gamestatus WHERE lower(description) = "incomplete"),
		(SELECT gamephase_id FROM mytcg_gamephase WHERE lower(description) = "card"), '.$categoryId.', now()
		FROM DUAL');
	
	//get the admins userId
	$adminUserIdQuery = myqu('SELECT user_id 
		FROM mytcg_user 
		WHERE username = "admin"');
	$adminUserId = $adminUserIdQuery[0]['user_id'];
	
	//add the players to the game, admin is the ai, the user will go first(active = 1)
	myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, is_active, score)
		VALUES ('.$gameId.', '.$iUserID.', 1, 0)');
	myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, is_active, score)
		VALUES ('.$gameId.', '.$adminUserId.', 0, 0)');
	
	//we need to get both players gameplayer_id
	$userPlayerIdQuery = myqu('SELECT gameplayer_id 
		FROM mytcg_gameplayer 
		WHERE user_id = '.$iUserID
		.' AND game_id = '.$gameId);
	$userPlayerId = $userPlayerIdQuery[0]['gameplayer_id'];
	$adminPlayerId = myqu('SELECT gameplayer_id 
		FROM mytcg_gameplayer 
		WHERE user_id = '.$adminUserId
		.' AND game_id = '.$gameId);
	$adminPlayerId = $adminPlayerId[0]['gameplayer_id'];
	
	//create random deck for both players from their available cards.
	//first we will need a list of cards for each player in the category.
	$userCards = array();
	$adminCards = array();
	
	//this will require some recursion, as the category given is the second highest level,
	// and the cards are an unknown amount of subcategories deep.
	$userCards = getAllUserCatCards($iUserID, $categoryId, $userCards);
	$adminCards = getAllUserCatCards($adminUserId, $categoryId, $adminCards);
	
	//for now we will set the biggest possible deck size to twenty, and make the size a multiple of 5.
	$deckSize = sizeof($userCards) > sizeof($adminCards)? sizeof($adminCards):sizeof($userCards);
	$deckSize = $deckSize - ($deckSize % 5);
	$deckSize = $deckSize > 20?20:$deckSize;
	
	//for now we will use a random selection of the cards
	//maybe later we will base it on rareity or use another method
	$userKeys = array_rand($userCards, $deckSize);
	$adminKeys = array_rand($adminCards, $deckSize);
	
	//insert created decks into player cards, all statuses unplayed
	for ($i = 0; $i < $deckSize; $i++) {
		myqu('INSERT INTO mytcg_gameplayercard 
			(gameplayer_id, usercard_id, gameplayercardstatus_id) 
			SELECT '.$userPlayerId.', '.$userCards[$userKeys[$i]]['usercard_id'].', gameplayercardstatus_id 
			FROM mytcg_gameplayercardstatus 
			WHERE lower(description) = "unused"');
		myqu('INSERT INTO mytcg_gameplayercard 
			(gameplayer_id, usercard_id, gameplayercardstatus_id) 
			SELECT '.$adminPlayerId.', '.$adminCards[$adminKeys[$i]]['usercard_id'].', gameplayercardstatus_id 
			FROM mytcg_gameplayercardstatus 
			WHERE lower(description) = "unused"');
	}
	
	//select card details of users deck
	$userCardDetails = myqu('SELECT c.card_id, gameplayercard_id,
		c.back_phone_imageserver_id, c.front_phone_imageserver_id,
		c.thumbnail_phone_imageserver_id, c.image, c.description
		FROM mytcg_gameplayercard gpc
		INNER JOIN mytcg_usercard uc
		ON gpc.usercard_id = uc.usercard_id
		INNER JOIN mytcg_card c
		ON c.card_id = uc.card_id
		WHERE gpc.gameplayers_id = '.$userPlayerId.' 
		ORDER BY c.description');
	
	//return xml with the gameId to the phone
	$sOP='<game>'.$sCRLF;
	$sOP.=$sTab.'<gameid>'.$gameId.'</gameid>'.$sCRLF;
	$sOP.='</game>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	
	exit;
}

//recurring method used to get the cards in a category and all its children
function getAllUserCatCards($userId,$categoryId,$results){
	//first get all the cards in the current category and add them to the list
	$cards = myqu('SELECT c.card_id, uc.usercard_id
		FROM mytcg_usercard uc
		INNER JOIN mytcg_card c
		ON uc.card_id = c.card_id
		INNER JOIN mytcg_usercardstatus ucs
		ON ucs.usercardstatus_id = uc.usercardstatus_id
		WHERE c.category_id = '.$categoryId.'
		AND uc.user_id = '.$userId.' 
		AND lower(ucs.description) = "default"');
	
	$count = 0;
	while ($card=$cards[$count]) {
		$results[sizeof($results)] = $card;
		$count++;
	}
	
	//then select the children categories
	$categories = myqu('SELECT cx.category_child_id
		FROM mytcg_category_x cx
		WHERE cx.category_parent_id = '.$categoryId);
		
	$count = 0;
	while ($category=$categories[$count]) {
		//and repeat for each one
		$results = getAllUserCatCards($userId, $category['category_child_id'], $results);
		$count++;
	}
	
	return $results;
}

/** list incomplete games for the user */
if ($_GET['getusergames']){
	$aCategories=myqu('SELECT concat(c.description, DATE_FORMAT(g.date_start, "\n%Y-%m-%d %H:%i")) description, g.game_id
		FROM mytcg_game g
		INNER JOIN mytcg_category c
		ON c.category_id = g.category_id
		INNER JOIN mytcg_gameplayer gp
		ON g.game_id = gp.game_id
		INNER JOIN mytcg_gamestatus gs
		ON gs.gamestatus_id = g.gamestatus_id
		WHERE gp.user_id = '.$iUserID.' 
		AND lower(gs.description) = "incomplete"
		ORDER BY g.game_id');
	$sOP='<games>'.$sCRLF;
	$iCount=0;
	while ($aCategory=$aCategories[$iCount]){
		$sOP.=$sTab.'<gameid>'.trim($aCategory['game_id']).'</gameid>'.$sCRLF;
		$sOP.=$sTab.'<gamedescription>'.trim($aCategory['description']).'</gamedescription>'.$sCRLF;
		$iCount++;
	}
	$sOP.='</games>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** get the details of a specific game, relevant to the user */
if ($_GET['viewgamedetails']){
	$gameId = $_GET['gameid'];
	$gameDetails=myqu('SELECT DISTINCT (CASE WHEN gp.is_active = "0" THEN "AI" ELSE "You" END) turn, gp.score playerScore, 
		gpOpp.score OppScore, gp.gameplayer_id
		FROM mytcg_game g
		INNER JOIN mytcg_gameplayer gp
		ON g.game_id = gp.game_id
		INNER JOIN mytcg_gameplayer gpOpp
		ON g.game_id = gpOpp.game_id
		WHERE g.game_id = '.$gameId.' 
		AND gp.user_id = '.$iUserID.'
		AND gpOpp.user_id != '.$iUserID);
	$gameDetailsResults = $gameDetails[0];
	$gamePlayerId = $gameDetailsResults['gameplayer_id'];
	$sOP='<gamedetails>'.$sCRLF;
	$sOP.=$sTab.'<turn>'.trim($gameDetailsResults['turn']).'</turn>'.$sCRLF;
	$sOP.=$sTab.'<playerscore>'.trim($gameDetailsResults['playerScore']).'</playerscore>'.$sCRLF;
	$sOP.=$sTab.'<opponentscore>'.trim($gameDetailsResults['OppScore']).'</opponentscore>'.$sCRLF;
	$gamePlayerCardStatuses=myqu('SELECT gpcs.description 
		FROM mytcg_gameplayercard gpc
		INNER JOIN mytcg_gameplayercardstatus gpcs
		ON gpc.gameplayercardstatus_id = gpcs.gameplayercardstatus_id
		WHERE gpc.gameplayer_id = '.$gamePlayerId);
	$iCount=0;
	$usedCount=0;
	while ($cardStatus=$gamePlayerCardStatuses[$iCount]){
		if ($cardStatus['description'] == 'used') {
			$usedCount++;
		}
		$iCount++;
	}
	$sOP.=$sTab.'<progress>'.($usedCount/$iCount * 100).'</progress>'.$sCRLF;
	$sOP.='</gamedetails>'.$sCRLF;
	header('xml_length: '.strlen($sOP));
	echo $sOP;
	exit;
}

/** give user categories in use */
if ($_GET['usercategories']){
	$aCategories=myqu('SELECT DISTINCT ca.category_id, ca.description
		FROM mytcg_usercard uc
		INNER JOIN mytcg_card c
		ON c.card_id = uc.card_id
		INNER JOIN mytcg_category ca
		ON c.category_id = ca.category_id
		INNER JOIN mytcg_usercardstatus ucs
		ON ucs.usercardstatus_id = uc.usercardstatus_id
		WHERE uc.user_id = '.$iUserID.'
		AND ucs.description = LOWER("default")
		ORDER BY ca.description');
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
		.'AND I.IMAGESERVER_ID = P.THUMBNAIL_IMAGESERVER_ID '
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
