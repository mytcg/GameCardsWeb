<?php
session_start();
if($_SERVER["SERVER_NAME"]=="localhost"){
	$aConn = array("localhost","root","","gamecard");
	$testing = true;  
}else{
	$aConn = array("localhost","mytcg_root","g4m3c4rd98","gamecard");
	$testing = false;
}

$sApplicationName = "Topcar Cards";
$sLogoFilename = "crosslogo.png";
$sFilePath = "Build/";
$sFileName = "TopCarCards";
$sWebPath = "http://mytcg.net/uad/";

$conn = mysql_connect($aConn[0],$aConn[1],$aConn[2]) or die ("Unable to connect to server");
mysql_select_db($aConn[3],$conn) or die ("Unable to connect to database");
?>
