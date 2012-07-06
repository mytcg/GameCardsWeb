<?php
$iUserID=$user['user_id'];
if ($_POST['saveprofiledetail'] == 1) {
	$iAnswerID=$_GET['answer_id'];
	$iAnswer=$_GET['answer'];
	saveProfileDetail($iAnswerID, $iAnswer, $iUserID);
	exit;
}
	$aUserProfile = myqu('SELECT user_id,email_address,email_verified, name, surname, msisdn
					FROM mytcg_user
					WHERE user_id ="'.$iUserID.'"');
    
?>
	<p class="text_important">Earn credits by filling in profile details...</p>
	<ul id="item_list">
	<li>Profile</li>
	</ul>
	<div style="margin-top: 15px; ">
    	<form method="POST" id="submitForm" action="index.php?page=profile&saveprofiledetail=1&answer_id=<?php $aProfileDetails['answer_id'] ?>&<?php $aProfileDetails['answer'] ?>">
             <div class="profile_form">
            	<?php echo "<p>Email</p>";?>
            	<input type="text" name="" value="<?php
            	if($aUserProfile[0]['email_address'] != NULL){
            	 echo ($aUserProfile[0]['email_address']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <div class="profile_form">
            	<?php echo "<p>Name</p>";?>
            	<input type="text" name="" value="<?php
            	if($aUserProfile[0]['name'] != NULL){
            	 echo ($aUserProfile[0]['name']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <div class="profile_form">
            	<?php echo "<p>Surname</p>";?>
            	<input type="text" name="" value="<?php
            	if($aUserProfile[0]['surname'] != NULL){
            	 echo ($aUserProfile[0]['surname']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <div class="profile_form">
            	<?php echo "<p>Cell number</p>";?>
            	<input type="text" name="" value="<?php
            	if($aUserProfile[0]['msisdn'] != NULL){
            	 echo ($aUserProfile[0]['msisdn']); }
            	 else {
            	 echo ("please enter detail for bonus credits");
            	 }?>" size="35" maxlength="50" class="textbox" />
             </div>
             <?php 
              	$aProfileDetails=myqu('SELECT d.description, d.detail_id, d.credit_value, a.answer_id, a.answered, a.answer 
						FROM mytcg_user_answer a, mytcg_user_detail d 
						WHERE a.detail_id = d.detail_id 
						AND a.user_id = "'.$iUserID.'"');
				
    			$iCount=0;
             while ($aProfileDetail=$aProfileDetails[$iCount]){?>
	             <div class="profile_form">
	            	<?php echo "<p>".$aProfileDetail['description']."</p>";?>
	            	<input type="text" name="" value="<?php
	            	if($aProfileDetail['answered'] == 1){
	            	 echo ($aProfileDetail['answer']); }
	            	 else {
	            	 echo ("please enter detail for bonus credits");
	            	 }?>" size="35" maxlength="50" class="textbox" />
	             </div>
             <?php
			 $iCount++;
             } ?>
            <input type="submit" value="Save" class="button" title="Save" alt="Save"/>
    	</form>
	</div>