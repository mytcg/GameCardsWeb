<?php

//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
$sCRLF="\r\n";
$sTab=chr(9);

//SETUP PREFIX FOR TABLES
$pre = $Conf["database"]["table_prefix"];

//LOGGED IN USER ID
$userID = $_SESSION["user"]["id"];

/*
 * Init
 */
if(isset($_GET['init']))
{
	$limit = ($_GET['limit']=='1') ? 'LIMIT 10' : '';
	$sql = "SELECT * FROM mytcg_transactionlog
				WHERE user_id={$userID}
				AND (transactionstatus_id=2 OR transactionstatus_id IS NULL)
				ORDER BY transaction_id DESC {$limit}";
	$creditLogs = myqu($sql);
	//return xml
	echo '<logs>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($creditLogs).'" />'.$sCRLF;
	if(sizeof($creditLogs) > 0){
		$i = 0;
		foreach($creditLogs as $log){
			echo $sTab.'<log_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<date val="'.$log['date'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<message val="'.$log['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<amount val="'.$log['val'].'" />'.$sCRLF;
			echo $sTab.'</log_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo '</logs>';
	exit;
}


function addTransaction($type, $gateway, $amount, $cost, $user, $pre)
{
	//insert new transaction log and set to submitted (status=1)
	$sql = "INSERT INTO mytcg_transactionlog(user_id,description,date,val,transactiontype_id,transactionstatus_id) VALUES (
				{$user},
				'Purchase {$amount} TCG credits by {$gateway} for {$cost}',
				NOW(),
				{$amount},
				{$type},
				1
			)";
	myqu($sql);
	
	//get and return unique transactionlog_id for transaction reference number to be passed to payment gateway
	$sql = "SELECT MAX(transaction_id) AS 'ref' FROM mytcg_transactionlog
				WHERE user_id={$user}
				AND transactiontype_id={$type}
				AND transactionstatus_id=1";
	$query = myqu($sql);
	
	return $query[0]['ref'];
}


/*
 * New payment gateway transaction
 */
if(isset($_GET['payment']))
{
	$amount = $_GET['amount'];
	$cost = $_GET['cost'];
	$result = 'success';
	$type = '1';
	switch($_GET['gateway'])
	{
		case 'sms':
			$type = '2';
			$gateway = 'SMS';
			$cost = 'R'.$cost.'.00';
			break;
		case 'paypal':
			$type = '3';
			$gateway = 'PayPal';
			break;
		case 'creditcard':
			$type = '4';
			$gateway = 'Credit Card';
			$cost = 'R'.$cost.'.00';
			break;
	}
	$referenceNumber = addTransaction($type,$gateway,$amount,$cost,$userID,$pre);
	
	if(is_null($referenceNumber))
	{
		$result = 'failed';
	}
	
	//return xml
	echo '<transaction>'.$sCRLF;
	echo $sTab.'<result val="'.$result.'" />'.$sCRLF;
	echo $sTab.'<reference val="'.$referenceNumber.'" />'.$sCRLF;
	echo '</transaction>';
	exit;
}

 
?>