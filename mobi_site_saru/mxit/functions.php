<?php
require_once("conn.php");

function addTransaction($type, $gateway, $amount, $cost, $user, $pre)
{
	//insert new transaction log and set to submitted (status=1)
	$sql = "INSERT INTO mytcg_transactionlog(
					user_id,
					description,
					date,
					val,
					transactiontype_id,
					transactionstatus_id
				) VALUES (
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

function myqu($sQuery){
	global $db;
  	$aOutput=array();
  	$pattern = '/INSERT/i';
  
	$aLink=mysqli_connect($db["host"],$db["username"],$db["password"],$db["database"]);
	$sQuery=str_replace("&nbsp;","",$sQuery);
  
	if($aResult=@mysqli_query($aLink, $sQuery))
	{
		//If insert - return last insert id
		if(preg_match($pattern, $sQuery)){
			$mp = mysqli_insert_id($aLink);
			@mysqli_free_result($aResult);
      		mysqli_close($aLink);
			return $mp;
		}
    //Else build return array
		while ($aRow=@mysqli_fetch_array($aResult,MYSQL_BOTH)){
			$aOutput[]=$aRow;
		}
		return $aOutput;
	}
  else{
    die("[".date("Y-m-d H:i:s")."] Err:".mysqli_errno($aLink)." - ".mysqli_error($aLink)." - ".$_SERVER['PHP_SELF']." - ".$sQuery);
    @mysqli_free_result($aResult);
    mysqli_close($aLink);
  }
}
?>