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
       		$mxitParameters = $_GET['mxit_transaction_res'];
       		if ($mxitParameters == 0){
       			echo 'Transaction completed successfully.<br/>';
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
		


       	?>	
		<?php  }else{ echo("No user logged for the purchase, please <a href='info.php'>try again</a><br/> "); }?>
       <a href="purchase.php">Back</a>
   </body>
<html>
