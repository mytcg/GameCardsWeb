<?php
require_once("conn.php");
?>
<html>
   <head>
   <style>p,a {font-size:12px;font-family:"Arial","Arial Black";font-weight:900;text-decoration:none;color:#777777}</style>
      <title>SA Rugby Cards Mxit App</title>
   </head>
   <body>
       <img src="images/header_left.png" border="0" /><br />
        <?php
       		$mxitParameters = $_GET['mxit_transaction_res'];
       		if ($mxitParameters == 0){
       			echo 'Transaction completed successfully.<br/>';
				// $description = "Purchased ".$item['description']." for ".$item['price']." Mxit Moola." ;
				// myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card,
				// transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type, order_id)
					// VALUES('.$userID.', NULL, NULL, NULL, 
				// now(), '".$description."', ".$item['item_id'].", 0, ".$item['item_id'].", 5, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = (select user_id from mytcg_user where facebook_user_id = '".$buyer_id."')), 15, '".$order_id."')");
       		}elseif($mxitParameters == 1){
       			echo 'Transaction rejected by user.<br/>';
       		}elseif($mxitParameters == 2){
       			echo 'Invalid MXit login name or password.<br/>';
       		}elseif($mxitParameters == 3){
       			echo 'User account is locked.<br/>';
       		}elseif($mxitParameters == 4){
       			echo 'User has insufficient funds.<br/>';
       		}elseif($mxitParameters == 5){
       			echo 'Transaction timed out before a response was received from the user.<br/>';
       		}elseif($mxitParameters == 6){
       			echo 'The user logged out without confirming or rejecting the transaction.<br/>';
       		}elseif($mxitParameters == -2){
       			echo 'The transaction parameters are not valid.<br/>';
       		}elseif($mxitParameters == -1){
       			echo 'Technical system error occurred.<br/>';
       		}else{
       			echo 'transaction failed, internal error. please try again later<br/>';
       		}
			
			// echo ($_GET['VendorId']);
			// echo ($_GET['TransactionReference']);
			// echo ($_GET['ProductId']);
			// echo ($_GET['ProductName']);
			// echo ($_GET['ProductDescription']);
			// echo ($_GET['MoolaAmount']);
			// echo ($_GET['CurrencyAmount']);

       	?>	
       <a href="purchase.php">Back</a>
   </body>
<html>
