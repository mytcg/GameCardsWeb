<?php
session_start();

//Var short
$sCRLF="\r\n";
$sTab=chr(9);

$db['host'] = "sarugbycards.com";
$db['database'] = "sarugmhnuf_db1";
$db['username'] = "sarugmhnuf_1";
$db['password'] = "j2gcuH88";
$db['pre'] = "mytcg";


//Database Config

// $db['host'] = "localhost";
// $db['database'] = "sarugmhnuf_db1";
// $db['username'] = "sarugmhnuf_1";
// $db['password'] = "j2gcuH88";
// $db['pre'] = "mytcg";


//Faceboook Config
$fbconfig['appid'] = "327347500625234";
$fbconfig['secret'] = "6fba62249d3826549b8a5b12e44e0f8c";
$fbconfig['baseUrl']    =   "https://sarugbycards.com/fbapp";
$fbconfig['appBaseUrl'] =   "https://apps.facebook.com/sarugbycards";
?>
