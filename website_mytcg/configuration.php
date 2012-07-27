<?php
session_start();

//Var short
$sCRLF="\r\n";
$sTab=chr(9);

//Database Config

$db['host'] = "localhost";
$db['database'] = "gamecard";
$db['username'] = "mytcg_root";
$db['password'] = "g4m3c4rd98";
$db['pre'] = "mytcg";


//Faceboook Config

$fbconfig['appid'] = "148093075213465";
$fbconfig['secret'] = "50d96782a8f57e27f191260eef504fd9";
$fbconfig['baseUrl']    =   "https://mytcg.net/fbapp";
$fbconfig['appBaseUrl'] =   "https://apps.facebook.com/mobilegamecards";
?>
