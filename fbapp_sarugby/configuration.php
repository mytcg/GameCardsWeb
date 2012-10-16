<?php
$localhost = ($_SERVER['HTTP_HOST']=='localhost') ? true : false ;
session_start();

//Var short
$sCRLF="\r\n";
$sTab=chr(9);

define('ACHI_INC','1'); 
define('ACHI_TOT','2'); 

if($localhost){
	$db['host'] = "localhost";
	$db['database'] = "sarugby";
	$db['username'] = "root";
	$db['password'] = "";
	$db['pre'] = "mytcg";
}else{
	$db['host'] = "localhost";
	$db['database'] = "sarugmhnuf_db1";
	$db['username'] = "sarugmhnuf_1";
	$db['password'] = "j2gcuH88";
	$db['pre'] = "mytcg";
}

//Faceboook Config
$fbconfig['appid'] = "342203842518329";
$fbconfig['secret'] = "840f9dbf9af87721af9b095c67b3339f";
$fbconfig['baseUrl']    =   "https://sarugbycards.com/fbapp";
$fbconfig['appBaseUrl'] =   "https://apps.facebook.com/sarugbycards";
?>
