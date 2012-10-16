<?php
function getAllCatChildren($categoryId,$results) {
	$categories = myqu('SELECT cx.category_child_id
		FROM mytcg_category_x cx
		WHERE cx.category_parent_id = '.$categoryId);
		
	$count = 0;
	while ($category=$categories[$count]) {
		//and repeat for each one
		$results[sizeof($results)] = $category;
		$results = getAllCatChildren($category['category_child_id'], $results);
		$count++;
	}
	
	return $results;
}

function newGame($iUserID, $gameId, $oppLimit=-1) {
	//we need to get both players' gameplayer_id, and the categoryId
	$userPlayerIdQuery = myqu('SELECT gp.gameplayer_id, g.category_id 
		FROM mytcg_gameplayer gp 
		INNER JOIN mytcg_game g 
		ON g.game_id = gp.game_id 
		WHERE gp.user_id = '.$iUserID.' 
		AND gp.game_id = '.$gameId);
	$userPlayerId = $userPlayerIdQuery[0]['gameplayer_id'];
	$categoryId = $userPlayerIdQuery[0]['category_id'];
	$oppPlayerIdQuery = myqu('SELECT gameplayer_id, user_id  
		FROM mytcg_gameplayer 
		WHERE user_id != '.$iUserID
		.' AND game_id = '.$gameId);
	$opponentId = $oppPlayerIdQuery[0]['user_id'];
	$oppPlayerId = $oppPlayerIdQuery[0]['gameplayer_id'];
	
	//create random deck for the players from their available cards.
	//first we will need a list of cards for the players in the category.
	$userCards = array();
	$oppCards = array();

	//we need to get a list of all the child categories of the one given
	$allCategories = array();
	$categories = getAllCatChildren($categoryId, $allCategories);
	$categoryString = $categoryId;
	foreach ($categories as $category) {
		$categoryString.=','.$category['category_child_id'];
	}
		
	$maxCardCopies = 1;
	$userCardsQuery = myqu('SELECT c.card_id, uc.usercard_id
		FROM mytcg_usercard uc
		INNER JOIN mytcg_card c
		ON uc.card_id = c.card_id
		INNER JOIN mytcg_usercardstatus ucs
		ON ucs.usercardstatus_id = uc.usercardstatus_id
		WHERE c.category_id in ('.$categoryString.')
		AND uc.user_id = '.$iUserID.' 
		AND lower(ucs.description) = "album" 
		ORDER BY c.avgranking DESC, c.card_id');
				
	//we just need to make sure the users dont end up with more than 1 of a card in their decks
	$currentCard = 0;
	$cardCount = 0;
	foreach ($userCardsQuery as $card) {
		if ($card['card_id'] != $currentCard) {
			$currentCard = $card['card_id'];
			$cardCount = 0;
		}
		else {
			$cardCount++;
		}
		if ($cardCount < $maxCardCopies) {
			$userCards[sizeof($userCards)] = $card;
		}
	}

		
	$oppLimitString = $oppLimit>-1?' AND c.ranking <= '.$oppLimit.' ':'';
	$oppCardsQuery = myqu('SELECT c.card_id, uc.usercard_id
		FROM mytcg_usercard uc
		INNER JOIN mytcg_card c
		ON uc.card_id = c.card_id
		INNER JOIN mytcg_usercardstatus ucs
		ON ucs.usercardstatus_id = uc.usercardstatus_id
		WHERE c.category_id in ('.$categoryString.')
		AND uc.user_id = '.$opponentId.' 
		AND lower(ucs.description) = "album" '.$oppLimitString.' 
		ORDER BY c.avgranking DESC, c.card_id');
			
	$currentCard = 0;
	$cardCount = 0;
	foreach ($oppCardsQuery as $card) {
		if ($card['card_id'] != $currentCard) {
			$currentCard = $card['card_id'];
			$cardCount = 0;
		}
		else {
			$cardCount++;
		}
		if ($cardCount < $maxCardCopies) {
			$oppCards[sizeof($oppCards)] = $card;
		}
	}
	
	//the standard deck size is 20, but for now I am going to set it to 10, for testing
	//$deckSize = 10;
	$deckSize = sizeof($userCards) > sizeof($oppCards)? sizeof($oppCards):sizeof($userCards);
	$deckSize = $deckSize - ($deckSize % 5);
	$deckSize = $deckSize > 10?10:$deckSize;
	
	$userCards = array_slice($userCards, 0, $deckSize);
	$oppCards = array_slice($oppCards, 0, $deckSize);
	
	shuffle($userCards);
	shuffle($oppCards);
	
	//insert created decks into player cards, all statuses normal
	$pos = 0;
	foreach ($userCards as $card) {
		myqu('INSERT INTO mytcg_gameplayercard 
			(gameplayer_id, usercard_id, gameplayercardstatus_id, pos) 
			SELECT '.$userPlayerId.', '.$card['usercard_id'].', gameplayercardstatus_id, '.$pos.' 
			FROM mytcg_gameplayercardstatus 
			WHERE lower(description) = "normal"');
		$pos++;
	}
	
	$pos = 0;
	foreach ($oppCards as $card) {
		myqu('INSERT INTO mytcg_gameplayercard 
			(gameplayer_id, usercard_id, gameplayercardstatus_id, pos) 
			SELECT '.$oppPlayerId.', '.$card['usercard_id'].', gameplayercardstatus_id, '.$pos.' 
			FROM mytcg_gameplayercardstatus 
			WHERE lower(description) = "normal"');
		$pos++;
	}
}

function chooseStat($cardId, $difficultyId=1, $showCheat=false) {
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

     $cheat.= 'Probabilities
'.print_r($probabilities,true).'=========================='.$sCRLF;

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

function continueGame($gameId, $iUserID) {
	$gamePhaseQuery = myqu('SELECT g.gamephase_id, lower(gp.description) as description
		FROM mytcg_game g
		INNER JOIN mytcg_gamephase gp
		ON g.gamephase_id = gp.gamephase_id
		WHERE g.game_id = '.$gameId);
	$gamePhase = $gamePhaseQuery[0]['description'];
	
	if ($gamePhase == 'stat') {
		//if the player is playing against the ai, the ai needs to make a move, if the ai is the active user
		//so we check if there is an ai user in the game
		$adminPlayerIdQuery = myqu('SELECT u.user_id, gp.gameplayer_id, gp.is_active 
			FROM mytcg_user u 
			INNER JOIN mytcg_gameplayer gp 
			ON gp.user_id = u.user_id 
			WHERE gp.game_id = '.$gameId.' 
			AND u.ai = 1');
		if (sizeof($adminPlayerIdQuery) > 0) {
			//check that the ai is the active player
			$aiIsActive = $adminPlayerIdQuery[0]['is_active'];
			$aiPlayerId = $adminPlayerIdQuery[0]['gameplayer_id'];
			$aiUserId = $adminPlayerIdQuery[0]['user_id'];
			
			if ($aiIsActive == '1') {
				// we need to get the best stat for the ai to pick, so we need their top card first
				$adminTopCardQuery = myqu('SELECT min(gpc.pos), gpc.gameplayercard_id, uc.card_id
					FROM mytcg_gameplayercard gpc
					INNER JOIN mytcg_usercard uc 
					ON uc.usercard_id = gpc.usercard_id
					WHERE gpc.gameplayer_id = '.$aiPlayerId.'  
					AND gpc.gameplayercardstatus_id = 1 
					GROUP BY gpc.pos');
				$adminTopCard = $adminTopCardQuery[0]['card_id'];
				
				$adminStat = chooseStat($adminTopCard);
				return $adminStat;
			}
		}
	}
}

function selectStat($userId, $oppUserId, $gameId, $statTypeId) {
	$returnData = array();
	
	//get the opponent's user_id and player_id
	$oppPlayerIdQuery = myqu('SELECT gp.gameplayer_id, u.username 
		FROM mytcg_gameplayer gp 
		INNER JOIN mytcg_user u 
		ON u.user_id = gp.user_id 
		WHERE gp.user_id = '.$oppUserId
		.' AND gp.game_id = '.$gameId);
	$oppPlayerId = $oppPlayerIdQuery[0]['gameplayer_id'];
	$oppPlayerUsername = $oppPlayerIdQuery[0]['username'];
	
	//get the users player_id
	$userPlayerIdQuery = myqu('SELECT gp.gameplayer_id, u.username
		FROM mytcg_gameplayer gp 
		INNER JOIN mytcg_user u 
		ON u.user_id = gp.user_id 
		WHERE gp.user_id = '.$userId
		.' AND gp.game_id = '.$gameId);
	$userPlayerId = $userPlayerIdQuery[0]['gameplayer_id'];
	$userPlayerUsername = $userPlayerIdQuery[0]['username'];
	
	//get stat value and typeId for user
	$cardStatDetails = myqu('SELECT gpc.pos position, gpc.gameplayercard_id, c.description card_name, cs.statvalue, cs.description statdescription,c.image,i.description AS path
		FROM mytcg_gameplayercard gpc
		INNER JOIN mytcg_usercard uc ON uc.usercard_id = gpc.usercard_id
		INNER JOIN mytcg_card c	ON c.card_id = uc.card_id
		INNER JOIN mytcg_cardstat cs ON cs.card_id = c.card_id
		INNER JOIN mytcg_imageserver i ON i.imageserver_id = c.back_imageserver_id
		INNER JOIN mytcg_gameplayercardstatus gpcs ON gpcs.gameplayercardstatus_id = gpc.gameplayercardstatus_id
		INNER JOIN mytcg_categorystat cats ON cats.categorystat_id = cs.categorystat_id
		WHERE lower(gpcs.description) = "normal"
		AND gpc.gameplayer_id = '.$userPlayerId.'
		AND cs.categorystat_id = '.$statTypeId.'
		ORDER BY gpc.pos ASC');
	$userCardId = $cardStatDetails[0]['gameplayercard_id'];
	$userStatValue = $cardStatDetails[0]['statvalue'];
	$userStatDescription = $cardStatDetails[0]['statdescription'];
	$userCardName = $cardStatDetails[0]['card_name'];
	
	$returnData['playerCard'] = $cardStatDetails[0]['image'];
	$returnData['playerPath'] = $cardStatDetails[0]['path'];
	
	//get selected card and statvalue for the opponent
	$oppCardDetails = myqu('SELECT gpc.pos position, gpc.gameplayercard_id, c.description card_name, cs.statvalue, cs.description statdescription, cats.description stattype,c.image,i.description AS path
		FROM mytcg_gameplayercard gpc
		INNER JOIN mytcg_usercard uc ON uc.usercard_id = gpc.usercard_id
		INNER JOIN mytcg_card c	ON c.card_id = uc.card_id
		INNER JOIN mytcg_cardstat cs ON cs.card_id = c.card_id
		INNER JOIN mytcg_imageserver i ON i.imageserver_id = c.back_imageserver_id
		INNER JOIN mytcg_gameplayercardstatus gpcs ON gpcs.gameplayercardstatus_id = gpc.gameplayercardstatus_id
		INNER JOIN mytcg_categorystat cats ON cats.categorystat_id = cs.categorystat_id
		WHERE lower(gpcs.description) = "normal"
		AND gpc.gameplayer_id = '.$oppPlayerId.' 
		AND cs.categorystat_id = '.$statTypeId.' 
		ORDER BY gpc.pos ASC');
	$oppCardId = $oppCardDetails[0]['gameplayercard_id'];
	$oppCardName = $oppCardDetails[0]['card_name'];
	$oppStatValue = $oppCardDetails[0]['statvalue'];
	$oppStatDescription = $oppCardDetails[0]['statdescription'];
	$statType = $oppCardDetails[0]['stattype'];
	
	$returnData['oppCard'] = $oppCardDetails[0]['image'];
	$returnData['oppPath'] = $oppCardDetails[0]['path'];
	
	//by default we set the gameplayerstatus_id to pending after a move, but if its an AI user, we must set it to waiting
	$oppStatusId = '1';
	//get the admins userId
	$aiUserIdQuery = myqu('SELECT u.user_id, gp.is_active, gp.gameplayer_id 
		FROM mytcg_user u 
		INNER JOIN mytcg_gameplayer gp 
		ON gp.user_id = u.user_id 
		WHERE gp.game_id = '.$gameId.' 
		AND u.ai = 1');

	//check if the game is against AI
	if (sizeof($aiUserIdQuery) > 0) {
		$oppStatusId = '2';
	}
	
	$winnerId = 0;//the winner and loser ids will be set in the following if, and then the cards assigned afterwards.
	$loserId = 0;
	$exp = '';
	//give the player with the higher stat both cards
	//we can also build a string explaining what happened, that we will send back to the user, and save for the opponent
	//in addition, as per normal trumps rules, whoever wins gets to pick the next stat.
	if ($oppStatValue > $userStatValue) {
		//the opponent won, set the last result and active player
		$exp = $oppPlayerUsername.' won! Their '.$statType.' of '.$oppStatDescription.
			' beat '.$userPlayerUsername.'\\\'s '.$userCardName.' with a '.$statType.
			' of '.$userStatDescription.'.';
		$returnData['playerWin'] = 0;
		$returnData['oppWin'] = 1;
		$returnData['stat'] = $statTypeId;
		$returnData['response'] = $exp;
		myqu('UPDATE mytcg_gameplayer SET is_active = 1, gameplayerstatus_id = '.$oppStatusId.' WHERE gameplayer_id = '.$oppPlayerId);
		
		myqu('UPDATE mytcg_gameplayer SET is_active = 0, gameplayerstatus_id = 1 WHERE gameplayer_id = '.$userPlayerId);
		
		//set the winner and loser ids
		$winnerId = $oppPlayerId;
		$loserId = $userPlayerId;
	}
	else if ($oppStatValue < $userStatValue) {
		//the user won, set the last result and active player
		$exp = $userPlayerUsername.' won! Their '.$statType.' of '.$userStatDescription.
			' beat '.$oppPlayerUsername.'\\\'s '.$oppCardName.' with a '.$statType.
			' of '.$oppStatDescription.'.';
		$returnData['playerWin'] = 1;
		$returnData['oppWin'] = 0;
		$returnData['stat'] = $statTypeId;
		$returnData['response'] = $exp;
		myqu('UPDATE mytcg_gameplayer SET is_active = 0, gameplayerstatus_id = '.$oppStatusId.' WHERE gameplayer_id = '.$oppPlayerId);
		
		myqu('UPDATE mytcg_gameplayer SET is_active = 1, gameplayerstatus_id = 1 WHERE gameplayer_id = '.$userPlayerId);
		
		//set active player
		$winnerId = $userPlayerId;
		$loserId = $oppPlayerId;
	}
	else {
		
		//it was a draw
		$exp = 'Draw! Your opponent\\\'s '.$statType.' of '.$oppStatDescription.
			' equals your\\\'s!';
		$returnData['playerWin'] = 0;
		$returnData['oppWin'] = 0;
		$returnData['stat'] = $statTypeId;
		$returnData['response'] = $exp;
		myqu('UPDATE mytcg_gameplayer SET gameplayerstatus_id = 1 WHERE gameplayer_id = '.$userPlayerId);
		myqu('UPDATE mytcg_gameplayer SET gameplayerstatus_id = '.$oppStatusId.' WHERE gameplayer_id = '.$oppPlayerId);
			
		//in the case of a draw, the active player stays the same.
		//we need to set the card ids to 'pending'
		myqu('UPDATE mytcg_gameplayercard SET gameplayercardstatus_id = 2 WHERE gameplayercard_id IN ('.$oppCardId.','.$userCardId.')');
	}
	
	//add the log message, so players can see what happened.
	myqu('INSERT INTO mytcg_gamelog 
		(game_id, date, message, categorystat_id, winner) 
		VALUES('.$gameId.', now(), \''.$exp.'\', '.$statTypeId.', '.(($winnerId == 0 && $loserId == 0)?'0':(($winnerId==$userPlayerId)?'1':'2')).')');
	
	//if there was a winner, assign cards
	if ($winnerId != 0 && $loserId != 0) {
		//get the current max pos for the winner
		$posQuery = myqu('SELECT max(pos) + 1 pos
     FROM mytcg_gameplayercard
     WHERE gameplayer_id = '.$winnerId);
		$pos = $posQuery[0]['pos'];
		
		//get the cards that need to change ownership
		$cards = myqu('SELECT gameplayercard_id
			FROM mytcg_gameplayercard
			WHERE (gameplayercardstatus_id = 2
				AND (gameplayer_id = '.$winnerId.' 
					OR gameplayer_id = '.$loserId.' ))
			OR (pos = (SELECT min(pos)
					FROM mytcg_gameplayercard
					WHERE gameplayercardstatus_id = 1
					AND gameplayer_id = '.$loserId.')
				AND gameplayer_id = '.$loserId.')');
		
		//set their gameplayer_id's to the winner's
		$count = 0;
		while ($card=$cards[$count]) {
			//and repeat for each one
			myqu('UPDATE mytcg_gameplayercard
				SET pos = '.$pos.',
				gameplayer_id = '.$winnerId.',
				gameplayercardstatus_id = 1 
				WHERE gameplayercard_id = '.$card['gameplayercard_id']);
			$count++;
			$pos++;
		}
		
		//get the card that won the round
		$winningCardQuery = myqu('SELECT min(pos), gameplayercard_id
			FROM mytcg_gameplayercard
			WHERE gameplayer_id = '.$winnerId.' 
			AND gameplayercardstatus_id = 1 
			GROUP BY pos');
		$winningCardId = $winningCardQuery[0]['gameplayercard_id'];
		//and update its position
		myqu('UPDATE mytcg_gameplayercard
			SET pos = '.$pos.' 
			WHERE gameplayercard_id = '.$winningCardId);
	}
	//we need to check if the game is over. that is, if one of the players has no more playable cards.
	$gameOverQuery = myqu('SELECT count(gameplayercard_id) cards, gameplayer_id 
		FROM mytcg_gameplayercard 
		WHERE gameplayer_id in ('.$userPlayerId.', '.$oppPlayerId.') 
		AND gameplayercardstatus_id = 1 
		GROUP BY gameplayer_id');
	
	$draw = false; //if both have no playable cards, the game is a draw
	$over = true; //if either of then have no playable cards, the game is over
	$winnerId = 0;
	
	if (sizeof($gameOverQuery) == 0) {
		$draw = true;
	}
	else if (sizeof($gameOverQuery) == 1) {
		$winnerId = $gameOverQuery[0]['gameplayer_id'];
	}
	else {
		$over = false;
	}
	$iUserID = $userId;
	//if the game is over, we can set the phase to results, and add an entry to mytcg_gamelog
	if ($over) {
		$exp = '';
		if ($draw) {
			$exp = 'The game ended in a draw!';
			$returnData['gameover'] = 'draw';
		}
		else {
			$winnerName = '';
			if ($winnerId == $userPlayerId) {
				$winnerName = $userPlayerUsername;
				$returnData['gameover'] = 'player';
				
				$aUpdate=myqu('SELECT gameswon
					FROM mytcg_user where user_id = (SELECT user_id from mytcg_gameplayer where gameplayer_id = '.$winnerId.')');
			
				$iUpdate=$aUpdate[0];
				if ($iUpdate['gameswon'] < 3) {
					myqu('INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES ((SELECT user_id from mytcg_gameplayer where gameplayer_id = '.$winnerId.'), "Received 50 credits for beating '.$oppPlayerUsername.'", now(), 50)');
			
					myqu('UPDATE mytcg_user SET credits = credits + 50, gameswon = (gameswon+1) WHERE user_id =(SELECT user_id from mytcg_gameplayer where gameplayer_id = '.$winnerId.')');
				} else if ($iUpdate['gameswon'] == 3) {
					myqu('UPDATE mytcg_user SET gameswon = (gameswon+1) WHERE user_id =(SELECT user_id from mytcg_gameplayer where gameplayer_id = '.$winnerId.')');
				}
			}
			else {
				$winnerName = $oppPlayerUsername;
				$returnData['gameover'] = 'opp';
			}
			$aUpdate=myqu('SELECT gameswon, credits
					FROM mytcg_user where user_id = (SELECT user_id from mytcg_gameplayer where gameplayer_id = '.$winnerId.')');
			$qu = 'SELECT credits
					FROM mytcg_user where user_id = '.$iUserID;
			$aCredits=myqu($qu);
			
			$qu = 'SELECT count(*) as cnt
					FROM mytcg_gameplayer a, mytcg_game b
					WHERE a.game_id = b.game_id
					AND gamestatus_id = 2
					AND user_id = '.$iUserID;
			$aPlayed=myqu($qu);
			
			$qu = 'select rownum 
					from (select @rownum:=@rownum+1 rownum, credits, user_id 
							from mytcg_user, (select @rownum:=0) r 
							where user_id <> 1 order by credits DESC) a 
					where user_id = '.$iUserID;
			$aRich=myqu($qu);
			
			$qu = 'SELECT rownum
					FROM
					(SELECT @rownum:=@rownum+1 as rownum, count(*) val, user_id 
					FROM (SELECT c.user_id, c.username, b.gameplayer_id, b.game_id , count(d.gameplayercard_id) as cnt 
						FROM mytcg_game a 
						INNER JOIN mytcg_gameplayer b ON b.game_id = a.game_id 
						INNER JOIN mytcg_user c ON c.user_id = b.user_id 
						LEFT OUTER JOIN mytcg_gameplayercard d ON d.gameplayer_id = b.gameplayer_id 
						WHERE c.user_id <> 1 GROUP BY b.gameplayer_id) e, 
						(SELECT @rownum:=0) f
					WHERE e.cnt = 20 
					GROUP BY username 
					ORDER BY count(*) DESC) a
					WHERE user_id = '.$iUserID;
			
			$aWon=myqu($qu);
		
			$iUpdate=$aUpdate[0];
			if ($iUpdate['gameswon'] <= 3) {
				$exp = $winnerName.' wins! '.$winnerName.' received 50 credits for winning.'; 
						/*You have played '.$aPlayed[0]['cnt'].' game(s) in total. You are currently ranked number '.$aWon[0]['rownum'].' on Most Games Won.
						Current credits '.$aCredits[0][credits].'. You are currently ranked number '.$aRich[0]['rownum'].' on Richest User.';*/
			} else {
				$exp = $winnerName.' wins! '.$winnerName.' already won 3 games today and was just playing for fun.'; 
						/*You have played '.$aPlayed[0]['cnt'].' game(s) in total. You are currently ranked number '.$aWon[0]['rownum'].' on Most Games Won.
						Current credits '.$aCredits[0][credits].'. You are currently ranked number '.$aRich[0]['rownum'].' on Richest User.';*/
			}
		}
		
		//add the log message, so players can see the outcome
		myqu('INSERT INTO mytcg_gamelog 
			(game_id, date, message, categorystat_id) 
			VALUES('.$gameId.', now(), \''.$exp.'\', 0)');
		//and set the game phase to results
		$gamePhaseIdQuery = myqu('SELECT gamephase_id 
			FROM mytcg_gamephase 
			WHERE description = "result"');
		$resultPhase = $gamePhaseIdQuery[0]['gamephase_id'];
		//and set the game phase to results
		$gameStatusIdQuery = myqu('SELECT gamestatus_id 
			FROM mytcg_gamestatus 
			WHERE description = "complete"');
		$completeStatus = $gameStatusIdQuery[0]['gamestatus_id'];
		myqu('UPDATE mytcg_game SET gamephase_id = '.$resultPhase.', gamestatus_id = '.$completeStatus.' WHERE game_id = '.$gameId);
		myqu('UPDATE mytcg_gameplayer SET pending = 1 WHERE game_id = '.$gameId);
	}
	return $returnData;
}
?>