<?php
//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
$sCRLF="\r\n";
$sTab=chr(9);

//LOGGED IN USER ID
$userID = $_SESSION["user"]["id"];

/*
 * Init
 */
if(isset($_GET['init']))
{
	$sql = "SELECT * FROM mytcg_notifications
			WHERE user_id={$userID}
			ORDER BY notedate DESC";
	$aNotifications = myqu($sql);
	//return xml
	echo '<logs>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($aNotifications).'" />'.$sCRLF;
	if(sizeof($aNotifications) > 0){
		$i = 0;
		foreach($aNotifications as $notifications){
			echo $sTab.'<log_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<date val="'.$notifications['notedate'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<message val="'.$notifications['notification'].'" />'.$sCRLF;
			echo $sTab.'</log_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo '</logs>';
	exit;
}

?>
