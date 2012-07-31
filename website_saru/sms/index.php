<?php
//================================================ PHP MO code sample ================================================//

/*
* There's a number of parameters pushed to one's server when a reply/MO is received. In this simple example
* only the 3 most commonly expected parameters are considered, these are msisdn, sender and message.
*
$msisdn = $_REQUEST['msisdn'];
$sender = $_REQUEST['sender'];
$message = $_REQUEST['message'];

echo "A message with body " . $message . " was sent from " . $sender . " to " . $msisdn ."\n";

// Printing the rest of the other pushed parameters

foreach ( $_REQUEST as $param => $value ) {
	echo $param . ": " . $value . "<br />";
}
*/
//================================================ PHP MO code sample ================================================//

/*******************************************************************************
 * TESTING
 */
if($_SERVER['HTTP_HOST']=='mytcg.net')
{
	//header("Location:http://127.0.0.1/mytcg".$_SERVER['REQUEST_URI']);
	$sTo = 'jaco@mytcg.net';
}
else
{
	$sTo = 'jaco@email.com';
}
/*******************************************************************************
 * TESTING
 */

require_once("../config.php");
require_once("../func.php");
require_once("bulksms.php");

sendEmail($sTo,'admin@mytcg.net','bulksms','<pre>REQUEST '.print_r($_REQUEST,true).'</pre>');

//variables
$sms = new bulksms();
$pre = $Conf["database"]["table_prefix"];
$userID = $_SESSION["user"]["id"];
$url = ($_SERVER['HTTP_HOST']=='mytcg.net') ? $_SERVER['HTTP_HOST'] : '127.0.0.1/mytcg';
$logID = $_GET['p2'];
$response = $_GET['p3'];
$details = '';

$description = 'TCG credits';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>MyTCG Trading Card Games</title>
		<link rel="icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="../ss.css" />
	</head>
	<body>
		<div style="display:none;text-align:left;background:#fff;"><pre><?php echo print_r($_GET,true); echo print_r($logQuery,true); ?></pre></div>
		<div style="/*padding-top:190px;*/position:relative;">
			<h1>SMS Received.</h1>
			<p>Your account has been credited with <strong><?php echo $description; ?></strong>.</p>
			<p><a href="http://<?php echo $url; ?>">Return to mytcg.net</a></p>


<div style="width:90%;background:#fff;text-align:left;font-size:12pt;line-height:15px;padding:10px;">
	<pre>
BULKSMS
<?=$description?>
	</pre>
</div>


		</div>
	</body>
</html>