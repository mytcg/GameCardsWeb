<?php
	//Friend Invite
	if ($_POST['friendinvite']){
		//Friend detail type
	  $tradeMethod = $_REQUEST['trademethod'];
	  //Friend detail
	  $receiveNumber = $_REQUEST['detail'];
	  
	  invite($tradeMethod, $receiveNumber, $iUserID, $invite);
	  exit;
	}
	
    $query = "SELECT * FROM mytcg_invite
            WHERE user_id=".$user['user_id'];
    $aFrendinv=myqu($query);
    $iCount=0;
?>
	<div style="margin-top: 15px; ">
    	<form method="POST" id="submitForm">
            <div class="profile_form">
            	Invite by Username<br />
            	<input type="text" name="Name" value="<?php echo($aFrendinv[0]['answer']); ?>" size="35" maxlength="50" class="textbox" />
            </div>
            <div class="profile_form">
	            Invite by Email<br />
	            <input type="int" name="Age" value="<?php echo($aFrendinv[1]['answer']); ?>" size="35" maxlength="50" class="textbox" />
            </div>
            <div class="profile_form">
	            Invite by Phone Number<br />
	            <input type="text" name="Own Car" value="<?php echo($aFrendinv[2]['answer']); ?>" size="35" maxlength="50" class="textbox" />
            </div>
            <input type="submit" value="CONTINUE" class="button" title="Continue" />
    	</form>
	</div>