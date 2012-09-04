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
			myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card,
				transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type,tcg_freemium,tcg_premium)
			VALUES(".$userID.", NULL, NULL, NULL, 
				now(), 'Received 20 credits for logging in today on the mobi site', 20, NULL, 'mobi site',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 16,20,0)");
			echo ("<div style='font-weight:bold;color:#B0750D;height:25px;text-align:center;'>You have received 20 for logging in today.</div>");
		}
	}
	else{
		echo ("invalid user details, please try again <br/>");
	}
}
if($_SERVER['HTTP_X_MXIT_USERID_R'] == null){
	
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
	<?php } 
	
	}else{
		
	if($_SESSION['userID']){ ?>
	<div id="navmenu">
		<a href="index.php?page=album_list" class="button" style="float:left" >Album</a><br />
		<a href="index.php?page=shop_list" class="button" style="float:left" >Shop</a><br />
		<a href="index.php?page=auction_cards" class="button" style="float:left" >Auction</a><br />
		<a href="index.php?page=credits" class="button" style="float:left" >Credits</a><br />
		<a href="index.php?page=profile" class="button" style="float:left" >Profile</a><br />
		<a href="index.php?page=notifications" class="button" style="float:left" >Notifications</a><br />
		<a href="index.php?page=logout" class="button" style="float:left" >Logout</a>
	</div>
	<?php } else { ?>
		<p>Error: Your log in was unsuccesful,</p>
		<a href='index.php?page=index'>Login in</a>
	<?php }
	}
	?>

