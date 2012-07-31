<?php

 require_once("../../config.php");
require_once("../../func.php");
//removeapp.php
 
//here you'll get the user id who is removing or deauthorize your application
$config['secret_key'] = "47614e6aef34b665541f865d2f5c9673" ; //this is your application's secret key
$data         =   parse_signed_request($_REQUEST['signed_request'], $config['secret_key']);
$fbUserId   =   $data['user_id'];
 
/*$fbUserId this is the Facebook User UID who is removed your application. So you can use this id to update your database or do other tasks if required for your application
*/

file_put_contents('/usr/www/users/mytcga/_app/fbconnect/myfile.txt', __LINE__.$sCRLF , FILE_APPEND);
 
 


$sCRLF="\r\n";
$sHTMLCRLF="<br />";
$sTab=chr(9);
$pre = "mytcg";

 
 
file_put_contents('/usr/www/users/mytcga/_app/fbconnect/myfile.txt', "update table ".$pre."_user set fbuid = '' where fbuid = '".$fbUserId."' --> ".$_SERVER["HTTP_referer"].$sCRLF , FILE_APPEND);

myqu("update ".$pre."_user set fbuid = '' where fbuid = '".$fbUserId."'");
 
 
/* These methods are provided by facebook
 
http://developers.facebook.com/docs/authentication/canvas
 
*/
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2);
 
  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);
 
  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }
 
  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }
 
  return $data;
}
 
function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}

?>