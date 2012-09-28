<?php
$iUserID=$user['user_id'];
if ($_POST['saveprofiledetail'] == 1) {
	$iAnswerID=$_POST['answer_id'];
	$iAnswer=$_POST['answer'];
	saveProfileDetail($iAnswerID, $iAnswer, $iUserID);
	exit;
}
    $aProfileDetails=myqu('SELECT d.description, d.detail_id, d.credit_value, a.answer_id, a.answered, a.answer 
		FROM mytcg_user_answer a, mytcg_user_detail d 
		WHERE a.detail_id = d.detail_id 
		AND a.user_id="'.$iUserID.'"');
    $iCount=0;
	
?>
	<p class="text_important">Earn credits by filling in profile details...</p>
	<ul id="item_list">
	<li>Profile</li>
	</ul>
	<div style="margin-top: 15px; ">
    	<form method="POST" id="submitForm" action="index.php?page=profile&saveprofiledetail=1&answer_id=<?php $aProfileDetails['answer_id'] ?>&<?php $aProfileDetails['answer'] ?>">
             <?php while ($aProfileDetail=$aProfileDetails[$iCount]){?>
             <div class="profile_form">
            	<?php echo "<p>".$aProfileDetail['description']."<p>";?>
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