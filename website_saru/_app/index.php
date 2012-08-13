<?php
require_once "../func.php";

$sCRLF="\r\n";
$sTab=chr(9);

//SETUP PREFIX FOR TABLES
$pre = $Conf["database"]["table_prefix"];

// user activates account
if ($sEmail=$_GET['forget']){
  $aUser=myqu('SELECT * FROM mytcg_user WHERE email_address = "'.$sEmail.'"');
  $iUsercheck = sizeof($aUser);
  if ($iUsercheck==1){
     if($aUser[0]['is_active']=="1"){
       
       //RANDOM GENERATED PASSWORD
       $password=substr(time()*rand(5,25), -6);
       $iUserID=intval($aUser[0]["user_id"]);
       $iMod=($iUserID % 10)+1;
       $sSalt=substr(md5($iUserID),$iMod,10);
       $sPassword = $sSalt.md5($password);
       
       $a = myqu("UPDATE ".$pre."_user SET password = '".$sPassword."' WHERE user_id = ".$iUserID);
       
       sendEmail($aUser[0]['email_address'], 'info@mytcg.net', 'Password Update - Mobile Game Card Applications',
    'So you forgot your password. Don\'t worry, it happens to everyone at some point.

We have randomly generated a new password for you to use. 
    
If you are not happy with this password, feel free to change it to whatever you want in the `My details` section of the site.
Make sure you log in with this new password first to get to that screen.

Username: '.$aUser[0]['username'].'
New Password: '.$password);
       
       echo '<reset>'.$sCRLF;
       echo $sTab.'<epicfail val="0" />'.$sCRLF;
       echo $sTab.'<epicmessage val="We have emailed you a new password to the given address.<br>You can change it again in the My Details section." />'.$sCRLF;
       echo '</reset>'.$sCRLF;
       
     }else{
       echo '<reset>'.$sCRLF;
       echo $sTab.'<epicfail val="1" />'.$sCRLF;
       echo $sTab.'<epicmessage val="This user has been deactivated." />'.$sCRLF;
       echo '</reset>'.$sCRLF;
     }
  }else{
    echo '<reset>'.$sCRLF;
    echo $sTab.'<epicfail val="1" />'.$sCRLF;
    echo $sTab.'<epicmessage val="This email address is not registered with our system." />'.$sCRLF;
    echo '</reset>'.$sCRLF;
  }
  exit;
}


// user logs out
if (intval($_GET['logout'])==1){
	$_SESSION=array();
  setcookie("rLogin", "", time()-3600,"/");
	session_destroy();
	exit;
}
	

// user activates account
if ($sActivate=$_GET['activate']){
	$aConf=myqu(
		'SELECT category, keyname, keyvalue '
		.'FROM '.$Conf["database"]["table_prefix"].'_system'
	);
	$aCheck=myqu(
		"SELECT user_id "
		."FROM ".$Conf["database"]["table_prefix"]."_user "
		."WHERE is_active='".$sActivate."'"
		);
	if (!$aCheck){
		echo findSQLValueFromKey($aConf,"memo","activate_message_fail");
	} else {
		$aActivate=myqu(
			"UPDATE ".$Conf["database"]["table_prefix"]."_user "
			."SET is_active='1' WHERE user_id='".$aCheck[0]["user_id"]."'"
		);
		echo findSQLValueFromKey($aConf,"memo","activate_message_success");
		echo "<br /><br />";
		echo "<a href='";
		echo $Conf["system"]["web_url"]."/".$Conf["files"]["directory_root"];
		echo "'>";
		echo $Conf["system"]["web_url"]."/".$Conf["files"]["directory_root"];
		echo "</a>";
	}
}

// user is logging in
if (intval($_GET["login"])==1){
	$sUsername=$_GET["username"];
	$sPassword=$_GET["password"];
	$aConf=myqu(
		'SELECT category, keyname, keyvalue '
		.'FROM '.$Conf["database"]["table_prefix"].'_system'
	);
	$sql = "SELECT user_id, username, password, date_last_visit,mobile_date_last_visit, premium, xp, freebie, completion_process_stage "
			."FROM ".$Conf["database"]["table_prefix"]."_user "
			."WHERE username='".$sUsername."' "
			."AND is_active='1'";
	$aValidUser=myqu($sql);
	$iValidUserID=$aValidUser[0]["user_id"];
	$iMod=(intval($iValidUserID) % 10)+1;
	$sPassword=substr(md5($iValidUserID),$iMod,10).md5($sPassword);
	if ($sPassword!=$aValidUser[0]['password']){
		$iValidUserID=0;
	}
	
	$sMobileLastDate=$aValidUser[0]["mobile_date_last_visit"];
	if ($iValidUserID){
		
		$sUA=$_SERVER["HTTP_USER_AGENT"];
    $ip = getip();
    $sUA=myqu("UPDATE ".$pre."_user SET last_useragent='".$sUA."',ip = '".$ip."' WHERE user_id='".$iValidUserID."'");
    
    $cps = intval($aValidUser[0]['completion_process_stage']);
    if(($cps==6)&&($sMobileLastDate==null)){
        myqu("UPDATE ".$pre."_user SET completion_process_stage = 7 WHERE user_id = ".$iValidUserID);
        $cps = 7;
    }
    
	//set session variables
	$_SESSION['user']['username']=$aValidUser[0]['username'];
	$_SESSION['user']['premium']=$aValidUser[0]['premium'];
	$_SESSION['user']['xp']=$aValidUser[0]['xp'];
	$_SESSION['user']['id']=$iValidUserID;
    
    //REMEMBER ME
    if($_GET['r']=="true"){
      $sDetails = base64_encode($iValidUserID."---".$aValidUser[0]['username']);
      $expire=time()+(60*60*24*14);
      setcookie("rLogin", $sDetails, $expire,"/");
    }
    
		echo '<login>'.$sCRLF;
		echo $sTab.'<action val="success" />'.$sCRLF;
    
		if($cps <= 2){
      $sql = "UPDATE ".$pre."_user SET completion_process_stage = 3 WHERE user_id = ".$iValidUserID;
      $ass = myqu($sql);
      $cps = 3;
		}
		echo $sTab.'<username val="'.$aValidUser[0]['username'].'" />'.$sCRLF;
		echo $sTab.'<credits val="'.$aValidUser[0]['premium'].'" />'.$sCRLF;
    echo $sTab.'<process val="'.$cps.'" />'.$sCRLF;
    echo $sTab.'<xp val="'.$aValidUser[0]['xp'].'" />'.$sCRLF;
	} else {
		echo '<login>'.$sCRLF;
		echo $sTab.'<action val="fail" />'.$sCRLF;
		echo $sTab.'<message val="'.findSQLValueFromKey($aConf,'memo','login_fail').'" />'.$sCRLF;
	}
	echo '</login>'.$sCRLF;
	exit;
}// END login





if (intval($_GET['init'])==1){
	$sql = 'SELECT category, keyname, keyvalue '
		.'FROM '.$Conf["database"]["table_prefix"].'_system';
	$aConf=myqu($sql);
	date_default_timezone_set(findSQLValueFromKey($aConf,'system','timezone'));
	echo '<init>'.$sCRLF;
	echo $sTab.'<containerwidth val="'
		.findSQLValueFromKey($aConf,'page','containerwidth').'" />'.$sCRLF;
	echo $sTab.'<containerheight val="'
		.findSQLValueFromKey($aConf,'page','containerheight').'" />'.$sCRLF;
	echo $sTab.'<footerheight val="'
		.findSQLValueFromKey($aConf,'page','footerheight').'" />'.$sCRLF;
	echo $sTab.'<headerheight val="'
		.findSQLValueFromKey($aConf,'page','headerheight').'" />'.$sCRLF;
	echo $sTab.'<headeropacity val="'
		.findSQLValueFromKey($aConf,'page','headeropacity').'" />'.$sCRLF;
	echo $sTab.'<windowopacity val="'
		.findSQLValueFromKey($aConf,'page','windowopacity').'" />'.$sCRLF;
	echo $sTab.'<windowopacityinactive val="'
		.findSQLValueFromKey($aConf,'page','windowopacityinactive').'" />'.$sCRLF;
	echo $sTab.'<componentmarginx val="'
		.findSQLValueFromKey($aConf,'page','componentmarginx').'" />'.$sCRLF;
	echo $sTab.'<componentmarginy val="'
		.findSQLValueFromKey($aConf,'page','componentmarginy').'" />'.$sCRLF;
	echo $sTab.'<windowtitleheight val="'
		.findSQLValueFromKey($aConf,'page','windowtitleheight').'" />'.$sCRLF;
	echo $sTab.'<windowdecorsize val="'
		.findSQLValueFromKey($aConf,'page','windowdecorsize').'" />'.$sCRLF;
  echo $sTab.'<menuleft val="'
    .findSQLValueFromKey($aConf,'menu','left').'" />'.$sCRLF;
	
	// User data and values
	
	$aUser = getUserData( $Conf["database"]["table_prefix"] );
	$aUser = $aUser[0];
	
	if ($_SESSION['user']['id']){
		$userID = $_SESSION['user']['id'];
		$popup = '0';
		echo $sTab.'<menutop val="';
		echo findSQLValueFromKey($aConf,'menu','top_in');
		//admin menu
		if ($userID==1){
			echo '|System';
		}
		
		$cps = $aUser['completion_process_stage'];
    $sMobileLastDate = $aUser['mobile_date_last_visit'];
    if(($cps=='6')&&($sMobileLastDate!="")){
      myqu("UPDATE mytcg_user SET completion_process_stage = 7 WHERE user_id = ".$userID);
      //myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val) VALUES (".$userID.", 'Received 10 premium for verifying email address', NOW(), 10)");
      $cps = "7";
    }
		
		echo '" />'.$sCRLF;
		echo $sTab.'<username val="'.$_SESSION['user']['username'].'" />'.$sCRLF;
		echo $sTab.'<credit val="'.$aUser['credits'].'" />'.$sCRLF;
    echo $sTab.'<process val="'.$cps.'" />'.$sCRLF;
    	echo $sTab.'<xp val="'.$aUser['xp'].'" />'.$sCRLF;
    	$sLastDate = $aUser['date_last_visit'];
      
		//update last visit
		$sDate=date("Y-m-d H:i:s");
		$aDateVisit=myqu(
			"UPDATE ".$Conf["database"]["table_prefix"]."_user "
			."SET date_last_visit='".$sDate."' "
			."WHERE user_id='".$userID."'"
		);
    
		$today = date("Y-m-d");
		if((substr($sLastDate,0,10) != $today)&&(substr($sMobileLastDate,0,10) != $today))
		{
			$popup='1';
			//give user credits for daily login
			$amount = $aUser['credits'] + 20;
			myqu("UPDATE mytcg_user SET credits = (".$amount.") , gameswon=0 WHERE user_id=".$userID);
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
<<<<<<< HEAD
					VALUES(".$userID.", 'Received 20 credits for logging in today', NOW(), 20)");
=======
					VALUES(".$userID.", 'Received 20 credits for logging in today', NOW(), 50)");
					
			myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type,tcg_freemium,tcg_premium)
				VALUES(".$userID.", NULL, NULL, NULL, 
				now(), 'Received 20 credits for logging in today', 20, NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 16,20,0)");
>>>>>>> 14b96007eff1253f948047641ff905931bbc79dc
			echo $sTab.'<dailyvisit val="1" />'.$sCRLF;
			echo $sTab.'<newcredits val="'.$amount.'" />'.$sCRLF;
		}

		if(($aUser['freebie']=='0') || (is_null($aUser['freebie']))){
			$popup = '1';
			echo $sTab.'<freebie val="1" />'.$sCRLF;
			$starterPacks = myqu("SELECT P.product_id, P.description, CONCAT(IMG.description,'products/',P.image,'.jpg') AS 'image'
				FROM mytcg_product P
				JOIN mytcg_imageserver IMG ON P.full_imageserver_id = IMG.imageserver_id
				WHERE P.freebie = 1
				ORDER BY P.description ASC");
			echo $sTab.'<starterpackscount val="'.sizeof($starterPacks).'" />'.$sCRLF;
			if(sizeof($starterPacks) > 0){
				echo $sTab.'<starterpacks>'.$sCRLF;
				$i = 0;
				foreach($starterPacks as $starterPack){
					echo $sTab.$sTab.'<starterpack_'.$i.'>'.$sCRLF;
					echo $sTab.$sTab.$sTab.'<product_id val="'.$starterPack['product_id'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.'<description val="'.$starterPack['description'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.'<image val="'.$starterPack['image'].'" />'.$sCRLF;
					echo $sTab.$sTab.'</starterpack_'.$i.'>'.$sCRLF;
					$i++;
				}
				echo $sTab.'</starterpacks>'.$sCRLF;
			}
		}
		echo $sTab.'<popup val="'.$popup.'" />'.$sCRLF;
	} else {
		echo $sTab.'<menutop val="'.findSQLValueFromKey($aConf,'menu','top').'" />'.$sCRLF;
	}
	echo $sTab.'<menubottom val="'.findSQLValueFromKey($aConf,'menu','bottom').'" />'.$sCRLF;
	echo $sTab.'<url val="'.$Conf['system']['web_url'].'/" />'.$sCRLF;

  $sql = "SELECT category_id,description,parent_id,level "
	    ."FROM mytcg_category "
	    ."ORDER BY description;";
  
  echo $sTab.'<categories>'.$sCRLF;
  $aCategory=myqu($sql);
  $iCatCount=sizeof($aCategory);
  echo $sTab.$sTab.'<iCount>'.$iCatCount.'</iCount>'.$sCRLF;
  for ($iCount=0;$iCount<$iCatCount;$iCount++){
    echo $sTab.$sTab.'<category_'.$iCount.'>'.$sCRLF;
    echo $sTab.$sTab.$sTab.'<category_id>'.$aCategory[$iCount]["category_id"].'</category_id>'.$sCRLF;
    $parent_id = ($aCategory[$iCount]["parent_id"] > 0) ? $aCategory[$iCount]["parent_id"] : "main";
    echo $sTab.$sTab.$sTab.'<parent_id>'.$parent_id.'</parent_id>'.$sCRLF;
    echo $sTab.$sTab.$sTab.'<description>'.$aCategory[$iCount]["description"].'</description>'.$sCRLF;
    echo $sTab.$sTab.$sTab.'<level>'.$aCategory[$iCount]["level"].'</level>'.$sCRLF;
    echo $sTab.$sTab.'</category_'.$iCount.'>'.$sCRLF;
  }
  echo $sTab.'</categories>'.$sCRLF;
  echo $sTab.'<registered val="'.$_COOKIE['registered'].'" />'.$sCRLF;
	echo '</init>'.$sCRLF;

}//end INIT


//user gets starter pack
if(isset($_GET['starter']))
{
	$productId = $_GET['product'];
	
	//check that the user still has not chosen free starter pack (via phone app)
	$sql = "SELECT freebie FROM ".$pre."_user WHERE user_id=".$_SESSION['user']['id'];
	$freebieCheck = myqu($sql);
	$freebieCheck[0]['freebie'];
	
	if($freebieCheck[0]['freebie'] == '1')
	{
		//user has already chosen free starter pack
		echo '<freebie>'.$sCRLF;
		echo $sTab.'<result val="0" />'.$sCRLF;
		echo $sTab.'<message val="You have already receieved your free starter pack." />'.$sCRLF;
		echo '</freebie>'.$sCRLF;
	}
	else
	{
		//get size of starter pack
		$sizeQuery = myqu("SELECT no_of_cards FROM ".$pre."_product WHERE product_id=".$productId);
		$starterPackSize = intval($sizeQuery[0]['no_of_cards']);
		
		//get all available cards in starter pack
		$sql = "SELECT DISTINCT(card_id)
				FROM ".$pre."_productcard
				WHERE product_id = ".$productId."
				ORDER BY RAND();";
		$allCards = myqu($sql);
		//print_r($allCards);exit;
		$availableCards = array();
		$addedCards = array();
		if(sizeof($allCards) > 0){
			$i = 0;
			foreach($allCards as $aCard){
				$availableCards[$i] = $aCard['card_id'];
				$addedCards[$i] = 0;
				$i++; 
			}
		}
		$numberOfAvailableCards = sizeof($availableCards);
		//print_r($availableCards);exit;
		
		//get size-of-starter-pack number of random cards out from available cards
		$starterCards = array();
		for($i=0; $i<$starterPackSize; $i++){
			$count = 0;
			do {
				$id = rand(0,$numberOfAvailableCards-1);
				$count = intval($addedCards[$id]);
			} while($count > 1); //maximum of 2 of the same card
			$starterCards[$i] = $availableCards[$id];
			$addedCards[$id] = $count+1;
		}
		//print_r($starterCards);
		
		//give starter cards to user
		$i = 0;
		$values = array();
		foreach($starterCards as $card)
		{
			$values[] = '('.$_SESSION['user']['id'].', '.$card.', 1, 1)';
			$i++;
		}
		$sql = 'INSERT INTO mytcg_usercard (user_id, card_id, usercardstatus_id, is_new) VALUES '.
				implode(', ',$values).';';
		//echo $sql.$sCRLF;
		myqu($sql);
		
		//update user got freebie flag
		myqu("UPDATE mytcg_user SET freebie=1 WHERE user_id=".$_SESSION['user']['id']);
		
		echo '<freebie>'.$sCRLF;
		echo $sTab.'<result val="1">'.$sCRLF;
		echo '</freebie>'.$sCRLF;
	}
}


//user credit log
if(isset($_GET['premium']))
{
	$sql = "SELECT * FROM mytcg_transactionlog WHERE user_id = ".$_SESSION['user']['id']." ORDER BY transaction_id DESC LIMIT 10";
	$creditLogs = myqu($sql);
	//return xml
	echo '<logs>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($creditLogs).'" />'.$sCRLF;
	if(sizeof($creditLogs) > 0){
		$i = 0;
		foreach($creditLogs as $log){
			echo $sTab.'<log_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<date val="'.$log['date'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<message val="'.$log['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<amount val="'.$log['val'].'" />'.$sCRLF;
			echo $sTab.'</log_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo '</logs>';
	exit;
}

?>