<?php
$url = 'mytcg.net';

require_once("../config.php");
require_once("../func.php");

$status = ucwords($_GET['st']);

if($status=='Completed')
{
	$status = 'Approved.';
	$description = 'TCG credits';
	$amt = $_GET['amt'];
	$credits = '0';
	switch($amt)
	{
		case '1.00':
			$credits = '350';
			break;
		case '1.50':
			$credits = '700';
			break;
		case '2.00':
			$credits = '1050';
			break;
	}
	$description = $credits.' TCG credits';
	$result = 'Your account has been credited with <strong>'.$description.'</strong>.';
	$message = '<strong>Thank you for your payment.</strong><br /><br />Your transaction has been completed, and a receipt for your purchase has been emailed to you. You may log into your account at <a href="http://www.sandbox.paypal.com/us" target="_blank">www.paypal.com/us</a> to view details of this transaction.';
}
else
{
	$status = 'Not Approved.';
	$result = '<span class="txtRed"><strong>Your payment was not completed.</strong></span>.';
	$message = 'You may log into your account at <a href="http://www.sandbox.paypal.com/us" target="_blank">www.paypal.com/us</a> to view details of this transaction.';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>MyTCG Trading Card Games</title>
		<link rel="icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="../ss.css" />
	</head>
	<body>
		<div style="padding-top:190px;position:relative;">
			<h1><?php echo $status; ?></h1>
			<p><?php echo $result; ?></p>
			<p style="width:40%;margin-left:auto;margin-right:auto;"><?php echo $message; ?></p>
			<br />
			<br />
			<p><a href="http://<?php echo $url; ?>">Return to mytcg.net</a></p>
		</div>
	</body>
</html>