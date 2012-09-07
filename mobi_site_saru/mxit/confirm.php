<?php
require_once("../conn.php");
require_once("../functions.php");
$pre = "mytcg";

?>
<html>
   <head>
   <style>p,a {font-size:12px;font-family:"Arial","Arial Black";font-weight:900;text-decoration:none;color:#777777}</style>
      <title>SA Rugby Cards Mxit App</title>
   </head>
   <body>
       <img src="images/header_left.png" border="0" /><br />
<?php
if ($_SESSION['userID']){
		
		$username = $_SESSION['username'];
		$userID = $_SESSION['userID'];
		$amount = (int)$_GET['a'];
		$cost = $_GET['cost'];
		$result = 'success';
		$type = '1';
			
	    if($amount==2000){
	    	$type = '20';
			$gateway = 'mxit moola';
	       	$credits = 1400;
			$cost = "R20.00";
	    }elseif($amount==1000){
	    	$type = '20';
			$gateway = 'mxit moola';
		   	$credits = 700;
			$cost = "R7.00";
	    }elseif($amount==500){
	    	$type = '20';
			$gateway = 'mxit moola';
	       	$credits = 350;
			$cost = "R5.00";
	    }
	    // echo ("this ".$type." + ".$gateway." + ".$amount." + ".$cost." + ".$userID." + ".$pre);
		
		$referenceNumber = addTransaction($type,$gateway,$amount,$cost,$userID,$pre);
		
		// echo ($referenceNumber."asd;flkj");
		if(is_null($referenceNumber))
		{
			$result = 'failed';
		}
		
	   if($result == "success"){
	   	echo("<p>Purchase {$credits} credits for {$amount} Moola?</p>");
       ?>
        <form action="http://billing.mxit.com/Transaction/PaymentRequest" method="post"> 
			<input id="VendorId" name="VendorId" type="hidden" value="1" />
			<input id="TransactionReference" name="TransactionReference" type="hidden" value="<?php echo($referenceNumber); ?>" />
			<input id="CallbackUrl" name="CallbackUrl" type="hidden" value="http://www.sarugbycards.com/mxit/callback.php" />
			<input id="ProductId" name="ProductId" type="hidden" value="<?php echo($userID."-".$amount); ?>" />
			<input id="ProductName" name="ProductName" type="hidden" value="credits<?php echo($credits); ?>" />
			<input id="ProductDescription" name="ProductDescription" type="hidden" value="creditPurchase<?php echo($credits); ?>" />
			<input id="MoolaAmount" name="MoolaAmount" type="hidden" value="<?php echo($amount); ?>" />
			<input id="CurrencyAmount" name="CurrencyAmount" type="hidden" value="<?php echo($worth); ?>" />
			<input type="submit" value="Continue" />
		</form><br />
<?php  }
	}else{ echo("No user logged for the purchase, please <a href='info.php'>try again</a> "); }?>
       <a href="purchase.php">Back</a>
   </body>
<html>
