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
	$db["host"]="localhost";
	$db["databasename"]="gamecard";
	$db["username"]="mytcg_root";
	$db["password"]="g4m3c4rd98";
	$db["table_prefix"]="mytcg";
}

//Faceboook Config
$fbconfig['appid'] = "342203842518329";
$fbconfig['secret'] = "840f9dbf9af87721af9b095c67b3339f";
$fbconfig['baseUrl']    =   "https://sarugbycards.com/fbapp";
$fbconfig['appBaseUrl'] =   "https://apps.facebook.com/sarugbycards";
?>
