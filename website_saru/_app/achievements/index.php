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
	$iID=myqu('SELECT id FROM mytcg_achievement ');
	$iNumAchis = sizeof($iID);
	
	$aServers=myqu('SELECT b.imageserver_id, b.description as URL 
		FROM mytcg_imageserver b 
		ORDER BY b.description DESC ');
	
	$sql = "SELECT progress, target, date_completed, complete_image, 
				name, description, incomplete_image, achievement_id, 
				a.imageserver_id as aserver_id, al.imageserver_id as alserver_id 
			FROM mytcg_userachievementlevel ual 
			LEFT OUTER JOIN mytcg_achievementlevel al 
			ON ual.achievementlevel_id = al.id 
			LEFT OUTER JOIN mytcg_achievement a 
			ON al.achievement_id = a.id 
			WHERE ual.user_id = ".$userID."
			ORDER BY achievement_id, name, target";
	$achiQuery = myqu($sql);
	
	$count = 0;
	$currentParent = '';
	
	//return xml
	echo '<achieve>'.$sCRLF;
	echo $sTab.'<count val="'.$iNumAchis.'" />'.$sCRLF;
	if(sizeof($achiQuery) > 0){
		$previous = 0;
		while ($aOneAchi=$achiQuery[$count]) {
			
			$achiId = $aOneAchi['achievement_id'];
			
			if ($achiId != $currentParent) {
				$currentParent = $achiId;
				
				if ($count > 0) {
					echo $sTab.$sTab.'</achi_'.$previous.'>'.$sCRLF;
					echo $sTab.'</achie>'.$sCRLF;
				}
				$previous = $achiId;
				
				echo $sTab.'<achie>'.$sCRLF;
				echo $sTab.$sTab.'<achi_'.$achiId.'>'.$sCRLF;
				echo $sTab.$sTab.$sTab.'<id>'.$achiId.'</id>'.$sCRLF;
				echo $sTab.$sTab.$sTab.'<name>'.$aOneAchi['name'].'</name>'.$sCRLF;
				echo $sTab.$sTab.$sTab.'<description>'.$aOneAchi['description'].'</description>'.$sCRLF;
				
				$sFound='';
				$iCountServer=0;
				while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
					if ($aOneServer['imageserver_id']==$aOneAchi['aserver_id']){
						$sFound=$aOneServer['URL'];
					} else {
						$iCountServer++;
					}
				}
				echo $sTab.$sTab.$sTab.'<incomplete_image>'.$sFound.'achi/'.$aOneAchi['incomplete_image'].'</incomplete_image>'.$sCRLF;
			}
			
			echo $sTab.$sTab.$sTab.'<subachi>'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<progress>'.$aOneAchi['progress'].'</progress>'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<target>'.$aOneAchi['target'].'</target>'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<date_completed>'.$aOneAchi['date_completed'].'</date_completed>'.$sCRLF;
			
			$sFound='';
			$iCountServer=0;
			while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
				if ($aOneServer['imageserver_id']==$aOneAchi['alserver_id']){
					$sFound=$aOneServer['URL'];
				} else {
					$iCountServer++;
				}
			}
			
			echo $sTab.$sTab.$sTab.$sTab.'<complete_image>'.$sFound.'achi/'.$aOneAchi['complete_image'].'</complete_image>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'</subachi>'.$sCRLF;
			$count++;
		}
		if ($count > 0) {
			echo $sTab.$sTab.'</achi_'.$achiId.'>'.$sCRLF;
			echo $sTab.'</achie>'.$sCRLF;
		}
	}
	echo '</achieve>'.$sCRLF;
	exit;
}

?>
