<?php
	//PHASE ONE - GET THE GAME PLAYERS
	$sql = "SELECT * FROM mytcg_gameplayer WHERE game_id = ".$gameID;
	$aGamePlayers = myqu($sql);
	if($aGamePlayers[0]['is_active'] != '1'){ //SEE WHICH PLAYER IS HUMAN
		$aPlayer = $aGamePlayers[0];
	 	$aOpp = $aGamePlayers[1];
	}else{
		$aPlayer = $aGamePlayers[1];
	 	$aOpp = $aGamePlayers[0];
	}

	//PHASE TWO - GET BOTH PLAYERS TOP CARDSCARDS
	//Player 1 card
	$sql = "SELECT GPC.pos, C.card_id, C.description
			FROM mytcg_gameplayercard GPC
			INNER JOIN mytcg_usercard UC ON (GPC.usercard_id = UC.usercard_id)
			INNER JOIN mytcg_card C ON (UC.card_id = C.card_id)
			WHERE GPC.gameplayer_id = ".$aPlayer['gameplayer_id']."
			ORDER BY pos";
	$aPlayerCard = myqu($sql);
	
	//Player 1 Card stats
	$sql = "SELECT CS.*, CTS.description AS statName
			FROM mytcg_cardstat CS
			INNER JOIN mytcg_categorystat CTS ON (CS.categorystat_id = CTS.categorystat_id)
			WHERE CS.card_id = ".$aPlayerCard[0]['card_id']."
			ORDER BY CS.categorystat_id";
	$aPlayerCardStats = myqu($sql);
	
	//Player 2 card
	$sql = "SELECT GPC.pos, C.card_id, C.description
			FROM mytcg_gameplayercard GPC
			INNER JOIN mytcg_usercard UC ON (GPC.usercard_id = UC.usercard_id)
			INNER JOIN mytcg_card C ON (UC.card_id = C.card_id)
			WHERE GPC.gameplayer_id = ".$aOpp['gameplayer_id']."
			ORDER BY pos";
	$aOppCard = myqu($sql);
	
	$sql = "SELECT CS.*, CTS.description AS statName
			FROM mytcg_cardstat CS
			INNER JOIN mytcg_categorystat CTS ON (CS.categorystat_id = CTS.categorystat_id)
			WHERE CS.card_id = ".$aOppCard[0]['card_id']."
			ORDER BY CS.categorystat_id";
	$aOppCardStats = myqu($sql);
	
	//PROCESS THE GAMEPLAY IF A CARD COMPARE NEEDS TO HAPPEN
	
	
	
	if($stat['statvalue'] > $ostat['statvalue']){
		//USER WINS
		$exp = $sUserPlayerName.' won! Their '.$aPlayerCard['description'].' of '.$stat['statvalue'].
		' beat '.$sOppPlayerName.'\\\'s '.$ostat[].' with a '.$statType.
		' of '.$oppStatDescription.'.';
		myqu('UPDATE mytcg_gameplayer SET is_active = 0, gameplayerstatus_id = '.$oppStatusId.' WHERE gameplayer_id = '.$oppPlayerId);
		myqu('UPDATE mytcg_gameplayer SET is_active = 1, gameplayerstatus_id = 1 WHERE gameplayer_id = '.$userPlayerId);
	}elseif($aPlayerCardStats[$statNr]['statvalue'] < $aOppCardStats[$statNr]['statvalue']){
		//AI WINS
		$exp = $oppPlayerUsername.' won! Their '.$statType.' of '.$oppStatDescription.
		' beat '.$userPlayerUsername.'\\\'s '.$userCardName.' with a '.$statType.
		' of '.$userStatDescription.'.';
		myqu('UPDATE mytcg_gameplayer SET is_active = 1, gameplayerstatus_id = '.$oppStatusId.' WHERE gameplayer_id = '.$oppPlayerId);
		myqu('UPDATE mytcg_gameplayer SET is_active = 0, gameplayerstatus_id = 1 WHERE gameplayer_id = '.$userPlayerId);
	}else{
		//PLAYERS DRAW
		$exp = 'Draw! Your opponent\\\'s '.$statType.' of '.$oppStatDescription.
		' equals your\\\'s!';
		myqu('UPDATE mytcg_gameplayer SET gameplayerstatus_id = 1 WHERE gameplayer_id = '.$userPlayerId);
		myqu('UPDATE mytcg_gameplayer SET gameplayerstatus_id = '.$oppStatusId.' WHERE gameplayer_id = '.$oppPlayerId);
			
		//in the case of a draw, the active player stays the same.
		//we need to set the card ids to 'pending'
		myqu('UPDATE mytcg_gameplayercard SET gameplayercardstatus_id = 2 WHERE gameplayercard_id IN ('.$oppCardId.','.$userCardId.')');
	}
	
?>