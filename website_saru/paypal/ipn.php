<?php

if($_SERVER['HTTP_HOST']=='mytcg.net')
{
	$sTo = 'jaco@mytcg.net';
}
else
{
	$sTo = 'jaco@email.com';
}

require_once("../config.php");
require_once("../func.php");

//[payment_status] => Completed
//[option_selection1] => 350 TCG Credits
//[option_selection2] => 1423 //transaction_id

$emailbody = print_r($_POST,true);

//variables
$response = $_POST['payment_status'];
$logID = $_POST['option_selection2'];
$pre = $Conf["database"]["table_prefix"];

//update transaction log
$sql = "SELECT * FROM {$pre}_transactionlog WHERE transaction_id={$logID}";
$logQuery = myqu($sql);
if(sizeof($logQuery) > 0)
{
	$credits = $logQuery[0]['val'];
	if($logQuery[0]['transactionstatus_id']=='1')
	{
		if($response=='Completed')
		{
			$userID = $logQuery[0]['user_id'];
			//update user credits
			$sql = "UPDATE {$pre}_user SET credits=credits+{$credits} WHERE user_id={$userID}";
			$emailbody.= $sql;
			myqu($sql);
			//update transaction status
			$sql = "UPDATE {$pre}_transactionlog SET
						transactionstatus_id=2,
						response='{$response}'
						WHERE transaction_id={$logID}";
			$emailbody.= $sql;
			myqu($sql);
		}
		else
		{
			//update transaction status
			$sql = "UPDATE {$pre}_transactionlog SET
						transactionstatus_id=3,
						response='{$response}'
						WHERE transaction_id={$logID}";
			$emailbody.= $sql;
			myqu($sql);
		}
	}
	else
	{
		//transaction has already been updated/processed
	}
}

sendEmail($sTo,'admin@mytcg.net','paypal',$emailbody);

//return response
echo 'OK 200';
exit;

/*******************************************************************************
 * PayPal IPN Listener
 */
/*
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp) {
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
		}
	}
	fclose ($fp);
}
*/
?>