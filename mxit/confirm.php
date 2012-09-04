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
		if ($_SESSION['userID']){
       // if($_POST['VendorId']!=""){
	       // echo($_POST['VendorId']."<br>");
		   // echo($_POST['TransactionReference']."<br>");
		   // echo($_POST['CallbackUrl']."<br>");
		   // echo($_POST['ProductId']."<br>");
		   // echo($_POST['ProductName']."<br>");
		   // echo($_POST['ProductDescription']."<br>");
		   // echo($_POST['MoolaAmount']."<br>");
		   // echo($_POST['CurrencyAmount']."<br>");
       // }
		$username = $_SESSION['username'];
		$userID = $_SESSION['userID'];
       
       $amount = (int)$_GET['a'];
       if($amount==2000){
       	 $credits = 1400;
		 $worth = "R20.00";
       }elseif($amount==1000){
	   	 $credits = 700;
		 $worth = "R7.00";
       }else{
       	 $credits = 350;
		 $worth = "R5.00";
       }
	   
	   echo("<p>Purchase {$credits} credits for {$amount} Moola?</p>");
       ?>
        <form action="http://billing.internal.mxit.com/Transaction/PaymentRequest" method="post"> 
			<input id="VendorId" name="VendorId" type="hidden" value="1" />
			<input id="TransactionReference" name="TransactionReference" type="hidden" value="<?php echo($username); ?>" />
			<input id="CallbackUrl" name="CallbackUrl" type="hidden" value="http://www.sarugbycards.com/mxit/callback.php" />
			<input id="ProductId" name="ProductId" type="hidden" value="<?php echo($userID."-".$amount); ?>" />
			<input id="ProductName" name="ProductName" type="hidden" value="credits<?php echo($credits); ?>" />
			<input id="ProductDescription" name="ProductDescription" type="hidden" value="creditPurchase<?php echo($credits); ?>" />
			<input id="MoolaAmount" name="MoolaAmount" type="hidden" value="<?php echo($amount); ?>" />
			<input id="CurrencyAmount" name="CurrencyAmount" type="hidden" value="<?php echo($worth); ?>" />
			<input type="submit" value="Continue" />
		</form><br />
		<?php  }else{ echo("No user logged for the purchase, please <a href='info.php'>try again</a> "); }?>
       <a href="purchase.php">Back</a>
   </body>
<html>
