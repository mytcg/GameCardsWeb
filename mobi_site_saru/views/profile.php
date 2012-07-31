<?php
$iUserID=$user['user_id'];
if (isset($_POST['profile'] ) && ($_GET["saveprofiledetail"]) == 1) {
	
	//Grab old email address
	$sql = "SELECT email_address FROM mytcg_user WHERE user_id = ".$iUserID;
	$sOldEmail = myqu($sql);
	$sOldEmail = $sOldEmail[0]['email_address'];
	
	//Save static details
	$sEmail = $_POST['email'];
	// $sPassword = $_POST['password'];
	$sCell = $_POST['cell'];
	$sName = $_POST['name'];
	$sSurname = $_POST['surname'];
	
	$sql = "SELECT email_address FROM mytcg_user WHERE email_address = '".$sEmail."'";
	$sValidEmail = myqu($sql);
	if((sizeof($sValidEmail) > 0)&&($sEmail != $sOldEmail)){
		echo "Email address already in use.";
	}else{
		//Update password if new one given
		// if($sPassword != ""){
			// $iMod=($user['user_id'] % 10)+1;
		    // $sSalt=substr(md5($user['user_id']),$iMod,10);
		    // $aSaltPassword=myqu("UPDATE mytcg_user SET password='".$sSalt.md5($sPassword)."' WHERE user_id=".$user['user_id']);
		// }
		
		//Update other fields
		$sql = "UPDATE mytcg_user SET
				username = '".$sEmail."',
				email_address = '".$sEmail."',
				msisdn = '".$sCell."',
				name = '".$sName."',
				surname = '".$sSurname."'
				WHERE user_id = ".$iUserID;
		$res = myqu($sql);

		//Set email verified if email address changed
		if($sOldEmail != $sEmail){
			$res=myqu("UPDATE mytcg_user SET email_verified=0 WHERE user_id=".$iUserID);
		}
		
		myqu("INSERT INTO tcg_user_log (user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id)
			SELECT user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id
			FROM mytcg_user WHERE user_id=".$iUserID);
		
		//UPDATE Personal information dynamic data
		$aQuestions = myqu("SELECT * FROM mytcg_user_detail");
	    foreach($aQuestions as $aQ){
	      $title = strtolower(str_replace(" ","",$aQ['description']));
	      if($_POST[$title]){
	        myqu("UPDATE mytcg_user_answer SET answer = '".$_POST[$title]."',answered=1 WHERE detail_id = ".$aQ['detail_id']." AND user_id = ".$iUserID);
	      }
	    }
		echo "Your details have been <b>saved successfully</b>.<br/> Return to <a href='index.php?page=profile'>profile</a>";
	}
	exit;
}
	$aUserProfile = myqu('SELECT user_id,email_address,email_verified, name, surname, msisdn
					FROM mytcg_user
					WHERE user_id ="'.$iUserID.'"');
    
    $aProfileDetails=myqu('SELECT d.description, d.detail_id, d.credit_value, a.answer_id, a.answered, a.answer 
						FROM mytcg_user_answer a, mytcg_user_detail d 
						WHERE a.detail_id = d.detail_id 
						AND a.user_id = "'.$iUserID.'"');
	$iCount=0;
?>
	<p class="text_important">Earn credits by filling in profile details...</p>
	<ul id="item_list">
	<li>Profile</li>
	</ul>
	<div style="margin-top: 15px; ">
    	<form method="POST" id="submitForm" action="index.php?page=profile&saveprofiledetail=1">
             <div class="profile_form">
            	<?php echo "<p>Email</p>";?>
            	<input type="text" name="email" value="<?php
            	if($aUserProfile[0]['email_address'] != NULL){
            	 echo ($aUserProfile[0]['email_address']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <div class="profile_form">
            	<?php echo "<p>Name</p>";?>
            	<input type="text" name="name" value="<?php
            	if($aUserProfile[0]['name'] != NULL){
            	 echo ($aUserProfile[0]['name']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <div class="profile_form">
            	<?php echo "<p>Surname</p>";?>
            	<input type="text" name="surname" value="<?php
            	if($aUserProfile[0]['surname'] != NULL){
            	 echo ($aUserProfile[0]['surname']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <div class="profile_form">
            	<?php echo "<p>Cell number</p>";?>
            	<input type="text" name="cell" value="<?php
            	if($aUserProfile[0]['msisdn'] != NULL){
            	 echo ($aUserProfile[0]['msisdn']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <?php
             while ($aProfileDetail=$aProfileDetails[$iCount]){ ?>
	             <div class="profile_form">
	            	<?php echo "<p>".$aProfileDetail['description']."</p>";?>
	            	<input type="text" value="<?php
	            	if($aProfileDetail['answered'] == 1){
	            	 echo ($aProfileDetail['answer']); }
	            	 else {
	            	 echo ("please enter detail for bonus credits");
	            	 }?>" name="<?php echo strtolower(str_replace(" ","",$aProfileDetail['description'])) ;?>" size="35" maxlength="50" class="textbox" />
	             </div>
             <?php
			 $iCount++;
             } ?>
            <input type="submit" name="profile" value="Save" class="button" title="Save" alt="Save"/>
    	</form>
	</div>