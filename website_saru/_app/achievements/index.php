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
	$aServers=myqu('SELECT b.imageserver_id, b.description as URL 
		FROM mytcg_imageserver b 
		ORDER BY b.description DESC '
	);
	
	$sql = "SELECT progress, target, date_completed, complete_image, 
				name, description, incomplete_image, achievement_id, 
				a.imageserver_id as aserver_id, al.imageserver_id as alserver_id 
			FROM mytcg_userachievementlevel ual 
			LEFT OUTER JOIN mytcg_achievementlevel al 
			ON ual.achievementlevel_id = al.id 
			LEFT OUTER JOIN mytcg_achievement a 
			ON al.achievement_id = a.id 
			ORDER BY name, achievement_id, target";
	$achiQuery = myqu($sql);
	//return xml
	echo '<achieve>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($achiQuery).'" />'.$sCRLF;
	if(sizeof($achiQuery) > 0){
		$i = 0;
		foreach($achiQuery as $achieve){
			echo $sTab.'<achieve_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<name>'.$achieve['name'].'</name>'.$sCRLF;
			echo $sTab.$sTab.'<description>'.$achieve['description'].'</description>'.$sCRLF;
			
			$sFound='';
			$iCountServer=0;
			while ((!$sFound)&&($aOneServer=$aServers[$iCountServer])){
				if ($aOneServer['imageserver_id']==$achieve['aserver_id']){
					$sFound=$aOneServer['URL'];
				} else {
					$iCountServer++;
				}
			}
			
			echo $sTab.$sTab.'<incomplete_image>'.$sFound.'achi/'.$achieve['incomplete_image'].'</incomplete_image>'.$sCRLF;
			echo $sTab.'</achieve_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo '</achieve>';
	exit;
}

?>
