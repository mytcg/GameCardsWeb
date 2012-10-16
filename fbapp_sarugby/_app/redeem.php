<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

// user logs out
if (isset($_GET['code'])){
	$userID = $_SESSION['userDetails']['user_id'];
	$code = $_GET["code"];
	$xml = "";
	$success = true;
	$sql = "SELECT RC.redeemcode_id, RC.redeemtype_id AS redeemType, RC.target, RC.active, RC.code, RC.limit, DATE(RC.date_end) <= DATE(NOW()) AS expired, (SELECT COUNT(redeemuser_id) FROM mytcg_redeemuser WHERE redeemcode_id = RC.redeemcode_id AND user_id = {$userID}) as userRedeemed
			FROM mytcg_redeemcode RC
			INNER JOIN mytcg_redeemtype RT ON (RC.redeemtype_id = RT.redeemtype_id)
			WHERE code = '".$code."'";
	$res = myqu($sql);
	$redeemStatus = $res[0];
	if(sizeof($redeemStatus) > 0){
		if($redeemStatus['userRedeemed']==1){
			$xml .=  '<response>'.$sCRLF;
			$xml .=  $sTab.'<type>error</type>'.$sCRLF;
		    $xml .=  $sTab.'<value>You already used this code</value>'.$sCRLF;
			$xml .=  '</response>'.$sCRLF;
			$success = false;
		}elseif($redeemStatus['expired']==1){
			$xml .=  '<response>'.$sCRLF;
			$xml .=  $sTab.'<type>error</type>'.$sCRLF;
		    $xml .=  $sTab.'<value>Code has expired</value>'.$sCRLF;
			$xml .=  '</response>'.$sCRLF;
			$success = false;
		}elseif($redeemStatus['active']==2){
			$xml .=  '<response>'.$sCRLF;
			$xml .=  $sTab.'<type>error</type>'.$sCRLF;
		    $xml .=  $sTab.'<value>Code no longer active</value>'.$sCRLF;
			$xml .=  '</response>'.$sCRLF;
			$success = false;
		}elseif($redeemStatus['limit'] > 0){
			$sql = "SELECT COUNT(redeemuser_id) AS Count
					FROM mytcg_redeemuser
					WHERE redeemcode_id = ".$redeemStatus['redeemcode_id'];
			$count = myqu($sql);
			$count = $res[0]['Count'];
			if($count >= $redeemStatus['limit']){
				$xml .=  '<response>'.$sCRLF;
				$xml .=  $sTab.'<type>error</type>'.$sCRLF;
			    $xml .=  $sTab.'<value>All codes used</value>'.$sCRLF;
				$xml .=  '</response>'.$sCRLF;
				$success = false;
			}
		}
	}else{
		$xml .=  '<response>'.$sCRLF;
		$xml .=  $sTab.'<type>error</type>'.$sCRLF;
	    $xml .=  $sTab.'<value>Invalid code</value>'.$sCRLF;
		$xml .=  '</response>'.$sCRLF;
		$success = false;
	}
	if($success){
		switch ($redeemStatus['redeemType']){
			case 1:
				$sql = "INSERT INTO mytcg_usercard (user_id,card_id,usercardstatus_id,is_new,loaded) VALUES ({$userID},{$redeemStatus['target']},1,1,1)";
				$res = myqu($sql);
				$query='SELECT C.card_id, C.image, C.description,I.description AS path '
		                .'FROM mytcg_card C '
		                .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
		                .'WHERE C.card_id = '.$redeemStatus['target'];
				$aCard=myqu($query);
				$xml .=  '<response>'.$sCRLF;
				$xml .=  $sTab.'<type>cards</type>'.$sCRLF;
		        $xml .=  $sTab.'<value>1</value>'.$sCRLF;
		        $xml .=  $sTab.'<count>'.sizeof($aCard).'</count>'.$sCRLF;
		        $xml .=  $sTab.'<cards>'.$sCRLF;
				$xml .= $sTab.$sTab.'<card_0>'.$sCRLF;
		        $xml .= $sTab.$sTab.$sTab.'<cardid>'.$aCard[0]['card_id'].'</cardid>'.$sCRLF;
		        $xml .= $sTab.$sTab.$sTab.'<description>'.$aCard[0]['description'].'</description>'.$sCRLF;
		        $xml .= $sTab.$sTab.$sTab.'<qty>1</qty>'.$sCRLF;
		        $xml .= $sTab.$sTab.$sTab.'<path>'.$aCard[0]['path'].'</path>'.$sCRLF;
		        $xml .= $sTab.$sTab.$sTab.'<img>'.$aCard[0]['image'].'</img>'.$sCRLF;
		        $xml .= $sTab.$sTab.'</card_0>'.$sCRLF;
				$xml .=  $sTab.'</cards>'.$sCRLF;
		        $xml .=  '</response>'.$sCRLF;
				
				myqu("INSERT INTO mytcg_redeemuser (user_id, redeemcode_id)	VALUES (".$userID.",".$redeemStatus['redeemcode_id'].")");
			break;
			case 2:
				$cards = openBooster($userID,$redeemStatus['target']);
				
				$xml .=  '<response>'.$sCRLF;
				$xml .=  $sTab.'<type>cards</type>'.$sCRLF;
		        $xml .=  $sTab.'<value>1</value>'.$sCRLF;
		        $xml .=  $sTab.'<count>'.sizeof($cards).'</count>'.$sCRLF;
		        $xml .=  $sTab.'<cards>'.$sCRLF;
		        $iCount = 0;
		        foreach($cards as $card){
		          $query='SELECT C.card_id, C.image, C.description,I.description AS path '
		                .'FROM mytcg_card C '
		                .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
		                .'WHERE C.card_id = '.$card['cardId'];
		          $aCard=myqu($query);
		          $xml .= $sTab.$sTab.'<card_'.$iCount.'>'.$sCRLF;
		          $xml .= $sTab.$sTab.$sTab.'<cardid>'.$card['cardId'].'</cardid>'.$sCRLF;
		          $xml .= $sTab.$sTab.$sTab.'<description>'.$aCard[0]['description'].'</description>'.$sCRLF;
		          $xml .= $sTab.$sTab.$sTab.'<qty>'.$card['quantity'].'</qty>'.$sCRLF;
		          $xml .= $sTab.$sTab.$sTab.'<path>'.$aCard[0]['path'].'</path>'.$sCRLF;
		          $xml .= $sTab.$sTab.$sTab.'<img>'.$aCard[0]['image'].'</img>'.$sCRLF;
		          $xml .= $sTab.$sTab.'</card_'.$iCount.'>'.$sCRLF;
		          $iCount++;
		        }
		        $xml .=  $sTab.'</cards>'.$sCRLF;
		        $xml .=  '</response>'.$sCRLF;
				
				myqu("INSERT INTO mytcg_redeemuser (user_id, redeemcode_id)	VALUES (".$userID.",".$redeemStatus['redeemcode_id'].")");
			break;
			case 3:
				myqu("UPDATE mytcg_user SET credits = credits+".$redeemStatus['target']." WHERE user_id = ".$userID);				
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
						VALUES(".$userID.", 'Received ".$redeemStatus['target']." from redeemed voucher ".$code."', NOW(), ".$redeemStatus['target'].")");
				myqu("INSERT INTO tcg_transaction_log (fk_user,  transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
						VALUES(".$userID.",	NOW(), 'Redeemed voucher ".$redeemStatus['code']." for ".$redeemStatus['target']." freemium credits', ".$redeemStatus['target'].", ".$redeemStatus['target'].", 0, NULL, 'website',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 24)");
				
				$credits = myqu("SELECT (IFNULL(credits,0)+IFNULL(premium,0)) AS credits FROM mytcg_user WHERE user_id = ".$userID);
				
				$xml .=  '<response>'.$sCRLF;
				$xml .=  $sTab.'<type>credits</type>'.$sCRLF;
		        $xml .=  $sTab.'<value>'.$redeemStatus['target'].'</value>'.$sCRLF;
				$xml .=  $sTab.'<credits>'.$credits[0]['credits'].'</credits>'.$sCRLF;
		        $xml .=  '</response>'.$sCRLF;
				
				myqu("INSERT INTO mytcg_redeemuser (user_id, redeemcode_id)	VALUES (".$userID.",".$redeemStatus['redeemcode_id'].")");
			break;
			case 4:
				myqu("UPDATE mytcg_user SET premium = premium+".$redeemStatus['target']." WHERE user_id = ".$userID);				
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
						VALUES(".$userID.", 'Received ".$redeemStatus['target']." from redeemed voucher ".$code."', NOW(), ".$redeemStatus['target'].")");				
				myqu("INSERT INTO tcg_transaction_log (fk_user,  transaction_date, description, tcg_credits, tcg_premium, tcg_freemium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
						VALUES(".$userID.",	NOW(), 'Redeemed voucher ".$redeemStatus['code']." for ".$redeemStatus['target']." freemium credits', ".$redeemStatus['target'].", ".$redeemStatus['target'].", 0, NULL, 'website',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 24)");
				
				$credits = myqu("SELECT (IFNULL(credits,0)+IFNULL(premium,0)) AS credits FROM mytcg_user WHERE user_id = ".$userID);
				
				$xml .=  '<response>'.$sCRLF;
				$xml .=  $sTab.'<type>credits</type>'.$sCRLF;
		        $xml .=  $sTab.'<value>'.$redeemStatus['target'].'</value>'.$sCRLF;
				$xml .=  $sTab.'<credits>'.$credits[0]['credits'].'</credits>'.$sCRLF;
		        $xml .=  '</response>'.$sCRLF;
				
				myqu("INSERT INTO mytcg_redeemuser (user_id, redeemcode_id)	VALUES (".$userID.",".$redeemStatus['redeemcode_id'].")");
			break;
		}
	}
	echo($xml);
	exit;
}
?>