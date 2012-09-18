<?php
//redeem card, adds the relevant card to the user's album
if ($code=$_POST['redeemcode']) {
	$exists = myqu('SELECT *
		FROM mytcg_card
		WHERE redeem_code = "'.$code.'"');

	//returns 1 if one card matches the redeem code, or 0 if no cards match
	if (sizeof($exists) > 0) {
		myqu('INSERT INTO mytcg_usercard
			(user_id, card_id, usercardstatus_id, is_new)
			SELECT '.$iUserID.', card_id, 4, 1
			FROM mytcg_card
			WHERE redeem_code =  "'.$code.'"');
			
		echo  "Card successfully redeemed.";
	}
	else {
		echo "Invalid redeem code.";
	}
	
	
	exit;
}
?>
    	<form method="POST" id="submitForm">
            <div class="profile_form">
            	Redeem code:<br />
            	<input type="text" name="redeem&redeemcode" value="" class="textbox" />
			</div>
				<input type="submit" value="REDEEM" class="button" title="Redeem" alt="Redeem"/>
		</form>