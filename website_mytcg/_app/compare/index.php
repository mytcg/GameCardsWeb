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
	$sql = "SELECT C.*, IMG.description AS 'path', CQ.description AS quality,
				(SELECT COUNT(*) AS 'possess' FROM ".$pre."_usercard WHERE card_id=C.card_id AND usercardstatus_id=1 AND user_id=".$userID.") AS possess,
				(SELECT GROUP_CONCAT(DISTINCT description SEPARATOR ',')
					FROM ".$pre."_productcard
					JOIN ".$pre."_product USING (product_id)
					WHERE card_id = C.card_id
					ORDER BY producttype_id ASC, description ASC
				) AS packs
			FROM ".$pre."_card C 
			JOIN ".$pre."_imageserver IMG ON C.back_imageserver_id = IMG.imageserver_id
			JOIN ".$pre."_cardquality CQ USING (cardquality_id) 
			GROUP BY C.card_id 
			ORDER BY C.description ASC";
	$allcards = myqu($sql);
	$allstats = array();
	
	// Return XML
	echo '<init>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($allcards).'" />'.$sCRLF;
	if(sizeof($allcards) > 0){
		echo $sTab.'<cards>'.$sCRLF;
		$i = 0;
		foreach($allcards as $card){
			//user cards
			echo $sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<card_id val="'.$card['card_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<possess val="'.$card['possess'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<path val="'.$card['path'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<quality val="'.$card['quality'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<ranking val="'.$card['ranking'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<avgranking val="'.$card['avgranking'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<value val="'.$card['value'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<packs val="'.$card['packs'].'" />'.$sCRLF;
			//card stats
			$cardstats = myqu("SELECT statvalue, top, width, height FROM ".$pre."_cardstat WHERE card_id=".$card['card_id']." ORDER BY categorystat_id ASC");
			if(sizeof($allstats) == 0){
				$allstats['width'] = $cardstats[0]['width'];
				$allstats['height'] = $cardstats[0]['height'];
				$allstats['count'] = count($cardstats);
			}				
			echo $sTab.$sTab.$sTab.'<stats>'.$sCRLF;
			if(sizeof($cardstats) > 0){
				$k = 0;
				foreach($cardstats as $stat){
					echo $sTab.$sTab.$sTab.$sTab.'<stat_'.$k.' val="'.$stat['statvalue'].'" />'.$sCRLF;
					if(sizeof($allstats['top']) < $allstats['count']){
						$allstats['top'][$k] = $stat['top'];
					}
					$k++;
				}
			}
			echo $sTab.$sTab.$sTab.'</stats>'.$sCRLF;
			echo $sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
		echo $sTab.'</cards>'.$sCRLF;
	}
	echo $sTab.'<allstats>'.$sCRLF;
	echo $sTab.$sTab.'<statscount val="'.$allstats['count'].'" />'.$sCRLF;
	echo $sTab.$sTab.'<width val="'.$allstats['width'].'" />'.$sCRLF;
	echo $sTab.$sTab.'<height val="'.$allstats['height'].'" />'.$sCRLF;
	for($i=0; $i<$allstats['count']; $i++){
		echo $sTab.$sTab.'<top_'.$i.' val="'.$allstats['top'][$i].'" />'.$sCRLF;
	}
	echo $sTab.'</allstats>'.$sCRLF;
	echo '</init>';
}



?>