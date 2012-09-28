<?php
	//INCLUDE GAME PROCESS PAGE
	require_once('gameplay_functions.php');
	//CREATE SESSION FOR CURRENT ACTIVE GAME
	if(isset($_GET['game_id'])){
		$_SESSION['game_id'] = $_GET['game_id'];
	}
	$iSize = $_SESSION['size'];
	if(isset($_GET['size'])){
		$_SESSION['size'] = $_GET['size'];
	}
	$iSize = $_SESSION['size'];
	
	//SET DEFAULT VARIABLES FOR GAME
	$categoryId = 2; //Static value for topcar category
	$gameID = $_SESSION['game_id'];
	$userID = $_SESSION['userID'];
	
	//CREATE THE NEW GAME ENTRIES INTO THE DATABASE
	if($gameID==0){
		//create the game, get the game_id
		$gameIDQuery = myqu('SELECT (CASE WHEN MAX(game_id) IS NULL THEN 0 ELSE MAX(game_id) END) + 1 AS game_id
			FROM mytcg_game');
		$gameID = $gameIDQuery[0]['game_id'];
		myqu('INSERT INTO mytcg_game (game_id, gamestatus_id, gamephase_id, category_id, date_start, date_created) 
			SELECT '.$gameID.', (SELECT gamestatus_id FROM mytcg_gamestatus WHERE lower(description) = "incomplete"),
			(SELECT gamephase_id FROM mytcg_gamephase WHERE lower(description) = "stat"), '.$categoryId.', now(), now()
			FROM DUAL');
		
		//add the player to the game, the host goes first
		myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, is_active, gameplayerstatus_id, deck_id)
			VALUES ('.$gameID.', '.$userID.', 1, 1, -1)');
		
		//get the ai users
		$aiUserIdQuery = myqu('SELECT u.user_id 
			FROM mytcg_user u
			WHERE u.user_id = 1');
		$aiUserId = $aiUserIdQuery[0]['user_id'];
		
		//add the ai to the game
		myqu('INSERT INTO mytcg_gameplayer (game_id, user_id, is_active, gameplayerstatus_id, deck_id)
			VALUES ('.$gameID.', '.$aiUserId.', 0, 2, -1)');
	
		newGame($userID,$gameID);
		$_SESSION['game_id'] = $gameID;
		$gameID = $_SESSION['game_id'];
		
		echo("stuff");
	}
	
	//GET SOME PLAYER DETAILS NEEDED FOR THE PROCESS
	$gamePhaseQuery = myqu('SELECT g.gamephase_id, g.lobby, lower(gp.description) as description
							FROM mytcg_game g
							INNER JOIN mytcg_gamephase gp
							ON g.gamephase_id = gp.gamephase_id
							WHERE g.game_id = '.$gameID."");
	$gamePhase = $gamePhaseQuery[0]['description'];
	
	//GET TOP OF PAGE DISPLAY RESULTS
	$cardsQuery = myqu('SELECT count(gpc.gameplayercard_id) cards, gp.user_id, u.username, gp.is_active, gp.gameplayer_id
			FROM mytcg_gameplayercard gpc
			INNER JOIN mytcg_gameplayer gp
			ON gp.gameplayer_id = gpc.gameplayer_id
			INNER JOIN mytcg_user u
			ON u.user_id = gp.user_id
			WHERE gp.game_id = '.$gameID.'
			GROUP BY gp.gameplayer_id');
	
	//***MIGHT NEED REVISION IF USER IS NOT FIRST ENTRY IN RESULTS
	$iUserID = $cardsQuery[0]['user_id'];
	$iUserPlayerID = $cardsQuery[0]['gameplayer_id'];
	$iUserStatus = $cardsQuery[0]['is_active'];
	$iUserCardCount = $cardsQuery[0]['cards'];
	$sUserPlayerName = $cardsQuery[0]['username'];
	
	$iOppID = $cardsQuery[1]['user_id'];
	$iOppPlayerID = $cardsQuery[1]['gameplayer_id'];
	$iOppStatus = $cardsQuery[1]['is_active'];
	$iOppCardCount = $cardsQuery[1]['cards'];
	$sOppPlayerName = $cardsQuery[1]['username'];
	
	if($iOppStatus == "1"){
		$adminStat = continueGame($gameID,$iUserID);
		$results = selectStat($iUserID, $iOppID, $gameID, $adminStat);	
	}
	if(($iUserStatus == "1")&&(isset($_GET['stat']))){
		$iStatValue = $_GET['stat'];
		$results = selectStat($iUserID,$iOppID,$gameID,$iStatValue);
	}

	//SET SELECTED PAGE SIZE DETAILS
	switch ($iSize)
	{
		//large landscape
		case 1:
			$iMarginTop = 55;
			$iCalcMarginTop = $iMarginTop-51;
		  	$iHeight = 300;
			$iWidth = 200;
			$iHeightStat = ($iHeight/7)-20;

		break;
		//large portrait
		case 2:
			$iMarginTop = 55;
			$iCalcMarginTop = $iMarginTop-51;
		  	$iHeight = 300;
			$iWidth = 200;
			$iHeightStat = ($iHeight/7)-20;
		break;
		//small landscape
		case 3:
			$iMarginTop = 42;
			$iCalcMarginTop = $iMarginTop-38;
		  	$iHeight = 250;
			$iWidth = 150;
			$iHeightStat = ($iHeight/7)-20;
		break;
		//small portrait
		case 4:
			$iMarginTop = 42;
			$iCalcMarginTop = $iMarginTop-38;
		  	$iHeight = 250;
			$iWidth = 150;
			$iHeightStat = ($iHeight/7)-20;
		break;
	}
?> 
<div id="card_game">
	<?php echo("<li><p>[".$iUserCardCount."]&nbsp;".$sUserPlayerName."&nbsp;&nbsp;VS&nbsp;&nbsp;".$sOppPlayerName."&nbsp;[".$iOppCardCount."]</p></li>"); ?>
	<?php if(($iUserStatus == "1")&&($results == null)){
		$topCardQuery = myqu('SELECT min(gpc.pos), gpc.gameplayercard_id, uc.card_id, c.image, i.description AS path
							  FROM mytcg_gameplayercard gpc
							  INNER JOIN mytcg_usercard uc ON uc.usercard_id = gpc.usercard_id
							  INNER JOIN mytcg_card c ON uc.card_id = c.card_id
							  INNER JOIN mytcg_imageserver i ON i.imageserver_id = c.back_imageserver_id
							  WHERE gpc.gameplayer_id = '.$iUserPlayerID.'
							  AND gpc.gameplayercardstatus_id = 1 
							  GROUP BY gpc.pos');
				$topCard = $topCardQuery[0];
	?>
	<div id="player-card-side" style="width:<?php echo($iWidth); ?>px">
		<div id="stat_selection" style="width:<?php echo($iWidth); ?>px">
			<a href="index.php?page=game_play&stat=1" class="stat_selection_type1" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iMarginTop); ?>px"><div></div></a>
			<a href="index.php?page=game_play&stat=2" class="stat_selection_type2" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"><div ></div></a>
			<a href="index.php?page=game_play&stat=3" class="stat_selection_type3" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"><div ></div></a>
			<a href="index.php?page=game_play&stat=4" class="stat_selection_type4" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"><div ></div></a>
			<a href="index.php?page=game_play&stat=5" class="stat_selection_type5" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"><div ></div></a>
			<a href="index.php?page=game_play&stat=6" class="stat_selection_type6" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"><div ></div></a>
			<a href="index.php?page=game_play&stat=7" class="stat_selection_type7" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"><div ></div></a>
		</div>
		<img src="<?php echo($topCard['path']); ?>cards/<?php echo($topCard['image']); ?>_back.png" alt="player Card" title="player" usemap="#card2" width="100%" height="100%" />
	</div>
	<div id="opponent-card-side" style="width:<?php echo($iWidth); ?>px">
		<img src="images/back.jpg" alt="Opponent Card" title="opponent" width="100%" height="100%" />
	</div>
	<?php }else{
		$winStat = (int)$results['stat'];
		if($results['playerWin']==1){ //Player wins
			$statRes[0]="win";
			$statRes[1]="lose";
		}elseif($results['oppWin']==1){ //Opp wins
			$statRes[1]="win";
			$statRes[0]="lose";
		}else{
			$statRes[0]="win";
			$statRes[1]="win";
		}
		$iCalcMarginTop = ($winStat*30)+20;
	?>
	<?php if($results['gameover'] != ''){ ?>
		<a href="index.php?page=game_over&game_over=<?php echo($results['gameover']); ?>"><div id="divNext">Next</div></a>
	<?php } else { ?>
		<a href="index.php?page=game_play&next=1"><div id="divNext">Next</div></a>
	<?php } ?>
	<div id="player-card-side" style="width:<?php echo($iWidth); ?>px">
		<div id="stat_selection" style="width:<?php echo($iWidth); ?>px">
			<div class="stat_selection_<?php echo($statRes[0]); ?>" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"></div>
		</div>
		<img src="<?php echo($results['playerPath']); ?>cards/<?php echo($results['playerCard']); ?>_back.png" alt="player Card" title="player" width="100%" height="100%" />
	</div>
	<div id="opponent-card-side" style="width:<?php echo($iWidth); ?>px">
		<div id="stat_selection" style="width:<?php echo($iWidth); ?>px">
			<div class="stat_selection_<?php echo($statRes[1]); ?>" style="width:<?php echo($iWidth); ?>px;height:<?php echo($iHeightStat); ?>px;margin-top:<?php echo($iCalcMarginTop); ?>px"></div>
		</div>
		<img src="<?php echo($results['oppPath']); ?>cards/<?php echo($results['oppCard']); ?>_back.png" alt="Opponent Card" title="opponent" width="100%" height="100%" />
	</div>
	<?php } ?>
</div>