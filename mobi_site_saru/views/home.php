<?php 
if (isset($_POST["login"])==1){
	
	$sUsername=$_POST["username"];
	$sPassword=$_POST["password"];
	
	if ($sPassword == "" && $sUsername == ""){
		echo ("You have not entered any details, please try again");
		exit;
	}
	
	$sql = "SELECT user_id, username, password, date_last_visit,mobile_date_last_visit, premium, xp, freebie, completion_process_stage "
			."FROM mytcg_user "
			."WHERE username='".$sUsername."' "
			."AND is_active='1'";
	$aValidUser=myqu($sql);
	$iValidUserID=$aValidUser[0]["user_id"];
	
	$iMod=(intval($iValidUserID) % 10)+1;
	$sPassword=substr(md5($iValidUserID),$iMod,10).md5($sPassword);
	if ($sPassword!=$aValidUser[0]['password']){
		$iValidUserID=0;
	}
		
	
	if ($iValidUserID){
	$userID = $user['user_id'];
	$sql = "SELECT user_id, username, password, date_last_visit, mobile_date_last_visit , (ifnull(credits,0)+ifnull(premium,0)) credits,credits freemium, premium, xp, freebie, completion_process_stage "
			."FROM mytcg_user "
			."WHERE user_id='".$userID."' "
			."AND is_active='1'";
			$aUser = myqu($sql);
			$aUser = $aUser[0];
			$sMobileLastDate = $aUser['mobile_date_last_visit'];	
			$sLastDate = $aUser['date_last_visit'];
  			
			//update last visit
			$sDate=date("Y-m-d H:i:s");
			$aDateVisit=myqu(
				"UPDATE mytcg_user "
				."SET date_last_visit='".$sDate."', last_useragent = 'mobi' "
				."WHERE user_id='".$userID."'"
			);
			
		$today = date("Y-m-d");
		if((substr($sLastDate,0,10) != $today)&&(substr($sMobileLastDate,0,10) != $today))
		{
			//give user credits for daily login
			$amount = $aUser['credits'] + 20;
			myqu("UPDATE mytcg_user SET credits = (".$amount.") , gameswon=0 WHERE user_id=".$userID);
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Received 20 credits for logging in on the mobi site today', NOW(), 20)");
			echo ("<div style='font-weight:bold;color:#B0750D;height:25px;text-align:center;'>You have received 20 for logging in today.</div>");
		}
	}
	else{
		echo ("invalid user details, please try again <br/>");
	}	
}elseif (isset($_POST["register"])==1){
	  $sEmail = $_POST["email_address"];
	  $sPassword = $_POST["password"];
	  $sName = $_POST["name"];
	  $sSurname = $_POST["surname"];
	  
	  if ($sEmail == "" && $sPassword == "" && $sName == "" && $sSurname == ""){
		echo ("Please complete all all fields, please <a href='index.php'>try again</a>");
		exit;
	  }
	  
	  $sql = "SELECT user_id FROM mytcg_user WHERE email_address='".$sEmail."'";
	  $getUser = myqu($sql);
	  if(sizeof($getUser) > 0){
	    echo('Email address already in use. <a href="index.php">try again</a>');
	    exit;
	  }
	  
	  $sql = "INSERT INTO mytcg_user (name,surname,date_register,username,email_address,credits,premium) VALUES ('".$sName."','".$sSurname."',NOW(),'".$sEmail."','".$sEmail."',0,0)";
	  $res = myqu($sql);
	  
	  $sql = "SELECT user_id FROM mytcg_user WHERE email_address='".$sEmail."'";
	  $getUser = myqu($sql);
	  
	  $user_id = $getUser[0]['user_id'];
	  
	  myqu("INSERT INTO tcg_user_log (user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id)
		SELECT user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id
		FROM mytcg_user WHERE user_id=".$user_id);
	  
	  $iMod=(intval($user_id) % 10)+1;
	  $sPassword=substr(md5($user_id),$iMod,10).md5($sPassword);
	  
	  $sql = "UPDATE mytcg_user SET password = '".$sPassword."' WHERE user_id = ".$user_id;
	  $res = myqu($sql);
	  
	  $sUA=$_SERVER["HTTP_USER_AGENT"];
	  $sUA=myqu("UPDATE mytcg_user SET last_useragent='".$sUA."' WHERE user_id='".$user_id."'");
	  
	  $sql = "SELECT * FROM mytcg_user_detail";
	  $getUser = myqu($sql);
	  foreach($getUser as $u){
	  	$sql = "INSERT INTO mytcg_user_answer (detail_id,answered,user_id) VALUES (".$u['detail_id'].",0,".$user_id.")";
	  	$res = myqu($sql);
	  }
	  
	  echo("<p>Your registration was successfull<br/>Welcome to SA Rugby Cards<br/></p>");
	  $_SESSION['userID'] = $user_id;
	  $_SESSION['booster'] = 2;
	  header("Location: index.php?page=shop_buyout&free=2");
}

if($_SESSION['userID']){ ?>
<ul id="navmenu">
	<li><a href="index.php?page=album_list" class="button"><img alt="Album" src="images/Album.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=shop_list" class="button"><img alt="Shop" src="images/Shop.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=auction_cards" class="button"><img alt="Auction" src="images/Auctions.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=credits" class="button"><img alt="Credits" src="images/Credits.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=profile" class="button"><img alt="Profile" src="images/Profile.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=notifications" class="button"><img alt="Notifications" src="images/Notifications.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=logout" class="button"><img alt="LOGOUT" src="images/Logout.png" width="115px" height="82px" /></a></li>
</ul>
<?php } else { ?>
	Error: Your log in was unsuccesful,<br>
	<a href='index.php?page=index'>Login in</a>
<?php } ?>