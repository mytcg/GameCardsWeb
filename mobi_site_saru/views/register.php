<?php 
if(isset($_POST["register"])==1){
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
	  $_SESSION['userID'] = $user_id;
	  $_SESSION['booster'] = 2;
	  
		  $packID = $_SESSION['booster'];
		  $userID = $_SESSION['userID'];
		  
		  ///giving the user its free cards
		  if ($packID == 2) {
			$iFree=myqu("SELECT freebie FROM mytcg_user WHERE user_id=".$userID);
			$iFree=$iFree[0]['freebie'];
			if ($iFree == 1) {
				echo("Unsuccesful Freebie given <a href");
				unset ($_SESSION['booster']);
				unset ($_SESSION['userID']);
				session_destroy($_SESSION['booster']);
				session_destroy($_SESSION['userID']);
				exit;
			} else {
				myqu("UPDATE mytcg_user SET freebie = 1 WHERE user_id=".$userID);
			}
		  }
		  
		  //GET PRODUCT DETAILS
		  $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, premium as price, P.no_of_cards '
		    .'FROM mytcg_product P '
		    .'INNER JOIN mytcg_producttype PT '
		    .'ON P.producttype_id = PT.producttype_id '
		    .'WHERE P.product_id='.$packID);
		  $iProductID = $aDetails[0]['product_id'];
		
		  //VALIDATE USER CREDITS
		  //User credits
		  $iCredits=myqu("SELECT premium FROM mytcg_user WHERE user_id=".$userID);
		  $iCredits=$iCredits[0]['premium'];
		  
		  //Total order cost
		  $itemCost = $aDetails[0]['price'];
		  $bValid = ($iCredits >= $itemCost);
		
			//RECEIVE ITEM
			$cards;
			if ($aDetails[0]['ptype'] == "Starter"){
			  $cards = openStarter($userID,$iProductID);
			}
			elseif($aDetails[0]['ptype'] == "Booster"){
			  $cards = openBooster($userID,$iProductID);
			}
		
			if(sizeof($cards) > 0){
			  //PAY FOR ORDER ITEM * QUANTITY ORDERED   
			  //$iCreditsAfterPurchase = $iCredits - $itemCost;
			  //$aCreditsLeft=myqu("UPDATE mytcg_user SET premium={$iCreditsAfterPurchase} WHERE user_id=".$userID);
			  //$_SESSION["user"]["premium"] = $iCreditsAfterPurchase;
			  
			  if ($packID == 2) {
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.",'Received free 3 card booster for registering.', NOW(), 0)");
					
				myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
					VALUES(".$userID.", ".$packID.", NULL, NULL, 
						now(), 'Received free 3 card booster for registering.', 0, NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 12)");
		
			 } 
			  ?>
			  <p>Your registration was successful<br/>Welcome to SA Rugby Cards</p>
			  <div>Here something to get you started...</div>
		        <?php
		        foreach($cards as $card){
		          $query='SELECT C.card_id, C.image, C.description,I.description AS path '
		                .'FROM mytcg_card C '
		                .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
		                .'WHERE C.card_id = '.$card['cardId'];
		          $aCard=myqu($query);
				  ?>
				  <div class="album_card_pic">
				  	<a href="index.php?page=card_display_front&card_id=<?php echo($aCard[0]['card_id']); ?>">
						<img src="<?php echo($aCard[0]['path']); ?>cards/jpeg/<?php echo($aCard[0]['image']); ?>_web.jpg" width="64" height="90" title="View potential cards">
			      	</a>
			      	<div style="width:64px"><?php echo($aCard[0]['description']); ?></div>
			      </div>
				  <?php
		          $iCount++;
		        }
				?>
				<div><a href="index.php?page=home"><div class="cmdButton" style="margin-top:5px;padding-top:8px;height:17px;">MENU</div></a></div>
				<?php
			}else{
		      echo("Your gift was not delivered...");
			}
		  exit;
}

if(!$_SESSION['userID']){ 
?>	
	<form method="POST" action="index.php?page=register&register=1" id="loginForm">
		Email:<br />
		<input type="text" name="email_address" value="" class="textbox" /><br />
		Password:<br />
		<input type="password" name="password" value="" class="textbox" /><br />
		First Name:<br />
		<input type="text" name="name" value="" class="textbox" /><br />
		Surname:<br />
		<input type="text" name="surname" value="" class="textbox" /><br />
		<input type="submit" name="register" value="REGISTER" style="width:80px;float:left" class="button" title="Login"/>
	<div><a href="index.php?page=index"><div class="cmdButton" style="margin-top:5px;padding-top:8px;height:17px;">BACK</div></a></div>
	</form>	


<?php } else { 
	echo("Hi,&nbsp;&nbsp;".$_SESSION['username']."<br>You are currently logged in<br><a href='index.php?page=home'>Main menu</a>");
 }?>	