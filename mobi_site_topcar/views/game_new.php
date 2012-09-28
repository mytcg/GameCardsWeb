<?php

$query=	'SELECT GP.*, G.*
		FROM mytcg_gameplayer AS GP
		INNER JOIN mytcg_game AS G
		WHERE GP.user_id = '.$user['user_id'].'
		AND G.game_id = GP.game_id
		ORDER BY G.date_start ASC ';
$aGames = myqu($query);
$iCount = 0;
$iUserID = $user['user_id'];
/** for when a user selects a stat. Compares to the corresponding stat on the opponents card and returns results */
if ($_GET['selectstat']) {
	
	$cardStatId = $_GET['statid'];
	$gameId = $_GET['game_id'];
	
	//select your opponent's id
	$opponentIdQuery = myqu('SELECT user_id 
		FROM mytcg_gameplayer 
		WHERE user_id != '.$iUserID.' 
		AND game_id = '.$gameId);
	$oppId = $opponentIdQuery[0]['user_id'];
	
	//select the category stat id
	$categoryStatQuery = myqu('SELECT categorystat_id 
		FROM mytcg_cardstat
		WHERE cardstat_id = '.$cardStatId);
	$categoryStatId = $categoryStatQuery[0]['categorystat_id'];
	
	//build xml with scores and explanation and send it back
	selectStat($iUserID, $oppId, $gameId, $categoryStatId);
	
	//load the game for the user
	// $sOP = loadGame($gameId, $iUserID, $iHeight, $iWidth, $root, $iBBHeight, $jpg);	
	//send xml with results back to the user
	// header('xml_length: '.strlen($sOP));
	// echo $sOP;


}

?>
<div id="card_game">
			<div id="opponent-card-side">
				<div class="card-overlay">
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
				</div>
				<img src="images/26_back.png" alt="Opponent Card" title="opponent" />
			</div>
			<div id="player-card-side">
				<div class="card-overlay">
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
					<div id="stat_selection">stat selection div</div>
				</div>
				<img src="images/26_back.png" alt="player Card" title="player" />
			</div>
</div>