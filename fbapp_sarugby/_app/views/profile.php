<?php
$response = false;
if($_POST['action']=="save"){
	//Grab old email address
	$sql = "SELECT email_address FROM mytcg_user WHERE user_id = ".$user['user_id'];
	$sOldEmail = myqu($sql);
	$sOldEmail = $sOldEmail[0]['email_address'];
	
	//Save static details
	$sEmail = $_POST['email_address'];
	$sPassword = $_POST['password'];
	$sCell = $_POST['msisdn'];
	$sName = $_POST['name'];
	$sSurname = $_POST['surname'];
	
	$sql = "SELECT email_address FROM mytcg_user WHERE email_address = '".$sEmail."'";
	$sValidEmail = myqu($sql);
	if(sizeof($sValidEmail) > 0){
		$returnMsg = "<span>Email address</span> already in use.";
	}else{
		//Update password if new one given
		if($sPassword != ""){
			$iMod=($user['user_id'] % 10)+1;
		    $sSalt=substr(md5($user['user_id']),$iMod,10);
		    $aSaltPassword=myqu("UPDATE mytcg_user SET password='".$sSalt.md5($sPassword)."' WHERE user_id=".$user['user_id']);
		}
		
		//Update other fields
		$sql = "UPDATE mytcg_user SET
				username = '".$sEmail."',
				email_address = '".$sEmail."',
				msisdn = '".$sCell."',
				name = '".$sName."',
				surname = '".$sSurname."'
				WHERE user_id = ".$user['user_id'];
		$res = myqu($sql);
		
		//Set email verified if email address changed
		if($sOldEmail != $sEmail){
			$res=myqu("UPDATE mytcg_user SET email_verified=0 WHERE user_id=".$user['user_id']);
		}
		
		//UPDATE Personal information dynamic data
		$sql = "SELECT * FROM mytcg_user_detail";
		$aDetails = myqu($sql);
		foreach($aDetails as $d){
			$id = $d['detail_id'];
			$var = $_POST[$id];
			if($var!=""){
				$res=myqu("UPDATE mytcg_user_answer SET answered=1, answer='".$var."' WHERE detail_id = ".$id." AND user_id=".$user['user_id']);
			}
		}
		$returnMsg = "Your details have been <span>saved successfully</span>.";
	}
	
	$response = true;
}

$sql = "SELECT user_id,email_address,email_verified, name, surname, msisdn
		FROM mytcg_user
		WHERE user_id = ".$user['user_id'];
$aUserData = myqu($sql);
$aUserData = $aUserData[0];

$sql = "SELECT UD.description, UA.answer, UD.detail_id
		FROM mytcg_user_answer UA
		INNER JOIN mytcg_user_detail UD ON (UA.detail_id = UD.detail_id)
		WHERE UA.user_id = ".$user['user_id'];
$aPersonalData = myqu($sql);

$verified = ($aUserData['email_verified']==1)? "dotActive" : "" ;
$showMsg = ($response)? "" : "display:none;";
?>
<div id="header" >
	<div class="headTitle">
		<div class="headProfile">
			<span>YOUR</span> PROFILE
		</div>
	</div>
	<form id="frmProfile" method="post" action="index.php?page=profile">
	<input type="hidden" name="action" value="save" />
	<div class="profileBody">
		<div class="profileSaved" style="<?php echo($showMsg); ?>"><?php echo($returnMsg); ?></div>
		<div class="profileSeparator"></div>
		<div class="profileContainerLeft">
			<div style="font-size:13px;"><span>Account</span> Profile</div> 
			Verify your email and update profile details.<br /><br />
			<span>E</span>MAIL<br />
			<input id="email_address" type="text" name="email_address" value="<?php echo($aUserData['email_address']); ?>" /><br /><br />
			<span>CHANGE</span> PASSWORD<br />
			<input type="text" name="password" /><br /><br />
			<span>Cell</span> Number<br />
			<input id="msisdn" type="text" name="msisdn" value="<?php echo($aUserData['msisdn']); ?>" /><br /><br />
			<span>Name</span><br />
			<input id="name" type="text" name="name" value="<?php echo($aUserData['name']); ?>" /><br /><br />
			<span>Surname</span><br />
			<input id="surname" type="text" name="surname" value="<?php echo($aUserData['surname']); ?>" /><br /><br /> 
			<span>Verify your email address</span><br />
			Click the send email button. Check your inbox.<br />
			Get the verification code, enter it in the box<br /> below and click the verify code button
			<input id="email_verified" type="text" name="email_verified" /><br /><br />
			
			<div class="profileDots <?php echo($verified); ?>" id="verified" style="top:416px;left:215px;"></div>
			
			<div id="btnSendEmail" class="profileButton" style="top:455px;left:0px;">SEND EMAIL</div>
			<div id="btnVerifyEmail" class="profileButton" style="top:455px;left:100px;">VERIFY CODE</div>
			<div id="txtResponse" class="profileResponse" style="height:16px;display:none;top:453px;left:200px;"><img src="_site/loading51.gif" width="15" height="15" /></div>
		</div>
		<div class="profileContainerRight">
			<div style="font-size:13px;margin-bottom:13px;"><span>Personal</span> Details</div>
			<div id="profileScroll" style="height:415px;width:270px;">
			<?php 
			$i = 0;
			foreach($aPersonalData as $data){
			$top = 23 + ($i * 64);
			$i++;
			$active = ($data['answer']!="")? "dotActive" : "";
			?>
			<span><?php echo($data['description']); ?></span><br />
			<input type="text" name="<?php echo($data['detail_id']); ?>" id="<?php echo($data['detail_id']); ?>" value="<?php echo($data['answer']); ?>" /><br /><br />
			<div class="profileDots <?php echo($active); ?>" style="top:<?php echo($top); ?>px;left:215px;"></div>
			<?php } ?>
			</div>
			<div id="btnProfileSave" class="profileButton" style="top:455px;left:0px;">SAVE ALL</div>
		</div>
	</div>
	</form>
</div>
<script language="JavaScript">
$(document).ready(function(){

	App.callAjax("_app/profile.php?answered=1",function(xml){

		//SHOW RECEIVED CARDS
		 var sXML = xml;
		 
		 if (parseInt(App.getXML(sXML,"success"))==1) {
			App.getItem(1);
		 }else {
			setTimeout(function() {App.showDid("Did you know?<br/><br/>You will get another card for completing your profile information.",1,true);},1000);
		}
	});
	/*App.getItem(1);*/
	
	//Active scrolling on dynamic personal details
	$('#profileScroll').jScrollPane();
	
	//Submit all details
	$("#btnProfileSave").click(function(){
		//validation
		var sEmail = $("#email_address").val();
		var validEmail = App.validateEmail(sEmail); 
		var sCell = $("#msisdn").val();
		var sName = $("#name").val();
		var sSurname = $("#surname").val();
		
		if(sEmail == ""){
			$(".profileSaved").html("<span>Email</span> cannot be empty.").fadeIn().delay(4000).fadeOut();
		}else if(!validEmail){
			$(".profileSaved").html("Not a valid <span>email address</span>.").fadeIn().delay(4000).fadeOut();
		}else if(sCell == ""){
			$(".profileSaved").html("<span>Cellnumber</span> cannot be empty.").fadeIn().delay(4000).fadeOut();
		}else if(isNaN(sCell)){
			$(".profileSaved").html("Not a valid <span>cell number</span>.").fadeIn().delay(4000).fadeOut();
		}else if(sName == ""){
			$(".profileSaved").html("<span>Name</span> cannot be empty.").fadeIn().delay(4000).fadeOut();
		}else if(sSurname == ""){
			$(".profileSaved").html("<span>Surname</span> cannot be empty.").fadeIn().delay(4000).fadeOut();
		}else{
			$("#frmProfile").submit();
		}
	});
	
	$(".profileSaved").delay(4000).fadeOut();
	
	//Send email verification details
	$("#btnSendEmail").click(function(){
		$("#txtResponse").fadeIn();
		var sEmail = $("#email_address").val();
		App.callAjax('_app/profile.php?email='+sEmail,function(sXML){
			var iSuccess = parseInt(App.getXML(sXML,"success"));
			if(iSuccess=="1"){
				$("#txtResponse").html("Email sent.").delay(4000).fadeOut().html('<img src="_site/loading51.gif" width="15" height="15" />');
			}else{
				$("#txtResponse").html("Could not send email.").delay(4000).fadeOut().html('<img src="_site/loading51.gif" width="15" height="15" />');
			}
		});
	});
	
	//Confirm email verification
	$("#btnVerifyEmail").click(function(){
		var sVerify = $("#email_verified").val();
		$("#txtResponse").html("Saved").delay(1000).fadeOut();
		App.callAjax('_app/profile.php?verify='+sVerify,function(sXML){
			var iSuccess = parseInt(App.getXML(sXML,"success"));
			if(iSuccess=="1"){
				$("#txtResponse").html("Email verified.").delay(4000).fadeOut().html('<img src="_site/loading51.gif" width="15" height="15" />');
				$("#verified").addClass("dotActive");
			}else{
				$("#txtResponse").html("Invalid code.").delay(4000).fadeOut().html('<img src="_site/loading51.gif" width="15" height="15" />');
			}
		});
	});

});
</script>