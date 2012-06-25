<?php

//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
$sCRLF="\r\n";
$sTab=chr(9);

//SETUP PREFIX FOR TABLES
$pre = $Conf["database"]["table_prefix"];

$userID = $_SESSION["user"]["id"];

if(isset($_GET['init']))
{
	$sql = "SELECT * FROM ".$pre."_leaderboards";
	$leaderBoards = myqu($sql);
	
	// Return XML
	echo '<init>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($leaderBoards).'" />'.$sCRLF;
	
	if(sizeof($leaderBoards) > 0){
		echo $sTab.'<leaderboards>'.$sCRLF;
		$i = 0;
		foreach($leaderBoards as $leaderBoard){
			$sql = $leaderBoard['lquery'];
			$leaders = myqu($sql);
			echo $sTab.$sTab.'<leaderboard_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description val="'.$leaderBoard['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<count val="'.sizeof($leaders).'" />'.$sCRLF;
			if(sizeof($leaders) > 0){
				echo $sTab.$sTab.$sTab.'<leaders>'.$sCRLF;
				$k = 0;
				foreach($leaders as $leader){
					echo $sTab.$sTab.$sTab.$sTab.'<leader_'.$k.'>'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<username val="'.$leader['usr'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<value val="'.$leader['val'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.'</leader_'.$k.'>'.$sCRLF;
					$k++;
				}
				echo $sTab.$sTab.$sTab.'</leaders>'.$sCRLF;
			}
			echo $sTab.$sTab.'</leaderboard_'.$i.'>'.$sCRLF;
			$i++;
		}
		echo $sTab.'</leaderboards>'.$sCRLF;
	}
	echo '</init>';
}

?>
