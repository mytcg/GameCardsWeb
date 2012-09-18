<?php
require_once("conn.php");
require_once("functions.php");

?>
<html>
   <head>
   <style>p,a {font-size:12px;font-family:"Arial","Arial Black";font-weight:900;text-decoration:none;color:#777777}</style>
      <title>SA Rugby Cards Mxit App</title>
   </head>
   <body>
       <img src="images/header_left.png" border="0" /><br />
        <?php
       	 //$order_id = $_REQUEST['TransactionReference'];
		if($_GET['id']){
			$refid = $_GET['id'];
			//selecting transaction details
			$sql = 	"SELECT *
					 FROM mytcg_transactionlog
					 WHERE transaction_id = {$refid}
					 AND transactiontype_id = 23
					 AND transactionstatus_id = 1 ";
			$transaction = myqu($sql);
			
			$userID = $transaction[0]['user_id'];
			$description = $transaction[0]['description'];
			$item_cost = $transaction[0]['val'];
			$order_id = $transaction[0]['transaction_id'];
		}
       	 
   		$mxitParameters = $_GET['mxit_transaction_res'];
		
       		if ($mxitParameters == 0){
				$result = 'Transaction completed successfully.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 2, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
				
				myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type, order_id)
				VALUES((select user_id from mytcg_user where user_id = '".$userID."'), NULL, NULL, NULL, 
					now(), '".$description."', ".$item_cost.", 0, ".$item_cost.", 23, 'mxit',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = (select user_id from mytcg_user where user_id = '".$userID."')), 23, '".$order_id."')");

				$query = "update mytcg_user set premium=ifnull(premium,0)+".$item_cost." where user_id = '".$userID."'";
				myqu($query);

       		}elseif($mxitParameters == 1){
       			$result = 'Transaction rejected by user.';
       			$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == 2){
       			$result = 'Invalid MXit login name or password.';
       			$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == 3){
       			$result = 'User account is locked.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == 4){
       			$result = 'User has insufficient funds.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == 5){
       			$result = 'Transaction timed out before a response was received from the user.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == 6){
       			$result = 'The user logged out without confirming or rejecting the transaction.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == -2){
       			$result = 'The transaction parameters are not valid.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}elseif($mxitParameters == -1){
       			$result = 'Technical system error occurred.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}else{
       			$result = 'Transaction failed, internal error. please try again later.';
				$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where user_id = '".$userID."'),'".$description."', now(), ".$item_cost.", 23, 3, '".$result."', 2, NULL, ".$order_id.")";
				myqu($query);
       		}
			echo ($result."<br/>");
       	?>	
       <a href="purchase.php">Back</a>
   </body>
<html>
