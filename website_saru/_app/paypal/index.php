<?php
/* This is the core file for the FBConnect component */
//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");

$sCRLF="\r\n";
$sHTMLCRLF="<br />";
$sTab=chr(9);
$pre = $Conf["database"]["table_prefix"];
$userID = $_SESSION["user"]["id"];
   
 
//Setup the database for the component
if($_GET['getcode'] == 1){
    
$code .= '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">';
$code .= '<input type="hidden" name="cmd" value="_s-xclick">';
$code .= '<input type="hidden" name="hosted_button_id" value="H4VNNFGNC3476">';
$code .= '<table>';
$code .= '<tr><td><input type="hidden" name="on0" value="Buy MyTCG Credits">Buy MyTCG Credits</td></tr><tr><td><select name="os0">';
$code .= '  <option value="100 Credits">100 Credits $5.99</option>';
$code .= '  <option value="200 Credits">200 Credits $9.99</option>';
$code .= '  <option value="500 Credits">500 Credits $19.99</option>';
$code .= '  <option value="1000 Credits">1000 Credits $35.99</option>';
$code .= '</select> </td></tr>';
$code .= '</table>';
$code .= '<input type="hidden" name="currency_code" value="USD">';
$code .= '<input type="hidden" name="custom" value="'.$userID.'">';
$code .= '<input type="image" src="https://www.sandbox.paypal.com/WEBSCR-640-20110429-1/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
$code .= '<img alt="" border="0" src="https://www.sandbox.paypal.com/WEBSCR-640-20110429-1/en_US/i/scr/pixel.gif" width="1" height="1">';
$code .= '</form>';




    
    echo $code;
    exit;
}





?>

