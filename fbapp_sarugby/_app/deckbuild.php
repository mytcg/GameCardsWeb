<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

$userID = $_SESSION['userDetails']['user_id'];
$pre = $db['pre'];

if (isset($_GET['deck'])){
	
	$deckID = $_GET['deck'];
	$sString = substr($_GET['list'], 0, -1);
	
	$query = "DELETE FROM mytcg_deckcard WHERE deck_id = ".$deckID;
	$res = myqu($query);
	
	$aList = explode("@",$sString);
	for($i=0;$i < sizeof($aList);$i++){
		$aSplit = explode("||",$aList[$i]);
		$pos = $aSplit[0];
		$card_id =  $aSplit[1];
		
		$query = "INSERT INTO mytcg_deckcard (card_id,position_id,deck_id) VALUES ({$card_id},{$pos},{$deckID})";
		$res = myqu($query);
	}
	exit;
}
?>