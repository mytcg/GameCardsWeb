<?php
$localhost = ($_SERVER['HTTP_HOST']=='localhost') ? true : false ;
session_start();

//Var short
$sCRLF="\r\n";
$sTab=chr(9);

if($localhost){
	$db['host'] = "localhost";
	$db['database'] = "gamecard";
	$db['username'] = "root";
	$db['password'] = "";
	$db['pre'] = "mytcg";
}else{
	$db['host'] = "localhost";
	$db['database'] = "gamecard";
	$db['username'] = "mytcg_root";
	$db['password'] = "g4m3c4rd98";
	$db['pre'] = "mytcg";
}

//Faceboook Config
$fbconfig['appid'] = "173115312813690";
$fbconfig['secret'] = "5976d79461bfd3c1c96993694da72764";
$fbconfig['baseUrl']    =   "http://mytcg.net/surfingcards";
$fbconfig['appBaseUrl'] =   "https://apps.facebook.com/surfingcards";
?>
