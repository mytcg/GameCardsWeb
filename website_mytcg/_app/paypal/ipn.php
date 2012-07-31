
<?php

// read the post from PayPal system and add 'cmd'
// TODO: This component is at a critical stage. It can go anywhere  from this point forward.
//       Find out exactly what needs to be done, what the use cases are. If anything is not as 
//       it should be, it could be tidious to update this component.
//
//           Use Case 1:
//           - All transaction activity is logged in a table. This includes Succesfull transfers,
//             refunds, failures, pending statusses etc.
//           - We still need a way to identify the user via IPN. It can be done via normal postbacks,
//             but seeing as the frontend is purely clientside, it could mess around with the normal
//             system processes
//           - Setup notification emails
 
require_once("../../config.php");
require_once("../../func.php");

$sCRLF="\r\n";
$sHTMLCRLF="<br />";
$sTab=chr(9);
$pre = "mytcg";

myqu("
CREATE TABLE `".$pre."_paypal` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) NOT NULL ,
`payment_amount`  double NOT NULL ,
`credits`  int(11) NOT NULL ,
`payment_date`  datetime NOT NULL ,
`status`  varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`debug`  text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
PRIMARY KEY (`id`)
)
;
");
 
$req = 'cmd=_notify-validate';

$CreditTypes['5.99'] = 100;
$CreditTypes['9.99'] = 200;
$CreditTypes['19.99'] = 500;
$CreditTypes['35.99'] = 1000;

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
  
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
 
  // If testing on Sandbox use: 
$header .= "Host: www.sandbox.paypal.com:443\r\n";
//$header .= "Host: www.paypal.com:443\r\n";

$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

  // If testing on Sandbox use:
  $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
//$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);


/*
 *  

The status of the payment:

Canceled_Reversal: A reversal has been canceled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you.

Completed: The payment has been completed, and the funds have been added successfully to your account balance.

Created: A German ELV payment is made using Express Checkout.

Denied: You denied the payment. This happens only if the payment was previously pending because of possible reasons described for the pending_reason variable or the Fraud_Management_Filters_x variable.

Expired: This authorization has expired and cannot be captured.

Failed: The payment has failed. This happens only if the payment was made from your customer’s bank account.

Pending: The payment is pending. See pending_reason for more information.

Refunded: You refunded the payment.

Reversed: A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element.

Processed: A payment has been accepted.

Voided: This authorization has been voided.
 * 
 * 
 * 
 */


// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];

$pending_reason = $_POST['pending_reason'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$payer_email = $_POST['payer_email'];
$mytcg_userid = $_POST['custom'];


$pending_reasons['multi-currency'] = "You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.";
$pending_reasons['order'] = "You set the payment action to Order and have not yet captured funds.";
$pending_reasons['paymentreview'] = "The payment is pending while it is being reviewed by PayPal for risk.";
$pending_reasons['unilateral'] = "The payment is pending because it was made to an email address that is not yet registered or confirmed.";
$pending_reasons['upgrade'] = "The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status in order to receive the funds. Upgrade can also mean that you have reached the monthly limit for transactions on your account.";
$pending_reasons['verify']= "The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.";
$pending_reasons['other'] = "The payment is pending for a reason other than those listed above. For more information, contact PayPal Customer Service.";

//set email variables
$From_email = "From: test@test.com";
$Subject_line = "Jou ma";

$myFile = strtolower($payment_status).".txt";
$fh = fopen($myFile, 'r');

$template_text = fread($fh, filesize($myFile));
eval("\$body=\"$template_text\";");
$email_msg = $body;


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
      
      $mail_From = $From_email;
      $mail_To = $payer_email;//$payer_email;
      $mail_Subject = $Subject_line;
      $mail_Body = $email_msg;
      
      mail($mail_To, $mail_Subject, $mail_Body, $mail_From);
      
     $tmp = serialize($_POST);
     myqu("insert `".$pre."_paypal` (user_id,payment_amount,credits,payment_date,status,debug) VALUES (".$mytcg_userid.",".$payment_amount.",".$CreditTypes[$payment_amount].",NOW(),'SUCCESFULL','".$tmp."')");

     myqu("update ".$pre."_user set premium = premium + ".$CreditTypes[$payment_amount]." where user_id = ".$mytcg_userid);

    }
    else if (strcmp ($res, "INVALID") == 0) {
      // log for manual investigation
      
      $mail_From = $From_email;
      $mail_To = $payer_email;
      $mail_Subject = "INVALID IPN POST";
      $mail_Body = "INVALID IPN POST. The raw POST string is below.\n\n" . $req;
      
      myqu("Update `".$pre."_paypal` (user_id,payment_amount,credits,payment_date,status,debug) VALUES (".$mytcg_userid.",".$payment_amount.",".$CreditTypes[$payment_amount].",NOW(),'FAILED',\"".print_r($_POST)."\")");
      
      
      mail($mail_To, $mail_Subject, $mail_Body, $mail_From);

    }
}
fclose ($fp);
}

fclose($fh);
?>

