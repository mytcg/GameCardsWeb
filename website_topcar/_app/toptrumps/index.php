<?php
require_once "../../func.php";

$sCRLF="\r\n";
$sTab=chr(9);

$pre = $Conf["database"]["table_prefix"];

$userID = $_SESSION["user"]["id"];

$deckSize = 10;

/*
 * Initialise game
 */
if (isset($_GET['init']))
{
	$gameId = $_GET['game'];
	$opponentId = $_GET['opponent'];
	$categoryId = $_GET['category'];
	$deckId = $_GET['deck'];
	$difficultyId = $_GET['difficulty'];
	
	if($gameId > 0)
	{
		if($opponentId == '0')
		{
			//Load saved game vs computer
			
			//get the game's difficulty level
			$gameQuery = myqu('SELECT gamedifficulty_id FROM mytcg_game WHERE game_id="'.$gameId.'"');
			$difficultyId = (!is_null($gameQuery[0]['gamedifficulty_id']) && strlen($gameQuery[0]['gamedifficulty_id'])>0) ? $gameQuery[0]['gamedifficulty_id'] : '1';
			
			//get the user's details
			$userQuery = myqu('SELECT * FROM mytcg_user WHERE user_id="'.$userID.'"');
			$userName = $userQuery[0]['username'];
			
			//get the admin user's details
			$adminUserQuery = myqu('SELECT user_id, username FROM mytcg_user WHERE user_id = "1"');
			$adminUserId = $adminUserQuery[0]['user_id'];
			$adminUserName = $adminUserQuery[0]['username'];
			
			//we need to get both players' gameplayer_id
			$userPlayerQuery = myqu('SELECT gameplayer_id, is_active
				FROM mytcg_gameplayer 
				WHERE user_id = '.$userID
				.' AND game_id = '.$gameId);
			$userPlayerId = $userPlayerQuery[0]['gameplayer_id'];
			if($userPlayerQuery[0]['is_active']=='1'){
				$activePlayer = '1';
			}
			$adminPlayerQuery = myqu('SELECT gameplayer_id, is_active
				FROM mytcg_gameplayer 
				WHERE user_id = '.$adminUserId
				.' AND game_id = '.$gameId);
			$adminPlayerId = $adminPlayerQuery[0]['gameplayer_id'];
			if($adminPlayerQuery[0]['is_active']=='1'){
				$activePlayer = '2';
			}
			
			//get the user cards, in position
			$sql = "SELECT GPC.*, UC.card_id
					FROM mytcg_gameplayercard GPC
					JOIN mytcg_usercard UC USING (usercard_id)
					WHERE GPC.gameplayer_id=".$userPlayerId."
					AND GPC.gameplayercardstatus_id=1
					ORDER BY GPC.pos ASC;";
			$cards = myqu($sql);
			$userCards = array();
			if(sizeof($cards) > 0)
			{
				foreach($cards as $card)
				{
					$userCards[] = $card['card_id'];
				}
			}
			
			//get the computer cards, in position
			$sql = "SELECT GPC.*, UC.card_id
					FROM mytcg_gameplayercard GPC
					JOIN mytcg_usercard UC USING (usercard_id)
					WHERE GPC.gameplayer_id=".$adminPlayerId."
					AND GPC.gameplayercardstatus_id=1
					ORDER BY GPC.pos ASC;";
			$cards = myqu($sql);
			$computerCards = array();
			if(sizeof($cards) > 0)
			{
				foreach($cards as $card)
				{
					$computerCards[] = $card['card_id'];
				}
			}
			
			//get p1 cards in draw pool
			$sql = "SELECT GPC.*, UC.card_id
					FROM mytcg_gameplayercard GPC
					JOIN mytcg_usercard UC USING (usercard_id)
					WHERE GPC.gameplayer_id=".$userPlayerId."
					AND GPC.gameplayercardstatus_id=2
					ORDER BY GPC.pos ASC;";
			$p1cards = myqu($sql);
			
			//get p2 cards in draw pool
			$sql = "SELECT GPC.*, UC.card_id
					FROM mytcg_gameplayercard GPC
					JOIN mytcg_usercard UC USING (usercard_id)
					WHERE GPC.gameplayer_id=".$adminPlayerId."
					AND GPC.gameplayercardstatus_id=2
					ORDER BY GPC.pos ASC;";
			$p2cards = myqu($sql);
			
			$drawCards = array();
			if(sizeof($p1cards) > 0)
			{
				for($i=0; $i<sizeof($p1cards); $i++)
				{
					if($activePlayer == '1')
					{
						$drawCards[] = $p2cards[$i]['card_id'];
						$drawCards[] = $p1cards[$i]['card_id'];
					}
					else
					{
						$drawCards[] = $p1cards[$i]['card_id'];
						$drawCards[] = $p2cards[$i]['card_id'];
					}
				}
			}
			
			echo '<init>'.$sCRLF;
			echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
			echo $sTab.'<difficulty val="'.$difficultyId.'" />'.$sCRLF;
			echo $sTab.'<p1card val="'.$userCards[0].'" />'.$sCRLF;
			echo $sTab.'<p1score val="'.count($userCards).'" />'.$sCRLF;
			echo $sTab.'<p1name val="'.$userName.'" />'.$sCRLF;
			echo $sTab.'<p2card val="';echo ($activePlayer=='2')?$computerCards[0]:'0';echo '" />'.$sCRLF;
			echo $sTab.'<p2score val="'.count($computerCards).'" />'.$sCRLF;
			echo $sTab.'<p2name val="'.$adminUserName.'" />'.$sCRLF;
			echo $sTab.'<activeplayer val="'.$activePlayer.'" />'.$sCRLF;
			echo $sTab.'<drawcards val="'.implode(',',$drawCards).'" />'.$sCRLF;
			echo '</init>';
		}
		else
		{
			//play game vs player (game has already been created)
			
			$players = array();
			
			//we need to get both players' gameplayer_id
			$gamePlayers = myqu('SELECT GP.*, U.username
				FROM mytcg_gameplayer GP
				JOIN mytcg_user U USING (user_id)
				WHERE GP.game_id = '.$gameId.'
				ORDER BY GP.gameplayer_id ASC');
			if($gamePlayers[0]['user_id'] == $userID){
				//This user is player 1
				$players[1]['gameplayer_id'] = $gamePlayers[0]['gameplayer_id'];
				$players[1]['user_id'] = $gamePlayers[0]['user_id'];
				$players[1]['deck_id'] = $gamePlayers[0]['deck_id'];
				$players[1]['username'] = $gamePlayers[0]['username'];
				$players[2]['gameplayer_id'] = $gamePlayers[1]['gameplayer_id'];
				$players[2]['user_id'] = $gamePlayers[1]['user_id'];
				$players[2]['deck_id'] = $gamePlayers[1]['deck_id'];
				$players[2]['username'] = $gamePlayers[1]['username'];
				if($gamePlayers[0]['is_active'] == '1'){
					$activePlayer = '1';
				}
				else{
					$activePlayer = '2';
				}
			}
			else{
				//This user is player 2
				$players[1]['gameplayer_id'] = $gamePlayers[1]['gameplayer_id'];
				$players[1]['user_id'] = $gamePlayers[1]['user_id'];
				$players[1]['deck_id'] = $gamePlayers[1]['deck_id'];
				$players[1]['username'] = $gamePlayers[1]['username'];
				$players[2]['gameplayer_id'] = $gamePlayers[0]['gameplayer_id'];
				$players[2]['user_id'] = $gamePlayers[0]['user_id'];
				$players[2]['deck_id'] = $gamePlayers[0]['deck_id'];
				$players[2]['username'] = $gamePlayers[0]['username'];
				if($gamePlayers[1]['is_active'] == '1'){
					$activePlayer = '1';
				}
				else{
					$activePlayer = '2';
				}
			}
			
			//check if game has been initialised
			$sql = "SELECT gamestatus_id, gamephase_id FROM mytcg_game WHERE game_id=".$gameId;
			$gameStatus = myqu($sql);
			
			if($gameStatus[0]['gamestatus_id'] == '99')
			{
				//Initialise game: game still open - set as incomplete to start the game
				
				//get both players deck cards
				$p = 1;
				foreach($gamePlayers as $player)
				{
					//get player cards from deck
					//check if deck was selected, otherwise use 10 highest ranked cards
					if($players[$p]['deck_id'] != '-1'){
						$sql = "SELECT usercard_id, card_id FROM mytcg_usercard WHERE deck_id=".$players[$p]['deck_id']." AND user_id=".$players[$p]['user_id']." ORDER BY RAND();";
					}
					else{
						$sql = "SELECT * FROM
								(
									SELECT UC.card_id, UC.usercard_id, C.ranking
									FROM mytcg_card C
									JOIN mytcg_usercard UC USING (card_id)
									JOIN mytcg_category CA USING (category_id)
									WHERE CA.parent_id = ".$categoryId."
									AND UC.user_id = ".$players[$p]['user_id']."
									GROUP BY UC.card_id
									ORDER BY C.ranking DESC
									LIMIT 10
								) tmp_table
								ORDER BY RAND();";
					}
					$deckCards = myqu($sql);
					if(sizeof($deckCards) > 0)
					{
						foreach($deckCards as $deckCard){
							$players[$p]['usercards'][] = $deckCard['usercard_id'];
							$players[$p]['cards'][] = $deckCard['card_id'];
						}
					}
					$p++;
				}
				
				//add both players cards to game
				foreach($players as $player)
				{
					$i = 0;
					$values = array();
					foreach($player['usercards'] as $card)
					{
						$values[] = '('.$player['gameplayer_id'].', '.$card.', 1, '.$i.')';
						$i++;
					}
					$sql = 'INSERT INTO mytcg_gameplayercard (gameplayer_id, usercard_id, gameplayercardstatus_id, pos) VALUES '.
							implode(', ',$values).';';
					myqu($sql);
				}
				
				//update game to indicate it has been initialised
				$sql = "UPDATE mytcg_game SET gamestatus_id=1, gamephase_id=2 WHERE game_id=".$gameId;
				myqu($sql);
			}
			else
			{
				//Load game
				
				//get player 1 cards, in position
				$sql = "SELECT GPC.*, UC.card_id
						FROM mytcg_gameplayercard GPC
						JOIN mytcg_usercard UC USING (usercard_id)
						WHERE GPC.gameplayer_id=".$players[1]['gameplayer_id']."
						AND GPC.gameplayercardstatus_id=1
						ORDER BY GPC.pos ASC";
				$cards = myqu($sql);
				if(sizeof($cards) > 0)
				{
					foreach($cards as $card)
					{
						$players[1]['cards'][] = $card['card_id'];
					}
				}
				
				//get player 2 cards, in position
				$sql = "SELECT GPC.*, UC.card_id
						FROM mytcg_gameplayercard GPC
						JOIN mytcg_usercard UC USING (usercard_id)
						WHERE GPC.gameplayer_id=".$players[2]['gameplayer_id']."
						AND GPC.gameplayercardstatus_id=1
						ORDER BY GPC.pos ASC";
				$cards = myqu($sql);
				$computerCards = array();
				if(sizeof($cards) > 0)
				{
					foreach($cards as $card)
					{
						$players[2]['cards'][] = $card['card_id'];
					}
				}
				
				//Get cards in draw pool
				//get p1 cards in draw pool
				$sql = "SELECT GPC.*, UC.card_id
						FROM mytcg_gameplayercard GPC
						JOIN mytcg_usercard UC USING (usercard_id)
						WHERE GPC.gameplayer_id=".$players[1]['gameplayer_id']."
						AND GPC.gameplayercardstatus_id=2
						ORDER BY GPC.pos ASC;";
				$p1cards = myqu($sql);
				//get p2 cards in draw pool
				$sql = "SELECT GPC.*, UC.card_id
						FROM mytcg_gameplayercard GPC
						JOIN mytcg_usercard UC USING (usercard_id)
						WHERE GPC.gameplayer_id=".$players[2]['gameplayer_id']."
						AND GPC.gameplayercardstatus_id=2
						ORDER BY GPC.pos ASC;";
				$p2cards = myqu($sql);
				
				$drawCards = array();
				if(sizeof($p1cards) > 0)
				{
					for($i=0; $i<sizeof($p1cards); $i++)
					{
						if($activePlayer == '1')
						{
							$drawCards[] = $p2cards[$i]['card_id'];
							$drawCards[] = $p1cards[$i]['card_id'];
						}
						else
						{
							$drawCards[] = $p1cards[$i]['card_id'];
							$drawCards[] = $p2cards[$i]['card_id'];
						}
					}
				}
			}
			
			$sql = "SELECT * 
					FROM mytcg_gamelog
					WHERE game_id=".$gameId."
					ORDER BY gamelog_id DESC
					LIMIT 1;";
			$lastlog = myqu($sql);
			$lastlog = (sizeof($lastlog) > 0) ? $lastlog[0]['gamelog_id'] : '0';
			
			//number of moves made in the game so far
			$gameMoves = myqu("SELECT COUNT(*) AS 'moves' FROM mytcg_gamelog WHERE game_id=".$gameId." GROUP BY game_id");
			$gameMoves = (strlen($gameMoves[0]['moves']) > 0) ? $gameMoves[0]['moves'] : '0';
			
			echo '<init>'.$sCRLF;
			echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
			echo $sTab.'<p1card val="'.$players[1]['cards'][0].'" />'.$sCRLF;
			echo $sTab.'<p1score val="'.count($players[1]['cards']).'" />'.$sCRLF;
			echo $sTab.'<p1name val="'.$players[1]['username'].'" />'.$sCRLF;
			if($activePlayer=='2'){
				echo $sTab.'<p2card val="'.$players[2]['cards'][0].'" />'.$sCRLF;
			}
			else{
				echo $sTab.'<p2card val="0" />'.$sCRLF;
			}
			echo $sTab.'<p2score val="'.count($players[2]['cards']).'" />'.$sCRLF;
			echo $sTab.'<p2name val="'.$players[2]['username'].'" />'.$sCRLF;
			echo $sTab.'<activeplayer val="'.$activePlayer.'" />'.$sCRLF;
			echo $sTab.'<drawcards val="'.implode(',',$drawCards).'" />'.$sCRLF;
			echo $sTab.'<log val="'.$lastlog.'" />'.$sCRLF;
			echo $sTab.'<moves val="'.$gameMoves.'" />'.$sCRLF;
			echo '</init>';
		}
	}
	else
	{
		if($deckId == '0')
		{
			//Random play vs computer
			
			echo '<init>'.$sCRLF;
			echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
			echo $sTab.'<p1name val="YOU" />'.$sCRLF;
			echo $sTab.'<p2name val="COMPUTER" />'.$sCRLF;
			echo $sTab.'<activeplayer val="1" />'.$sCRLF;
			echo $sTab.'<drawcards val="" />'.$sCRLF;
			echo $sTab.'<log val="0" />'.$sCRLF;
			echo '</init>';
			
		}
		else
		{
			//Create new game vs computer
			
			//get the next available game id
			$gameIdQuery = myqu('SELECT (CASE WHEN MAX(game_id) IS NULL THEN 0 ELSE MAX(game_id) END) + 1 AS game_id FROM mytcg_game');
			$gameId = $gameIdQuery[0]['game_id'];
			
			//create a new game
			$sql = 'INSERT INTO mytcg_game (game_id, gamestatus_id, gamephase_id, category_id, date_start, gamedifficulty_id) 
				SELECT '.$gameId.', (SELECT gamestatus_id FROM mytcg_gamestatus WHERE lower(description) = "incomplete"),
				(SELECT gamephase_id FROM mytcg_gamephase WHERE lower(description) = "stat"), '.$categoryId.', now(), '.$difficultyId.'
				FROM DUAL;';
			//echo $sql.$sCRLF;
			myqu($sql);
			
			//get the user's details
			$userQuery = myqu('SELECT * FROM mytcg_user WHERE user_id="'.$userID.'"');
			$userName = $userQuery[0]['username'];
			
			//get the admin user's details
			$adminUserQuery = myqu('SELECT user_id, username FROM mytcg_user WHERE user_id = "1"');
			$adminUserId = $adminUserQuery[0]['user_id'];
			$adminUserName = $adminUserQuery[0]['username'];
			
			$activePlayer = '1';
			$turn['user']['is_active'] = '1';
			$turn['user']['gameplayerstatus_id'] = '1';
			$turn['computer']['is_active'] = '0';
			$turn['computer']['gameplayerstatus_id'] = '2';
			if($difficultyId == '3'){
				$activePlayer = '2';
				$turn['user']['is_active'] = '0';
				$turn['user']['gameplayerstatus_id'] = '2';
				$turn['computer']['is_active'] = '1';
				$turn['computer']['gameplayerstatus_id'] = '1';
			}
			//add this user and admin user as a players to the game
			$sql = 'INSERT INTO mytcg_gameplayer (game_id, user_id, is_active, gameplayerstatus_id)
				VALUES ('.$gameId.', '.$userID.', '.$turn['user']['is_active'].', '.$turn['user']['gameplayerstatus_id'].');';
			//echo $sql.$sCRLF;
			myqu($sql); //gameplayerstatus_id 1 is waiting, so they need to make the move
			$sql = 'INSERT INTO mytcg_gameplayer (game_id, user_id, is_active, gameplayerstatus_id)
				VALUES ('.$gameId.', '.$adminUserId.', '.$turn['computer']['is_active'].', '.$turn['computer']['gameplayerstatus_id'].');';
			//echo $sql.$sCRLF;
			myqu($sql);//gameplayerstatus_id 2 is waiting, so waiting for the other player to move
			
			//we need to get both players' gameplayer_id
			$userPlayerIdQuery = myqu('SELECT gameplayer_id 
				FROM mytcg_gameplayer 
				WHERE user_id = '.$userID
				.' AND game_id = '.$gameId);
			$userPlayerId = $userPlayerIdQuery[0]['gameplayer_id'];
			$adminPlayerId = myqu('SELECT gameplayer_id 
				FROM mytcg_gameplayer 
				WHERE user_id = '.$adminUserId
				.' AND game_id = '.$gameId);
			$adminPlayerId = $adminPlayerId[0]['gameplayer_id'];
			
			//get player cards from deck
			if($deckId != '-1'){
				$sql = "SELECT usercard_id, card_id FROM mytcg_usercard WHERE deck_id=".$deckId." AND user_id=".$userID." ORDER BY RAND();";
			}
			else{
				$sql = "SELECT * FROM
						(
							SELECT UC.card_id, UC.usercard_id, C.ranking
							FROM mytcg_card C
							JOIN mytcg_usercard UC USING (card_id)
							JOIN mytcg_category CA USING (category_id)
							WHERE CA.parent_id = ".$categoryId."
							AND UC.user_id = ".$userID."
							GROUP BY UC.card_id
							ORDER BY C.ranking DESC
							LIMIT 10
						) tmp_table
						ORDER BY RAND();";
			}
			$deckCards = myqu($sql);
			$userCards = array();
			$userCardIds = array();
			if(sizeof($deckCards) > 0){
				$i = 0;
				foreach($deckCards as $deckCard){
					$userCards[] = $deckCard['usercard_id'];
					$userCardIds[] = $deckCard['card_id'];
				}
			}
			//print_r($userCards);
			
			//get all admin user (computer) cards in category for difficulty level
			switch($difficultyId){
				case '1':
					$difficultylimit = ' AND (C.ranking BETWEEN 35 AND 60) ';
					$sql = "SELECT UC.card_id, UC.usercard_id
							FROM ".$pre."_card C
							JOIN ".$pre."_usercard UC USING (card_id)
							JOIN ".$pre."_category CA USING (category_id)
							WHERE CA.parent_id = ".$categoryId."
							".$difficultylimit."
							AND UC.user_id = ".$adminUserId."
							GROUP BY UC.card_id
							ORDER BY RAND()";
				break;
				case '2':
					$limit = '';
				case '3':
					if(!isset($limit)) $limit = ' LIMIT 10';
					$difficultylimit = '77';
					$sql = "SELECT * FROM
							(
								SELECT UC.card_id, UC.usercard_id, CS.ranking
								FROM ".$pre."_card C
								JOIN ".$pre."_usercard UC USING (card_id)
								JOIN ".$pre."_category CA USING (category_id)
								JOIN ".$pre."_cardstat CS USING (card_id)
								WHERE CA.parent_id = 2
								AND UC.user_id = 1
								ORDER BY card_id, ranking DESC
							) AS tmp
							WHERE ranking > ".$difficultylimit."
							GROUP BY card_id
							ORDER BY ranking DESC".
							$limit;
				break;
			}

			$allCards = myqu($sql);
			//print_r($allCards);
			$availableCards = array();
			$availableCardIds = array();
			$addedCards = array();
			if(sizeof($allCards) > 0){
				$i = 0;
				foreach($allCards as $aCard){
					$availableCards[] = $aCard['usercard_id'];
					$availableCardIds[] = $aCard['card_id'];
					$addedCards[$i] = 0;
					$i++;
				}
			}
			
			//get 10 random cards for computer out of available cards
			$size = count($allCards);
			$computerCards = array();
			for($i=0; $i<$deckSize; $i++){
				$count = 0;
				do {
					$id = rand(0,$size-1);
					$count = intval($addedCards[$id]);
				} while($count > 0); //only 1 copy of a card allowed
				$computerCards[$i] = $availableCards[$id];
				$computerCardIds[$i] = $availableCardIds[$id];
				$addedCards[$id] = $count+1;
			}
			//print_r($computerCards);
			
			//add user cards to game
			$i = 0;
			$values = array();
			foreach($userCards as $card)
			{
				$values[] = '('.$userPlayerId.', '.$card.', 1, '.$i.')';
				$i++;
			}
			$sql = 'INSERT INTO mytcg_gameplayercard (gameplayer_id, usercard_id, gameplayercardstatus_id, pos) VALUES '.
					implode(', ',$values).';';
			//echo $sql.$sCRLF;
			myqu($sql);
			
			//add computer cards to game
			$i = 0;
			$values = array();
			foreach($computerCards as $card)
			{
				$values[] = '('.$adminPlayerId.', '.$card.', 1, '.$i.')';
				$i++;
			}
			$sql = 'INSERT INTO mytcg_gameplayercard (gameplayer_id, usercard_id, gameplayercardstatus_id, pos) VALUES '.
					implode(', ',$values).';';
			//echo $sql.$sCRLF;
			myqu($sql);
			
			echo '<init>'.$sCRLF;
			echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
			echo $sTab.'<difficulty val="'.$difficultyId.'" />'.$sCRLF;
			echo $sTab.'<p1card val="'.$userCardIds[0].'" />'.$sCRLF;
			echo $sTab.'<p1score val="'.count($userCardIds).'" />'.$sCRLF;
			echo $sTab.'<p1name val="'.$userName.'" />'.$sCRLF;
			if($activePlayer=='2'){
				echo $sTab.'<p2card val="'.$computerCardIds[0].'" />'.$sCRLF;
			}
			else{
				echo $sTab.'<p2card val="0" />'.$sCRLF;
			}
			echo $sTab.'<p2score val="'.count($computerCardIds).'" />'.$sCRLF;
			echo $sTab.'<p2name val="'.$adminUserName.'" />'.$sCRLF;
			echo $sTab.'<activeplayer val="'.$activePlayer.'" />'.$sCRLF;
			echo $sTab.'<drawcards val="" />'.$sCRLF;
			echo '</init>';
		}
	}
	
}


/*
 * Listen
 */
if(isset($_GET['listen']))
{
	$gameId = $_GET['game'];
	$moves = $_GET['moves'];
	$nextmove = intval($moves)+1;
	$creditsWon = 0;
	
	//get last log entry
	$sql = "SELECT GS.description AS 'status', tmp.* FROM
			(
				SELECT * FROM {$pre}_gamelog WHERE game_id=".$gameId."
				ORDER BY gamelog_id ASC
				LIMIT ".$nextmove."
			)
			tmp
			JOIN {$pre}_game GA USING (game_id)
			JOIN {$pre}_gamestatus GS USING (gamestatus_id)
			ORDER BY gamelog_id DESC
			LIMIT 1";
	$log = myqu($sql);
	$logId = '0';
	$logStat = '0';
	$logMessage = '';
	$winner = '';
	$status = 'incomplete';
	if(sizeof($log) > 0){
		$logId = $log[0]['gamelog_id'];
		$logStat = $log[0]['categorystat_id'];
		$logMessage = $log[0]['message'];
		$winner = $log[0]['winner'];
		$status = $log[0]['status'];
		if($winner!='0'){
			//not a draw, swap winners for opponent's perspective
			if($log[0]['winner']=='1')
				$winner = '2';
			elseif($log[0]['winner']=='2')
				$winner = '1';
		}
		if($logStat=='-1'){
			//opponent has forfeited
			$status = 'forfeit';
			
			//give credits if the user is the winner
			$creditsWon = awardCredits($pre, $userID, $gameId, $deckSize);
		}
	}
	//winner is relative to the active player
	//so from the user's perspective the roles are reversed
	//for each player the opponent is player 2
	//winner = 0 : draw - other player is still the active player
	//winner = 1 : other player won and is still the active player
	//winner = 2 : this user won and is now the active player
	echo '<result>'.$sCRLF;
	echo $sTab.'<status val="'.$status.'" />'.$sCRLF;
	echo $sTab.'<log val="'.$logId.'" />'.$sCRLF;
	echo $sTab.'<stat val="'.$logStat.'" />'.$sCRLF;
	echo $sTab.'<message val="'.$logMessage.'" />'.$sCRLF;
	echo $sTab.'<winner val="'.$winner.'" />'.$sCRLF;
	echo $sTab.'<creditswon val="'.$creditsWon.'" />'.$sCRLF;
	echo '</result>';
	exit;
}


if(isset($_GET['choosestat']))
{
	$cardId = $_GET['choosestat'];
	echo chooseStat($cardId,3,true);
	exit;
}


/*
 * CPU player choosing a stat
 */
function chooseStat($cardId, $difficultyId=1, $showCheat=false)
{
	$pre = "mytcg";
	$sCRLF = "\r\n";
	$cheat = '';
	$id = $cardId;
	
	$cheat.= 'CARD '.$id.$sCRLF.'=========================='.$sCRLF;
	
	$probabilities = array();
	
	for($cat=1; $cat<=7; $cat++)
	{
		$cheat.= 'Stat '.$cat;
		
		$wins = 0;
		$losses = 0;
		$draws = 0;
		$total = 0;
			
		$sql = "SELECT * 
				FROM ".$pre."_cardstat CS
				WHERE CS.categorystat_id = $cat
				AND CS.card_id NOT IN ($id);";
		$cards = myqu($sql);
		
		if(count($cards) > 0)
		{
			$sql = "SELECT statvalue
					FROM ".$pre."_cardstat CS
					WHERE CS.categorystat_id = $cat
					AND CS.card_id = $id;";
			$base = myqu($sql);
			$baseval = $base[0]['statvalue'];
			
			$cheat.= ' -> '.$baseval;
			
			foreach($cards as $card)
			{
				if($baseval > $card['statvalue']){
					$wins++;
				}
				elseif($baseval < $card['statvalue']){
					$losses++;
				}
				else{
					$draws++;
				}
				$total++;
			}
		}
		$cheat.= $sCRLF.'-------------------'.$sCRLF;
		$probabilities['win'][$cat] = round(($wins/$total)*100,2);
		$probabilities['lose'][$cat] = round(($losses/$total)*100,2);
		$probabilities['draw'][$cat] = round(($draws/$total)*100,2);
		$combined[$cat] = $probabilities['lose'][$cat] + $probabilities['draw'][$cat];
		
	}

	$cheat.= 'Probabilities '.print_r($probabilities,true).'=========================='.$sCRLF;
	
	//sort stats according to lowest losing probability
	$statsinbestorder = $combined;
	asort($statsinbestorder);
	$i = 1;
	$beststat = array();
	foreach($statsinbestorder as $key=>$stat){
		$beststat[] = $key;
		if(++$i > 3) break;
	}
	
	$chosenstat = 0;
	switch($difficultyId){
		case '1':
			$difficultyLevel = 'Easy';
			$chosenstat = rand(0,2);
		break;
		case '2':
			$difficultyLevel = 'Normal';
			$chosenstat = rand(0,1);
		break;
		case '3':
			$difficultyLevel = 'Hard';
			$chosenstat = 0;
		break;
	}
	$chosenstat = $beststat[$chosenstat];
	
	$cheat.= 'BEST CHOICE = Stat '.$beststat[0].$sCRLF;
	$cheat.= '2nd CHOICE = Stat '.$beststat[1].$sCRLF;
	$cheat.= '3rd CHOICE = Stat '.$beststat[2].$sCRLF;
	$cheat.= '=========================='.$sCRLF;
	$cheat.= 'CPU Difficulty = '.$difficultyLevel.$sCRLF;
	$cheat.= 'CHOICE = Stat '.$chosenstat.$sCRLF;
	
	if($showCheat)
	{
		return $cheat;
	}
	else
	{
		return $chosenstat;
	}
}


/*
 * Play game
 */
if(isset($_GET['play']))
{
	$gameId = $_GET['game'];
	
	//***************************************
	//* freeplay							*
	//***************************************
	
	if($gameId=='0')
	{
		$p1card = $_GET['p1'];
		$p2card = $_GET['p2'];
		$activePlayer = $_GET['player'];
		if($activePlayer=='1'){
			$stat = intval($_GET['stat'])+1;
		}
		else{
			$stat = chooseStat($p2card);
		}
		
		//get player 1 card and stat
		$sql = "SELECT CA.description AS 'card', CAS.description AS 'stattype', CS.statvalue, CS.description AS 'stat'
				FROM {$pre}_card CA JOIN {$pre}_cardstat CS USING (card_id)
				JOIN {$pre}_categorystat CAS USING (categorystat_id)
				WHERE CA.card_id={$p1card} AND CS.categorystat_id=".($stat);
		$query = myqu($sql);
		$p1 = $query[0];
		$p[1]['card'] = $p1['card'];
		$p[1]['stat'] = $p1['stat'];
		$p[1]['type'] = $p1['stattype'];
		$p[1]['val'] = floatval($p1['statvalue']);
		//get player 1 card and stat
		$sql = "SELECT CA.description AS 'card', CAS.description AS 'stattype', CS.statvalue, CS.description AS 'stat'
				FROM {$pre}_card CA JOIN {$pre}_cardstat CS USING (card_id)
				JOIN {$pre}_categorystat CAS USING (categorystat_id)
				WHERE CA.card_id={$p2card} AND CS.categorystat_id=".($stat);
		$query = myqu($sql);
		$p2 = $query[0];
		$p[2]['card'] = $p2['card'];
		$p[2]['stat'] = $p2['stat'];
		$p[2]['type'] = $p2['stattype'];
		$p[2]['val'] = floatval($p2['statvalue']);
		
		//compare players' cards and determine the winner
		if($p[1]['val'] == $p[2]['val']){
			$winner = 0;
		}
		else{
			if($p[1]['val'] > $p[2]['val']){
				$winner = 1;
				$loser = 2;
			}
			else if($p[2]['val'] > $p[1]['val']){
				$winner = 2;
				$loser = 1;
			}
		}
		if($winner!=0){
			if($winner == 1){
				$message = 'You won! Your '.$p[$winner]['card'].' with a '.$p[$winner]['type'].' of '.$p[$winner]['stat'].' beat the Computer`s '.$p[$loser]['card'].' with a '.$p[$loser]['type'].' of '.$p[$loser]['stat'];
			}
			else{
				$message = 'You lost! The Computer`s '.$p[$winner]['card'].' with a '.$p[$winner]['type'].' of '.$p[$winner]['stat'].' beat the your '.$p[$loser]['card'].' with a '.$p[$loser]['type'].' of '.$p[$loser]['stat'];
			}
		}
		else{
			$message = 'Draw! Your '.$p[1]['card'].' with a '.$p[1]['type'].' of '.$p[1]['stat'].' equals the Computer`s '.$p[2]['card'].' with a '.$p[2]['type'].' of '.$p[2]['stat'];
		}
		
		//return xml
		echo '<play>'.$sCRLF;
		echo $sTab.'<status val="incomplete" />'.$sCRLF;
		echo $sTab.'<log val="0" />'.$sCRLF;
		echo $sTab.'<moves val="0" />'.$sCRLF;
		echo $sTab.'<message val="'.$message.'" />'.$sCRLF;
		echo $sTab.'<winner val="'.$winner.'" />'.$sCRLF;
		echo $sTab.'<p2card val="'.$p2card.'" />'.$sCRLF; //opponent's card for this round (must still be shown)
		echo $sTab.'<stat val="'.($stat-1).'" />'.$sCRLF;
		echo $sTab.'<creditswon val="0" />'.$sCRLF;
		echo '</play>';
		exit;
	}
	
	//***************************************
	//* live play							*
	//***************************************
	
	$activePlayer = $_GET['player']; //1 - user, 2 - opponent
	$gameStatus = 'incomplete';
	
	//Check if user is only viewing gameover results
	$gameInfo = myqu("SELECT gamephase_id FROM mytcg_game WHERE game_id=".$gameId);
	if($gameInfo[0]['gamephase_id']=='3')
	{
		//check if the game is complete because the other player forfeited the game
		$gameStatus = 'complete';
		$logMessage = '';
		$sql = "SELECT categorystat_id AS lastmove, message
				FROM ".$pre."_gamelog 
				WHERE game_id=".$gameId."
				ORDER BY gamelog_id DESC
				LIMIT 1";
		$gameQuery = myqu($sql);
		$lastMove = $gameQuery[0]['lastmove'];
		$creditsWon = '0';
		if($lastMove == '-1')
		{
			$gameStatus = 'forfeit';
			$logMessage = $gameQuery[0]['message'];
			//give credits if the user is the winner
			$creditsWon = awardCredits($pre, $userID, $gameId, $deckSize);
		}
		
		//Set game as complete
		$sql = "UPDATE mytcg_game
				SET gamestatus_id = 2
				WHERE game_id = ".$gameId;
		myqu($sql);
		
		echo '<result>'.$sCRLF;
		echo $sTab.'<log val="0" />'.$sCRLF;
		echo $sTab.'<status val="'.$gameStatus.'" />'.$sCRLF;
		echo $sTab.'<message val="'.$logMessage.'" />'.$sCRLF;
		echo $sTab.'<creditswon val="'.$creditsWon.'" />'.$sCRLF;
		echo '</result>';
		exit;
	}
	else
	{
		//Identify game players
		$sql = "SELECT GP.*, U.username
				FROM ".$pre."_gameplayer GP
				JOIN ".$pre."_user U USING (user_id)
				WHERE GP.game_id=".$gameId."
				ORDER BY GP.gameplayer_id ASC";
		$gamePlayers = myqu($sql);
		if($gamePlayers[0]['user_id'] == $userID){
			//This user is player 1
			$players[1]['gameplayer_id'] = $gamePlayers[0]['gameplayer_id'];
			$players[1]['user_id'] = $gamePlayers[0]['user_id'];
			$players[1]['deck_id'] = $gamePlayers[0]['deck_id'];
			$players[1]['username'] = $gamePlayers[0]['username'];
			$players[2]['gameplayer_id'] = $gamePlayers[1]['gameplayer_id'];
			$players[2]['user_id'] = $gamePlayers[1]['user_id'];
			$players[2]['deck_id'] = $gamePlayers[1]['deck_id'];
			$players[2]['username'] = $gamePlayers[1]['username'];
			$p1 = $gamePlayers[0]['gameplayer_id'];
			$p2 = $gamePlayers[1]['gameplayer_id'];
		}
		else{
			//This user is player 2
			$players[1]['gameplayer_id'] = $gamePlayers[1]['gameplayer_id'];
			$players[1]['user_id'] = $gamePlayers[1]['user_id'];
			$players[1]['deck_id'] = $gamePlayers[1]['deck_id'];
			$players[1]['username'] = $gamePlayers[1]['username'];
			$players[2]['gameplayer_id'] = $gamePlayers[0]['gameplayer_id'];
			$players[2]['user_id'] = $gamePlayers[0]['user_id'];
			$players[2]['deck_id'] = $gamePlayers[0]['deck_id'];
			$players[2]['username'] = $gamePlayers[0]['username'];
			$p2 = $gamePlayers[0]['gameplayer_id'];
			$p1 = $gamePlayers[1]['gameplayer_id'];
		}
		
		//Identify top card for player 1
		$sql = "SELECT GPC.gameplayercard_id, C.description AS card, C.card_id
				FROM ".$pre."_gameplayercard GPC
				LEFT JOIN ".$pre."_usercard UC USING (usercard_id)
				LEFT JOIN ".$pre."_card C USING (card_id)
				WHERE GPC.gameplayer_id=".$p1." AND GPC.gameplayercardstatus_id=1
				ORDER BY GPC.pos ASC
				LIMIT 1";
		$query = myqu($sql);
		$players[1]['topcard'] = $query[0]['gameplayercard_id'];
		$players[1]['card_id'] = $query[0]['card_id'];
		$players[1]['card'] = $query[0]['card'];
		
		//Identify top card for player 2
		$sql = "SELECT GPC.gameplayercard_id, C.description AS card, C.card_id
				FROM ".$pre."_gameplayercard GPC
				LEFT JOIN ".$pre."_usercard UC USING (usercard_id)
				LEFT JOIN ".$pre."_card C USING (card_id)
				WHERE GPC.gameplayer_id=".$p2." AND GPC.gameplayercardstatus_id=1
				ORDER BY GPC.pos ASC
				LIMIT 1";
		$query = myqu($sql);
		$players[2]['topcard'] = $query[0]['gameplayercard_id'];
		$players[2]['card_id'] = $query[0]['card_id'];
		$players[2]['card'] = $query[0]['card'];
		
		//Get stat values of both players' top cards
		if($activePlayer=='1'){
			$statId = intval($_GET['stat']) + 1; //stat selected
			$i = 1;
			foreach($players as $player){
				$sql = "SELECT CAT.description AS stattype, CAS.description AS stat, CAS.statvalue
						FROM {$pre}_card CA
						JOIN {$pre}_cardstat CAS USING(card_id)
						JOIN {$pre}_categorystat CAT USING(categorystat_id)
						WHERE CA.card_id={$player['card_id']}
						AND CAS.categorystat_id={$statId}";
				$cardstat = myqu($sql);
				//user has selected a stat
				$players[$i]['value'] = $cardstat[0]['statvalue'];
				$players[$i]['type'] = $cardstat[0]['stattype'];
				$players[$i]['stat'] = $cardstat[0]['stat'];
				$i++;
			}
		}
		elseif($activePlayer=='2'){
			//computer must still choose a stat
			$difficultyId = $_GET['difficulty'];
			$statId = chooseStat($players[2]['card_id'], $difficultyId);
			$i = 1;
			foreach($players as $player){
				$sql = "SELECT CAT.description AS stattype, CAS.description AS stat, CAS.statvalue
						FROM {$pre}_card CA
						JOIN {$pre}_cardstat CAS USING(card_id)
						JOIN {$pre}_categorystat CAT USING(categorystat_id)
						WHERE CA.card_id={$player['card_id']}
						AND CAS.categorystat_id={$statId}";
				$cardstat = myqu($sql);
				//user has selected a stat
				$players[$i]['value'] = $cardstat[0]['statvalue'];
				$players[$i]['type'] = $cardstat[0]['stattype'];
				$players[$i]['stat'] = $cardstat[0]['stat'];
				$i++;
			}
		}
		
		//Identify winner/loser
		if($players[1]['value'] == $players[2]['value']){
			$winningPlayer = '0';
			$losingPlayer = '0';
			$winner = '0';
			$explanation = "Draw! ".$players[1]['username']."`s ".$players[1]['card']." with a ".$players[1]['type']." of ".$players[1]['stat']." equals ".$players[2]['username']."`s ".$players[1]['card']."!";
		}
		elseif($players[1]['value'] > $players[2]['value']){
			$winningPlayer = $p1;
			$losingPlayer = $p2;
			$winner = '1';
			$explanation = $players[1]['username']." won! Their ".$players[1]['card']." with a ".$players[1]['type']." of ".$players[1]['stat']." beat ".$players[2]['username']."`s ".$players[2]['card']." with a ".$players[2]['type']." of ".$players[2]['stat'].".";
		}
		elseif($players[2]['value'] > $players[1]['value']){
			$winningPlayer = $p2;
			$losingPlayer = $p1;
			$winner = '2';
			$explanation = $players[2]['username']." won! Their ".$players[2]['card']." with a ".$players[2]['type']." of ".$players[2]['stat']." beat ".$players[1]['username']."`s ".$players[1]['card']." with a ".$players[1]['type']." of ".$players[1]['stat'].".";
		}
		
		//move cards according to who won
		if($winner != '0')
		{
			//one of the players has won, now check for cards in the draw pool and give to winning player
			//get p1 cards in draw pool
			$sql = "SELECT *
					FROM mytcg_gameplayercard GPC
					WHERE gameplayer_id=".$p1."
					AND gameplayercardstatus_id=2
					ORDER BY pos ASC;";
			$p1cards = myqu($sql);
			//get p2 cards in draw pool
			$sql = "SELECT *
					FROM mytcg_gameplayercard GPC
					WHERE gameplayer_id=".$p2."
					AND gameplayercardstatus_id=2
					ORDER BY pos ASC";
			$p2cards = myqu($sql);
			
			//get next card position for winning player
			$sql = "SELECT MAX(pos)+1 AS 'newpos' FROM mytcg_gameplayercard WHERE gameplayer_id = ".$winningPlayer;
			$newpos = myqu($sql);
			$newpos = intval($newpos[0]['newpos']);
			
			if(sizeof($p1cards) > 0)
			{
				//Assign cards in pool to winning player
				for($i=0; $i<sizeof($p1cards); $i++)
				{
					if($activePlayer == '1')
					{
						$sql = "UPDATE mytcg_gameplayercard SET
								gameplayer_id = ".$winningPlayer.",
								pos = ".$newpos.",
								gameplayercardstatus_id = 1
								WHERE gameplayercard_id=".$p2cards[$i]['gameplayercard_id'];
						myqu($sql);
						$newpos++;
						$sql = "UPDATE mytcg_gameplayercard SET
								gameplayer_id = ".$winningPlayer.",
								pos = ".$newpos.",
								gameplayercardstatus_id = 1
								WHERE gameplayercard_id=".$p1cards[$i]['gameplayercard_id'];
						myqu($sql);
						$newpos++;
					}
					else
					{
						$sql = "UPDATE mytcg_gameplayercard SET
								gameplayer_id = ".$winningPlayer.",
								pos = ".$newpos.",
								gameplayercardstatus_id = 1
								WHERE gameplayercard_id=".$p1cards[$i]['gameplayercard_id'];
						myqu($sql);
						$newpos++;
						$sql = "UPDATE mytcg_gameplayercard SET
								gameplayer_id = ".$winningPlayer.",
								pos = ".$newpos.",
								gameplayercardstatus_id = 1
								WHERE gameplayercard_id=".$p2cards[$i]['gameplayercard_id'];
						myqu($sql);
						$newpos++;
					}
				}
			}
			//Assign cards in play to winner
			if($winner == '1')
			{
				//player1 won
				$sql = "UPDATE mytcg_gameplayercard SET
						gameplayer_id = ".$winningPlayer.",
						pos = ".$newpos."
						WHERE gameplayercard_id=".$players[2]['topcard'];
				myqu($sql);
				$newpos++;
				$sql = "UPDATE mytcg_gameplayercard SET
						gameplayer_id = ".$winningPlayer.",
						pos = ".$newpos."
						WHERE gameplayercard_id=".$players[1]['topcard'];
				myqu($sql);
				$newpos++;
			}
			elseif($winner == '2')
			{
				//player2 won
				$sql = "UPDATE mytcg_gameplayercard SET
						gameplayer_id = ".$winningPlayer.",
						pos = ".$newpos."
						WHERE gameplayercard_id=".$players[1]['topcard'];
				myqu($sql);
				$newpos++;
				$sql = "UPDATE mytcg_gameplayercard SET
						gameplayer_id = ".$winningPlayer.",
						pos = ".$newpos."
						WHERE gameplayercard_id=".$players[2]['topcard'];
				myqu($sql);
				$newpos++;
			}
			//Update active player
			$sql = "UPDATE mytcg_gameplayer SET is_active = 1 WHERE gameplayer_id = ".$winningPlayer;
			myqu($sql);
			$sql = "UPDATE mytcg_gameplayer SET is_active = 0 WHERE gameplayer_id = ".$losingPlayer;
			myqu($sql);
		}
		else
		{
			//draw - update top cards to pending status to add them to the draw pool
			$sql = "UPDATE mytcg_gameplayercard SET
					gameplayercardstatus_id = 2
					WHERE gameplayercard_id = ".$players[1]['topcard'];
			myqu($sql);
			$sql = "UPDATE mytcg_gameplayercard SET
					gameplayercardstatus_id = 2
					WHERE gameplayercard_id = ".$players[2]['topcard'];
			myqu($sql);
		}
		
		//add game log
		$sql = "INSERT INTO mytcg_gamelog (game_id, date, categorystat_id, message, winner)
				VALUES (".$gameId.", NOW(), ".$statId.", '".$explanation."', ".$winner.");";
		myqu($sql);
		
		//check if it is game over
		$sql = "SELECT COUNT(*) AS 'cards' 
				FROM {$pre}_gameplayer GP
				JOIN {$pre}_gameplayercard GPC USING (gameplayer_id)
				WHERE GP.gameplayer_id=".$players[1]['gameplayer_id'];
		$cardsLeft = myqu($sql);
		$p1score = $cardsLeft[0]['cards'];
		$sql = "SELECT COUNT(*) AS 'cards' 
				FROM {$pre}_gameplayer GP
				JOIN {$pre}_gameplayercard GPC USING (gameplayer_id)
				WHERE GP.gameplayer_id=".$players[2]['gameplayer_id'];
		$cardsLeft = myqu($sql);
		$p2score = $cardsLeft[0]['cards'];
		$creditsWon = 0;
		if($p1score==0 || $p2score==0)
		{
			$gameStatus = "complete";
			$message = $players[intval($winner)]['username']." wins! ";
			//update game status and phase to indicate it game over
			$sql = "UPDATE {$pre}_game SET gamestatus_id=2, gamephase_id=3 WHERE game_id=".$gameId;
			myqu($sql);
			//give credits if the user is the winner
			if($p1score > 0){
				$creditsWon = awardCredits($pre, $userID, $gameId, $deckSize);
				if($creditsWon > 0){
					$message.= $players[1]['username']." received ".$creditsWon." TCG credits for winning.";
				}
				else{
					$message.= $players[1]['username']." already won 3 games today and was just playing for fun.";
				}
			}
			//add game over log
			$sql = "INSERT INTO mytcg_gamelog (game_id, date, categorystat_id, message)
					VALUES (".$gameId.", NOW(), 0, '".$message."');";
			myqu($sql);
		}
		
		//get last log entry
		$sql = "SELECT MAX(gamelog_id) AS 'last' 
				FROM mytcg_gamelog
				WHERE game_id=".$gameId;
		$log = myqu($sql);
		$logId = '0';
		if(!is_null($log[0]['last'])){
			$logId = $log[0]['last'];
		}
		
		//number of moves made in the game so far
		$gameMoves = myqu("SELECT COUNT(*) AS 'moves' FROM mytcg_gamelog WHERE game_id=".$gameId." GROUP BY game_id");
		$gameMoves = $gameMoves[0]['moves'];
		
		//return xml
		echo '<game>'.$sCRLF;
		echo $sTab.'<status val="'.$gameStatus.'" />'.$sCRLF;
		echo $sTab.'<log val="'.$logId.'" />'.$sCRLF;
		echo $sTab.'<moves val="'.$gameMoves.'" />'.$sCRLF;
		echo $sTab.'<message val="'.$explanation.'" />'.$sCRLF;
		echo $sTab.'<winner val="'.$winner.'" />'.$sCRLF;
		echo $sTab.'<p2card val="'.$players[2]['card_id'].'" />'.$sCRLF; //opponent's card for this round (must still be shown)
		echo $sTab.'<stat val="'.($statId-1).'" />'.$sCRLF;
		echo $sTab.'<creditswon val="'.$creditsWon.'" />'.$sCRLF;
		echo '</game>';
		exit;
	}
}


/*
 * Next Round
 */
if(isset($_GET['nextround']))
{
	//get details of game for next round
	$gameStatus = 'incomplete';
	$gameId = $_GET['game'];
	
	if($gameId=='0') exit;
	
	//get game status active player and both players' next cards
	$sql = "SELECT nextround.*, GS.description AS 'status', COUNT(*) AS 'score' FROM
			(
				SELECT GP.game_id, GP.gameplayer_id, GP.is_active, GP.user_id, UC.card_id AS nextcard
				FROM mytcg_gameplayer GP
				JOIN mytcg_gameplayercard GPC USING (gameplayer_id)
				JOIN mytcg_usercard UC USING (usercard_id)
				WHERE GP.game_id={$gameId}
				AND GPC.gameplayercardstatus_id=1
				ORDER BY GPC.pos ASC
			) nextround
			JOIN mytcg_game GA USING (game_id)
			JOIN mytcg_gamestatus GS USING (gamestatus_id)
			GROUP BY gameplayer_id
			ORDER BY gameplayer_id ASC";
	$nextRound = myqu($sql);
	
	$activePlayer = '0';
	$p1card = '0';
	$p2card = '0';
	$p1score = '0';
	$p2score = '0';
	
	//return xml
	echo '<nextround>'.$sCRLF;
	if(sizeof($nextRound)>0)
	{
		echo $sTab.'<status val="'.$nextRound[0]['status'].'" />'.$sCRLF;
		foreach($nextRound as $player){
			if($player['user_id']==$userID){
				//this user is player 1
				if($player['is_active']=='1'){
					$activePlayer = '1';
				}
				$p1card = $player['nextcard'];
				$p1score = $player['score'];
			}
			else{
				//opponent is player 2
				if($player['is_active']=='1'){
					$activePlayer = '2';
				}
				$p2card = $player['nextcard'];
				$p2score = $player['score'];
			}
		}
		//hide opponent's card if the user is the active player
		$p2card = ($activePlayer=='2') ? $p2card : '0';
	}
	echo $sTab.'<p1score val="'.$p1score.'" />'.$sCRLF;
	echo $sTab.'<p2score val="'.$p2score.'" />'.$sCRLF;
	echo $sTab.'<p1card val="'.$p1card.'" />'.$sCRLF;
	echo $sTab.'<p2card val="'.$p2card.'" />'.$sCRLF;
	echo $sTab.'<activeplayer val="'.$activePlayer.'" />'.$sCRLF;
	echo '</nextround>';
	exit;
	/*
		//get player1's next card
		$sql = "SELECT GPC.*, UC.card_id
				FROM mytcg_gameplayercard GPC
				JOIN mytcg_usercard UC USING (usercard_id)
				WHERE GPC.gameplayer_id=".$p1."
				AND GPC.gameplayercardstatus_id=1
				ORDER BY GPC.pos ASC;";
		$cards = myqu($sql);
		if(sizeof($cards) > 0){
			$gameCards = array();
			foreach($cards as $card){
				$gameCards[] = $card['card_id'];
			}
			$p1score = count($gameCards);
			$p1card = $gameCards[0];
		}
		else{
			$p1score = '0';
			$p1card = '0';
		}
		//get player2's next card
		$sql = "SELECT GPC.*, UC.card_id
				FROM mytcg_gameplayercard GPC
				JOIN mytcg_usercard UC USING (usercard_id)
				WHERE GPC.gameplayer_id=".$p2."
				AND GPC.gameplayercardstatus_id=1
				ORDER BY GPC.pos ASC;";
		$cards = myqu($sql);
		if(sizeof($cards) > 0){
			$gameCards = array();
			foreach($cards as $card){
				$gameCards[] = $card['card_id'];
			}
			$p2score = count($gameCards);
			$p2card = $gameCards[0];
		}
		else{
			$p2score = '0';
			$p2card = '0';
		}
	 * */
}


/*
 * Game Logs
 */
if(isset($_GET['gamelog']))
{
	$gameId = $_GET['game'];
	$sql = "SELECT * FROM mytcg_gamelog WHERE game_id={$gameId} ORDER BY gamelog_id DESC";
	$gameLogs = myqu($sql);
	$logs = array();
	if(sizeof($gameLogs)>0){
		foreach($gameLogs as $log){
			$logs[] = $log['message'];
		}
	}
	print_r($logs);
	exit;
}


/*
 * Award credits for win
 */
function awardCredits($pre, $userID, $gameId, $deckSize)
{
	$creditsAwarded = 0;
	//get user's credits and gameswon
	$sql = "SELECT credits, gameswon FROM ".$pre."_user WHERE user_id = ".$userID;
	$userData = myqu($sql);
	$userCredits = intval($userData[0]['credits']);
	$gamesWon = intval($userData[0]['gameswon']);
	//check if user has not won more than 3 games today
	if($gamesWon < 3)
	{
		//check if the game was forfeited - if so the user must have 75% or more of the total cards
		//(e.g. 15 or more cards where each player started with 10 cards, 20 total)
		$sql = "SELECT categorystat_id AS lastmove
				FROM ".$pre."_gamelog 
				WHERE game_id=".$gameId."
				ORDER BY gamelog_id DESC
				LIMIT 1";
		$gameQuery = myqu($sql);
		$lastMove = $gameQuery[0]['lastmove'];
		$forfeitFlag = 1;
		if($lastMove == '-1')
		{
			//check how many cards the winning player has
			$sql = "SELECT COUNT(*) AS cardscount
					FROM ".$pre."_gameplayercard 
					JOIN ".$pre."_gameplayer USING (gameplayer_id)
					WHERE game_id=".$gameId."
					AND user_id=".$userID;
			$cardsQuery = myqu($sql);
			$cardsCount = intval($cardsQuery[0]['cardscount']);
			if($cardsCount < ($deckSize*2)*0.75)
			{
				$forfeitFlag = 0;
			}
		}
		
		//get opponent details
		$sql = "SELECT GA.gamedifficulty_id, GP.gameplayer_id, U.user_id, U.username
				FROM mytcg_game GA
				JOIN mytcg_gameplayer GP USING(game_id)
				JOIN mytcg_user U USING(user_id)
				WHERE GP.game_id={$gameId}
				AND user_id!={$userID}";
		$gameQuery = myqu($sql);
		$difficulty = $gameQuery[0]['gamedifficulty_id'];
		$opponentName = $gameQuery[0]['username'];
		$opponentId = $gameQuery[0]['user_id'];
		
		//check how many credits the user gets
		//$creditsAwarded = 25;
		if($opponentId == '1'){
			switch($difficulty){
				case '1':
					$creditsAwarded = 20;
				break;
				case '2':
					$creditsAwarded = 25;
				break;
				case '3':
					$creditsAwarded = 30;
				break;
			}
		}
		$creditsAwarded *= $forfeitFlag;
		
		if($creditsAwarded > 0){
			//award user with credits for winning the game
			$userCredits += $creditsAwarded;
			myqu("UPDATE {$pre}_user SET credits = (".$userCredits.") WHERE user_id=".$userID);
			//add transaction log
			myqu("INSERT INTO {$pre}_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Received ".$creditsAwarded." credits for beating ".$opponentName."', NOW(), ".$creditsAwarded.")");
			//update gameswon
			myqu("UPDATE mytcg_user SET gameswon = (gameswon+1) WHERE user_id=".$userID);
		}
	}
	//return amount of credits awarded
	return $creditsAwarded;
}


/*
 * Retrieve all cards for category
 */
if (intval($_GET['cards'])==1)
{
	//get all cards for specified category
	$categoryId = $_GET['category'];
	
	$sql = "SELECT C.*, IMGS.description AS 'imageserver'
			FROM ".$pre."_card C
			JOIN ".$pre."_imageserver IMGS ON C.back_imageserver_id = IMGS.imageserver_id
			JOIN ".$pre."_category CA USING (category_id)
			WHERE CA.parent_id = ".$categoryId.";";
	$cards = myqu($sql);
	$allstats = array();
	
	echo '<cards>'.$sCRLF;
	echo $sTab.'<cardcount val="'.count($cards).'" />'.$sCRLF;
	if(count($cards) > 0)
	{
		$i = 0;
		foreach($cards as $card)
		{
			$sql = "SELECT CS.description AS 'stattext', CS.statvalue, CCS.description AS 'category', CS.top, CS.width, CS.height
					FROM mytcg_cardstat CS
					JOIN mytcg_categorystat CCS USING (categorystat_id)
					WHERE CS.card_id = ".$card['card_id'].";";
			$cardstats = myqu($sql);
					
			echo $sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<cardid val="'.$card['card_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<imageserver val="'.$card['imageserver'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<fullimage val="'.$card['imageserver'].'cards/'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<stats>'.$sCRLF;
			//card stats
			if(sizeof($allstats) == 0){
				$allstats['width'] = $cardstats[0]['width'];
				$allstats['height'] = $cardstats[0]['height'];
				$allstats['count'] = count($cardstats);
			}				
			if(count($cardstats) > 0)
			{
				$s = 0;
				foreach($cardstats as $stat)
				{
					echo $sTab.$sTab.$sTab.'<stat_'.$s.'>'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.'<description val="'.$stat['stattext'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.'<statvalue val="'.$stat['statvalue'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.'<category val="'.$stat['category'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.'</stat_'.$s.'>'.$sCRLF;
					if(sizeof($allstats['top']) < $allstats['count']){
						$allstats['top'][$s] = $stat['top'];
					}
					$s++;
				}
			}
			echo $sTab.$sTab.'</stats>'.$sCRLF;
			echo $sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	//card stats
	echo $sTab.'<allstats>'.$sCRLF;
	echo $sTab.$sTab.'<statscount val="'.$allstats['count'].'" />'.$sCRLF;
	echo $sTab.$sTab.'<width val="'.$allstats['width'].'" />'.$sCRLF;
	echo $sTab.$sTab.'<height val="'.$allstats['height'].'" />'.$sCRLF;
	for($i=0; $i<$allstats['count']; $i++){
		echo $sTab.$sTab.'<top_'.$i.' val="'.$allstats['top'][$i].'" />'.$sCRLF;
	}
	echo $sTab.'</allstats>'.$sCRLF;
	echo '</cards>'.$sCRLF;
	
}


/*
 * Get saved games
 */
if (isset($_GET['savedgames']))
{
	$sql = "SELECT *
			FROM mytcg_gameplayer GP
			JOIN mytcg_game GA USING (game_id)
			JOIN mytcg_category CA USING (category_id)
			WHERE GP.user_id = ".$userID."
			AND GA.gamestatus_id = 1
			ORDER BY GA.date_start DESC;";
	$savedGames = myqu($sql);
	//print_r($savedGames);exit;
	echo '<games>'.$sCRLF;
	echo $sTab.'<count val="'.sizeof($savedGames).'" />'.$sCRLF;
	if(sizeof($savedGames) > 0)
	{
		$i = 0;
		foreach($savedGames as $game)
		{
			//get players data
			$sql = "SELECT GP.user_id, U.username, COUNT(*) AS 'score', GD.description AS 'difficulty'
					FROM mytcg_gameplayer GP
					JOIN mytcg_user U USING (user_id)
					LEFT JOIN mytcg_gameplayercard GPC USING(gameplayer_id)
					LEFT JOIN mytcg_game GA USING (game_id)
					LEFT JOIN mytcg_gamedifficulty GD USING(gamedifficulty_id)
					WHERE GP.game_id = ".$game['game_id']." 
					AND GPC.gameplayercardstatus_id = 1
					GROUP BY GP.user_id";
			$players = myqu($sql);
			$gameover = '0';
			if(sizeof($players) > 1)
			{
				foreach($players as $player)
				{
					if($player['user_id'] == '1'){
						$opptype = "0";
						$difficulty = (!is_null($player['difficulty']) && strlen($player['difficulty'])>0) ? $player['difficulty'] : 'Easy';
						$oppname = "Computer [".$difficulty.']';
						$score['opponent'] = $player['score'];
					}
					else{
						if($player['user_id'] == $userID){
							$score['user'] = $player['score'];
						}
						else
						{
							$opptype = "1";
							$oppname = $player['username'];
							$score['opponent'] = $player['score'];
						}
					}
				}
			}
			elseif(sizeof($players) > 0)
			{
				$gameover = '1';
				$player = $players[0];
				if($player['user_id'] == '1'){
					$opptype = "0";
					$difficulty = (!is_null($player['difficulty']) && strlen($player['difficulty'])>0) ? $player['difficulty'] : 'Easy';
					$oppname = "Computer [".$difficulty.']';
					$score['opponent'] = $player['score'];
					$score['user'] = 0;
				}
				elseif($player['user_id'] == $userID)
				{
					$score['user'] = $player['score'];
					$score['opponent'] = 0;
				}
				else
				{
					$opptype = "1";
					$oppname = $player['username'];
					$score['opponent'] = $player['score'];
					$score['user'] = 0;
				}
			}
			$score['draw'] = 20 - (intval($score['opponent']) + intval($score['user']));
			
			
			echo $sTab.'<game_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<game_id val="'.$game['game_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<category_id val="'.$game['category_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<category val="'.$game['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<date_start val="'.$game['date_start'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<active val="'.$game['is_active'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<score val="'.$score['user'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<opponent>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<type val="'.$opptype.'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<name val="'.$oppname.'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<score val="'.$score['opponent'].'" />'.$sCRLF;
			echo $sTab.$sTab.'</opponent>'.$sCRLF;
			echo $sTab.$sTab.'<draw val="'.$score['draw'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<over val="'.$gameover.'" />'.$sCRLF;
			echo $sTab.'</game_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo '</games>';
}


/*
 * Search for friend
 */
if(isset($_GET['search']))
{
	$searchString = $_GET['friend'];
	$sql = "SELECT * FROM mytcg_user WHERE username LIKE '%".$searchString."%' AND user_id != ".$userID;
	$searchResults = myqu($sql);
	//print_r($searchResults);
	echo '<search>'.$sCRLF;
	echo $sTab.'<found val="'.sizeof($searchResults).'" />'.$sCRLF;
	if(sizeof($searchResults) > 0)
	{
		echo $sTab.'<results>'.$sCRLF;
		//found result(s)
		$i = 0;
		foreach($searchResults as $result)
		{
			echo $sTab.$sTab.'<result_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<user_id val="'.$result['user_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<username val="'.$result['username'].'" />'.$sCRLF;
			echo $sTab.$sTab.'</result_'.$i.'>'.$sCRLF;
			$i++;
		}
		echo $sTab.'</results>'.$sCRLF;
	}
	echo '</search>';
}


/*
 * Friend game setup
 */
if (isset($_GET['friendgame']))
{
	$friend = $_GET['username'];
	$categoryId = $_GET['category'];
	$deckId = $_GET['deck'];
	
	//check if friend is looking for this user
	$sql = "SELECT GA.*, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(GA.date_start)) AS 'online', GP.user_id, GP.deck_id, U.username
			FROM mytcg_game GA
			LEFT JOIN mytcg_gameplayer GP USING(game_id)
			LEFT JOIN mytcg_user U USING (user_id)
			WHERE GA.gamestatus_id=99 AND GA.friend = '".$_SESSION['user']['username']."'
			ORDER BY GA.game_id DESC
			LIMIT 1;";
	$friendGames = myqu($sql);
	
	echo '<result>'.$sCRLF;
	if(sizeof($friendGames) > 0)
	{
		//found friend looking for this user also
		
		foreach($friendGames as $friendGame)
		{
			//check if the game is fresh enough
			$gameId = $friendGame['game_id'];
			if(intval($friendGame['online']) < 60)
			{
				if($friend == $friendGame['username']){
					//join the game
					//add this user as a player to the game
					myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, deck_id, is_active, gameplayerstatus_id)
						VALUES ('.$gameId.', '.$userID.', '.$deckId.', 0, 2)'); //gameplayerstatus_id 2 is waiting, so they need wait for the other player to make the move
					
					//we need to get this user's gameplayer_id
					$userPlayerIdQuery = myqu('SELECT gameplayer_id 
						FROM mytcg_gameplayer 
						WHERE user_id = '.$userID
						.' AND game_id = '.$gameId);
					$userPlayerId = $userPlayerIdQuery[0]['gameplayer_id'];
					
					echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
					echo $sTab.'<status val="ready" />'.$sCRLF;
					echo $sTab.'<opponent>'.$sCRLF;
					echo $sTab.$sTab.'<username val="'.$friend.'" />'.$sCRLF;
					echo $sTab.$sTab.'<deckranking val="'.getDeckRanking($friendGame['user_id'],$friendGame['deck_id']).'" />'.$sCRLF;
					echo $sTab.'</opponent>'.$sCRLF;
					echo '</result>'.$sCRLF;
					exit;
				}
			}
			else
			{
				//delete the game and its players
				myqu("DELETE FROM mytcg_gameplayer WHERE game_id = ".$gameId);
				myqu("DELETE FROM mytcg_game WHERE game_id = ".$gameId);
			}
		}
		//continue on to create a new open game with friend
	}
	else
	{
		//no friends looking for this user
		//check if this user has any open games created with the friend
		
		$sql = "SELECT GA.game_id, GA.gamephase_id, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(GA.date_start)) AS 'online', GA.category_id, COUNT(*) AS 'players', GP.user_id, GP.deck_id, U.username, GA.friend
				FROM ".$pre."_game GA
				LEFT JOIN ".$pre."_gameplayer GP USING (game_id)
				LEFT JOIN ".$pre."_user U USING(user_id)
				WHERE GA.category_id = ".$categoryId." AND GA.gamestatus_id=99 
				AND (GA.friend != '' AND GA.friend = '".$friend."')
				GROUP BY GA.game_id;";
		$openGames = myqu($sql);
		
		//print_r($openGames);exit;
		
		if(sizeof($openGames) > 0)
		{
			foreach($openGames as $openGame)
			{
				if($openGame['players'] == '2')
				{
					//both players have joined, game is ready to be played
					
					$gameId = $openGame['game_id'];
					
					//get opponent's details
					$oppQuery = myqu("SELECT user_id, deck_id FROM mytcg_gameplayer WHERE game_id=".$gameId." AND user_id != ".$userID);
					$oppUserId = $oppQuery[0]['user_id'];
					$oppDeckId = $oppQuery[0]['deck_id'];
				
					echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
					echo $sTab.'<status val="ready" />'.$sCRLF;
					echo $sTab.'<opponent>'.$sCRLF;
					echo $sTab.$sTab.'<username val="'.$friend.'" />'.$sCRLF;
					echo $sTab.$sTab.'<deckranking val="'.getDeckRanking($oppUserId,$oppDeckId).'" />'.$sCRLF;
					echo $sTab.'</opponent>'.$sCRLF;
					echo '</result>'.$sCRLF;
					exit;
				}
				else
				{
					//the other player has not joined yet
					if($openGame['user_id'] == $userID)
					{
						//this is the user's game
						$gameId = $openGame['game_id'];
						
						//check if friend has declined invitation
						if($openGame['gamephase_id'] == '5')
						{
							//friend declined invitation
							//close the game
							myqu("UPDATE mytcg_game SET gamestatus_id = 2 WHERE game_id = ".$gameId);
							
							echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
							echo $sTab.'<status val="declined" />'.$sCRLF;
							echo '</result>'.$sCRLF;
							exit;
						}
						else
						{
							//still waiting for friend to join
							//update date of game
							myqu("UPDATE mytcg_game SET date_start = now() WHERE game_id = ".$gameId);
							//update deck of player (in case it changed)
							myqu("UPDATE mytcg_gameplayer SET deck_id=".$deckId." WHERE user_id=".$userID." AND game_id=".$gameId);
							
							echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
							echo $sTab.'<status val="waiting" />'.$sCRLF;
							echo '</result>'.$sCRLF;
							exit;
						}
					}
				}
			}
		}
	}
	
	//create a new open game specifying friend, get the game_id
	$gameIdQuery = myqu('SELECT (CASE WHEN MAX(game_id) IS NULL THEN 0 ELSE MAX(game_id) END) + 1 AS game_id FROM mytcg_game');
	$gameId = $gameIdQuery[0]['game_id'];
	myqu('INSERT INTO mytcg_game (game_id, gamestatus_id, gamephase_id, category_id, date_start, friend) 
		SELECT '.$gameId.', (SELECT gamestatus_id FROM mytcg_gamestatus WHERE lower(description) = "open"),
		(SELECT gamephase_id FROM mytcg_gamephase WHERE lower(description) = "lfm"), '.$categoryId.', now(), \''.$friend.'\'
		FROM DUAL');
	
	//add this user as a player to the game
	myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, deck_id, is_active, gameplayerstatus_id)
		VALUES ('.$gameId.', '.$userID.', '.$deckId.', 1, 1)'); //gameplayerstatus_id 1 is waiting, so they need to make the move
	
	echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
	echo $sTab.'<status val="waiting" />'.$sCRLF;
	echo '</result>'.$sCRLF;
		
}


function getDeckRanking($user, $deck)
{
	//get player's deck ranking
	if($deck == '-1'){
		$sql = "SELECT SUM(ranking) AS 'deckranking' FROM
				(
					SELECT C.card_id, UC.usercard_id, C.description, C.ranking 
					FROM mytcg_usercard UC
					JOIN mytcg_card C USING (card_id)
					WHERE UC.user_id=".$user."
					GROUP BY C.card_id
					ORDER BY C.ranking DESC
					LIMIT 10
				)
				tmp";
	}
	else{
		$sql = "SELECT SUM(ranking) AS 'deckranking' FROM
				(
					SELECT C.card_id, UC.usercard_id, C.description, C.ranking 
					FROM mytcg_usercard UC
					JOIN mytcg_card C USING (card_id)
					WHERE UC.user_id=".$user." AND deck_id=".$deck."
					GROUP BY C.card_id
					ORDER BY C.ranking DESC
					LIMIT 10
				)
				tmp";
	}
	$result = myqu($sql);
	return $result[0]['deckranking'];
}


/*
 * Accept invitation to multiplayer game
 */
if (isset($_GET['accept']))
{
	$deckId = $_GET['deck'];
	$gameId = $_GET['game'];
	
	//join the invited game
	//add this user as a player to the game
	myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, deck_id, is_active, gameplayerstatus_id)
		VALUES ('.$gameId.', '.$userID.', '.$deckId.', 0, 2)'); //gameplayerstatus_id 2 is waiting, so they need wait for the other player to make the move
	
	//get the opponent's details
	$oppPlayerQuery = myqu('SELECT user_id, deck_id, username FROM mytcg_gameplayer JOIN mytcg_user USING (user_id) WHERE user_id != '.$userID.' AND game_id = '.$gameId);
	$oppUserId = $oppPlayerQuery[0]['user_id'];
	$oppDeckId = $oppPlayerQuery[0]['deck_id'];
	$oppUsername = $oppPlayerQuery[0]['username'];
	
	echo '<result>'.$sCRLF;
	echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
	echo $sTab.'<status val="ready" />'.$sCRLF;
	echo $sTab.'<opponent>'.$sCRLF;
	echo $sTab.$sTab.'<userid val="'.$oppUserId.'" />'.$sCRLF;
	echo $sTab.$sTab.'<username val="'.$oppUsername.'" />'.$sCRLF;
	echo $sTab.$sTab.'<deckranking val="'.getDeckRanking($oppUserId,$oppDeckId).'" />'.$sCRLF;
	echo $sTab.'</opponent>'.$sCRLF;
	echo '</result>'.$sCRLF;
	exit;
}


/*
 * Decline invitation to multiplayer game
 */
if (isset($_GET['decline']))
{
	$gameId = $_GET['game'];
	
	//decline invitation - close game
	myqu("UPDATE mytcg_game SET gamephase_id=5 WHERE game_id=".$gameId);
	
	echo '<result>'.$sCRLF;
	echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
	echo $sTab.'<status val="declined" />'.$sCRLF;
	echo '</result>'.$sCRLF;
	exit;
}


/*
 * Multiplayer game setup
 */
if (isset($_GET['onlinegame']))
{
	$categoryId = $_GET['category'];
	$deckId = $_GET['deck'];
	
	echo '<result>'.$sCRLF;
	
	//check if a friend is looking for this user
	$sql = "SELECT GA.*, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(GA.date_start)) AS 'online', COUNT(*) AS 'players', GP.user_id, GP.deck_id, U.username
			FROM mytcg_game GA
			JOIN mytcg_gameplayer GP USING(game_id)
			JOIN mytcg_user U USING (user_id)
			WHERE GA.gamestatus_id=99 AND GA.friend = '".$_SESSION['user']['username']."' AND GA.gamephase_id != 5
			GROUP BY GA.game_id
			HAVING players < 2 
			ORDER BY GA.game_id DESC
			LIMIT 1;";
	$friendGames = myqu($sql);
	
	if(sizeof($friendGames) > 0)
	{
		$gameId = $friendGames[0]['game_id'];
		if($friendGames[0]['online'] <= 5)
		{
			//inform the user of invitation to play from another user
			echo $sTab.'<game val="'.$friendGames[0]['game_id'].'" />'.$sCRLF;
			echo $sTab.'<status val="invite" />'.$sCRLF;
			echo $sTab.'<opponent>'.$sCRLF;
			echo $sTab.$sTab.'<username val="'.$friendGames[0]['username'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<deckranking val="'.getDeckRanking($friendGames[0]['user_id'], $friendGames[0]['deck_id']).'" />'.$sCRLF;
			echo $sTab.'</opponent>'.$sCRLF;
			echo '</result>'.$sCRLF;
			exit;
		}
		else
		{
			//delete the old open game and its players
			myqu("DELETE FROM mytcg_gameplayer WHERE game_id = ".$gameId);
			myqu("DELETE FROM mytcg_game WHERE game_id = ".$gameId);
		}
	}
	
	
	//an open game is a game with only 1 player
	$sql = "SELECT GA.game_id, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(GA.date_start)) AS 'online', GA.category_id, COUNT(*) AS 'players', GP.user_id, GP.deck_id, U.username
			FROM ".$pre."_game GA
			LEFT JOIN ".$pre."_gameplayer GP USING (game_id)
			LEFT JOIN ".$pre."_user U USING(user_id)
			WHERE GA.category_id = ".$categoryId." AND GA.friend = ''
			GROUP BY GA.game_id
			HAVING players < 2;";
	$openGames = myqu($sql);
	
	//check if there are any open games
	if(sizeof($openGames) > 0)
	{
		//open game found
		//check if it is this user's game
		if($openGames[0]['user_id'] == $userID)
		{
			//this is the user's game, waiting for a player to join
			//update date of game
			
			$gameId = $openGames[0]['game_id'];
			
			myqu("UPDATE mytcg_game SET date_start = now() WHERE game_id = ".$gameId);
			
			echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
			echo $sTab.'<status val="waiting" />'.$sCRLF;
		}
		else
		{
			//check how recent the game is
			
			if(intval($openGames[0]['online']) <= 5)
			{
				//open game available to be joined
				
				$gameId = $openGames[0]['game_id'];
				
				//*****************
				//***BLACK MAGIC for joining the open game
				//*****************
				
				//add this user as a player to the game
				myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, deck_id, is_active, gameplayerstatus_id)
					VALUES ('.$gameId.', '.$userID.', '.$deckId.', 0, 2)'); //gameplayerstatus_id 2 is waiting, so they need wait for the other player to make the move
				
				//update the time
				myqu("UPDATE mytcg_game SET date_start = now() WHERE game_id = ".$gameId);
				
				echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
				echo $sTab.'<status val="ready" />'.$sCRLF;
				echo $sTab.'<opponent>'.$sCRLF;
				echo $sTab.$sTab.'<userid val="'.$openGames[0]['user_id'].'" />'.$sCRLF;
				echo $sTab.$sTab.'<username val="'.$openGames[0]['username'].'" />'.$sCRLF;
				echo $sTab.$sTab.'<deckranking val="'.getDeckRanking($openGames[0]['user_id'],$openGames[0]['deck_id']).'" />'.$sCRLF;
				echo $sTab.'</opponent>'.$sCRLF;
			}
			else
			{
				//open game too old, assume player not online anymore
				//delete the open game - create a new one
				
				$gameId = $openGames[0]['game_id'];
				myqu("DELETE FROM mytcg_gameplayer WHERE game_id=".$gameId);
				myqu("DELETE FROM mytcg_game WHERE game_id=".$gameId);
				
				//create a new open game, get the game_id
				$gameIdQuery = myqu('SELECT (CASE WHEN MAX(game_id) IS NULL THEN 0 ELSE MAX(game_id) END) + 1 AS game_id FROM mytcg_game');
				$gameId = $gameIdQuery[0]['game_id'];
				myqu('INSERT INTO mytcg_game (game_id, gamestatus_id, gamephase_id, category_id, date_start) 
					SELECT '.$gameId.', (SELECT gamestatus_id FROM mytcg_gamestatus WHERE lower(description) = "open"),
					(SELECT gamephase_id FROM mytcg_gamephase WHERE lower(description) = "lfm"), '.$categoryId.', now()
					FROM DUAL');
				
				//add this user as a player to the game
				myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, deck_id, is_active, gameplayerstatus_id)
					VALUES ('.$gameId.', '.$userID.', '.$deckId.', 1, 1)'); //gameplayerstatus_id 1 is waiting, so they need to make the move
				
				echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
				echo $sTab.'<status val="waiting" />'.$sCRLF;
			}
		}
	}
	else
	{
		//No recent open games with only 1 player found
		
		$sql = "SELECT *, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(GA.date_start)) AS 'online'
				FROM mytcg_game GA
				JOIN mytcg_gameplayer GP USING(game_id)
				WHERE GA.gamestatus_id = 99
				AND GP.user_id = ".$userID.";";
		$readyGame = myqu($sql);
		
		//check if an open game is ready to be played
		if(sizeof($readyGame) > 0)
		{
			//open game found
			$gameId = $readyGame[0]['game_id'];
			
			//check if it is new enough
			if($readyGame[0]['online'] <= 5)
			{
				//game still fresh
				$sql = "SELECT *
						FROM mytcg_game GA
						LEFT JOIN mytcg_gameplayer GP USING (game_id)
						JOIN mytcg_user U USING (user_id)
						WHERE GA.game_id = ".$gameId."
						AND GP.user_id != ".$userID.";";
				$gameOpponent = myqu($sql);
				
				if(sizeof($gameOpponent) < 1){
					//update the time
					myqu("UPDATE mytcg_game SET date_start = now() WHERE game_id = ".$gameId);
					//still waiting
					echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
					echo $sTab.'<status val="waiting" />'.$sCRLF;
					exit;
				}
				else{
					//ready to be played
					echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
					echo $sTab.'<status val="ready" />'.$sCRLF;
					echo $sTab.'<opponent>'.$sCRLF;
					echo $sTab.$sTab.'<userid val="'.$gameOpponent[0]['user_id'].'" />'.$sCRLF;
					echo $sTab.$sTab.'<username val="'.$gameOpponent[0]['username'].'" />'.$sCRLF;
					echo $sTab.$sTab.'<deckranking val="'.getDeckRanking($gameOpponent[0]['user_id'],$gameOpponent[0]['deck_id']).'" />'.$sCRLF;
					echo $sTab.'</opponent>'.$sCRLF;
				}
			}
			else
			{
				//game too old - remove other old player
				$sql = "DELETE FROM mytcg_gameplayer WHERE game_id=".$gameId." AND user_id != ".$userID;
				myqu($sql);
				//update the time
				myqu("UPDATE mytcg_game SET date_start = now() WHERE game_id = ".$gameId);
				//take first move
				myqu("UPDATE mytcg_gameplayer SET gameplayerstatus_id=1, is_active=1, deck_id=".$deckId." WHERE game_id = ".$gameId." AND user_id=".$userID);
				//still waiting
				echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
				echo $sTab.'<status val="waiting" />'.$sCRLF;
				exit;
			}
		}
		else
		{
			//No open games found
			
			//create a new open game, get the game_id
			$gameIdQuery = myqu('SELECT (CASE WHEN MAX(game_id) IS NULL THEN 0 ELSE MAX(game_id) END) + 1 AS game_id FROM mytcg_game');
			$gameId = $gameIdQuery[0]['game_id'];
			myqu('INSERT INTO mytcg_game (game_id, gamestatus_id, gamephase_id, category_id, date_start) 
				SELECT '.$gameId.', (SELECT gamestatus_id FROM mytcg_gamestatus WHERE lower(description) = "open"),
				(SELECT gamephase_id FROM mytcg_gamephase WHERE lower(description) = "lfm"), '.$categoryId.', now()
				FROM DUAL');
			
			//add this user as a player to the game
			myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, deck_id, is_active, gameplayerstatus_id)
				VALUES ('.$gameId.', '.$userID.', '.$deckId.', 1, 1)'); //gameplayerstatus_id 1 is waiting, so they need to make the move
			
			echo $sTab.'<game val="'.$gameId.'" />'.$sCRLF;
			echo $sTab.'<status val="waiting" />'.$sCRLF;
		}
	}

	echo '</result>';
}


/*
 * Forfeit game
 */
if(isset($_GET['forfeit']))
{
	$gameId = $_GET['game'];
	
	//first check if the opponent has not already forfeited
	$sql = "SELECT gamestatus_id FROM {$pre}_game WHERE game_id={$gameId}";
	$gameQuery = myqu($sql);
	$gameStatus = $gameQuery[0]['gamestatus_id'];
	if($gameStatus == '2')
	{
		//opponent has forfeited the game
		$sql = "SELECT * FROM mytcg_gamelog WHERE game_id={$gameId} ORDER BY gamelog_id DESC";
		$logQuery = myqu($sql);
		$lastMove = $logQuery[0]['categorystat_id'];
		$logMessage = $logQuery[0]['message'];
		
		//give credits if the user is the winner
		$creditsWon = awardCredits($pre, $userID, $gameId, $deckSize);
		
		//return xml
		echo '<game>'.$sCRLF;
		echo $sTab.'<result val="0" />'.$sCRLF;
		echo $sTab.'<message val="'.$message.'" />'.$sCRLF;
		echo $sTab.'<creditswon val="'.$creditsWon.'" />'.$sCRLF;
		echo '</game>';
		exit;
	}
	else
	{
		//forfeit the game
		//get the other player's gameplayer id
		$sql = "SELECT gameplayer_id FROM ".$pre."_gameplayer WHERE game_id=".$gameId." AND user_id=".$userID;
		$playerQuery = myqu($sql);
		$gamePlayerId = $playerQuery[0]['gameplayer_id'];
		
		//place all player cards in draw pool
		$sql = "UPDATE ".$pre."_gameplayercard SET gameplayercardstatus_id=2 WHERE gameplayer_id=".$gamePlayerId;
		myqu($sql);
		
		//set the game to complete
		$sql = "UPDATE ".$pre."_game SET gamestatus_id=2, gamephase_id=3 WHERE game_id=".$gameId;
		myqu($sql);
		
		//add a game log
		$message = $_SESSION['user']['username']." forfeited the game!";
		$sql = "INSERT INTO mytcg_gamelog (game_id, date, categorystat_id, message, winner)
				VALUES (".$gameId.", NOW(), -1, '".$message."', 2);";
		myqu($sql);
		
		echo '<game>'.$sCRLF;
		echo $sTab.'<result val="1" />'.$sCRLF;
		echo $sTab.'<message val="'.$message.'" />'.$sCRLF;
		echo '</game>';
		exit;
	}
}

?>