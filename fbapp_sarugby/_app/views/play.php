<?php 
function cardBackImageURL($account) {
	switch($account) {
		case 'topcar': return  "_site/card_back_big.png";
		case 'generic': return "http://www.mytcg.net/img/cards/gc.png";
	}
}
?>

<div class="headTitle">
		<div class="lineGrey" style="width:585px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="head<?php echo $_GET['page']; ?>"></div>
		<div class="lineGrey" style="width:30px;margin-left: 10px;"></div>
</div>

<div id="play-window">
	<div class="game-view" id="active_game">
		<div id="game-table">
			<div id="gameIconSmall"></div>
			<div id="gameDrawCard">
				<div id="drawCardDesc">Draw</div>
				<div id="drawCardNumber"></div>
			</div>
			<div id="gameMenuButton" class="return-button">Return to main menu</div>
			<div class="roundResult" id="youDraw"></div>
			<div class="roundResult" id="youWin"></div>
			<div class="roundResult" id="youLose"></div>
			
			<div class="gameTableTopRowItem" id="playerAvatar">
				<div class="avatar">
					<!-- <div class="avatarXp"><?php echo $user['xp']; ?></div> -->
					<img src="https://graph.facebook.com/<?php echo $userProfile['id']; ?>/picture?type=square" alt="user pic" height="65" width="65">
				</div>
			</div>
			
			<div class="gameTableTopRowItem" id="gameScore">
				<div class="card-number-background">
					<span class="card-number" id="cardNumberLeft">88</span> /
					<span class="card-number" id="cardNumberRight">88</span>
				</div>
			</div>
			
			<div class="gameTableTopRowItem" id="opponentAvatar">
				<div class="avatar">
					<div class="avatarXp"></div>
					
				</div>
			</div>
					
			<div id="player-card-side" class="active-game-card-side">
				<div class="card-overlay">
					<div class="attribute-overlay" id="0"><div class="indicator"></div><div class="indicator-active"></div></div>
					<div class="attribute-overlay" id="1"><div class="indicator"></div><div class="indicator-active"></div></div>
					<div class="attribute-overlay" id="2"><div class="indicator"></div><div class="indicator-active"></div></div>
					<div class="attribute-overlay" id="3"><div class="indicator"></div><div class="indicator-active"></div></div>
					<div class="attribute-overlay" id="4"><div class="indicator"></div><div class="indicator-active"></div></div>
					<div class="attribute-overlay" id="5"><div class="indicator"></div><div class="indicator-active"></div></div>
					<div class="attribute-overlay" id="6"><div class="indicator"></div><div class="indicator-active"></div></div>
				</div>
				<div id="playerCardSideImage" class="activeGameCardSideImage">
					<img src=<?php echo cardBackImageURL('topcar'); ?> alt="User Card" />
				</div>
					
			</div>
			<div id="opponent-card-side" class="active-game-card-side">
				<div class="card-overlay">
					<div class="attribute-overlay"><div class="indicator"></div></div>
					<div class="attribute-overlay"><div class="indicator"></div></div>
					<div class="attribute-overlay"><div class="indicator"></div></div>
					<div class="attribute-overlay"><div class="indicator"></div></div>
					<div class="attribute-overlay"><div class="indicator"></div></div>
					<div class="attribute-overlay"><div class="indicator"></div></div>
					<div class="attribute-overlay"><div class="indicator"></div></div>
				</div>
				<div id="opponentCardSideImage" class="activeGameCardSideImage">
					<img src=<?php echo cardBackImageURL('topcar'); ?> alt="Opponent Card" />
				</div>
					
			</div>
				
			<div class="card-container" id="cardContainerLeft">
				<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />
								<img src="_site/card_back_small.png" />			
			</div>
				
			<div class="card-container" id="cardContainerRight">	
				<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />
							<img src="_site/card_back_small.png" />				
				</div>
			</div>
		</div>
		
	<div class="game-view" id="gameConclusion" >
		<div class="gameConclusionImage gameMenuContainer">
			<div id="gameConclusionScore"></div>
			<div id="gameContinueButton">Continue</div>
		</div>
		
	</div>
	
	<div class="game-view" id="game_menu" >
		<div class="game-window-heading"></div>
		<div class="gameMenuContainer gradient-bg">
			<div class="game-menu-item initGame" id="new-game">
				<div id="newGameIcon" class="gameMenuIcon"></div>New Game
			</div>
			<div class="game-menu-item initGame" id="load-game">
				<div id="loadGameIcon" class="gameMenuIcon"></div>Load Game
			</div>
		</div>
	</div>

	<div class="game-view" id="choose_deck">
		<div id="choose-deck-heading" class="game-window-heading"></div>
		<div class="gameMenuContainer gradient-bg" >
			<div class="chooseCaption">Choose Deck</div>
			<div id="next-button" class="game-back-next-buttons"></div>
			<div id="deck_list" class="decks">
			</div>
			<div id="menu-left-button" class="menu-scroll-button"></div>
			<div id="menu-right-button" class="menu-scroll-button"></div>
		</div>
		<div class="return-button">Return to main menu</div>
	</div>
	
	<div class="game-view" id="load_game">
		<div id="load-game-heading" class="game-window-heading"></div>
		
		<div class="saved-game-list gameMenuContainer" id="game_list">
			<div id="loadGamesIndicator" class="loadingIndicator">
				<p>Loading previous games...</p>
			<img alt="Loading" src="_site/loading2.gif" />
			</div>
		</div>
		<div class="return-button">Return to main menu</div>
	</div>
	
	<div class="game-view" id="choose_opponent" >
		<div id="choose-opponent-heading" class="game-window-heading"></div>
		<div class="gameMenuContainer gradient-bg" >
			<div class="chooseCaption">Choose Opponent</div>
			<div class="choose-friend game-menu-item">
				<div class="gameMenuIcon"></div>
				Friend</div>
			<div class="choose-computer game-menu-item">
				<div class="gameMenuIcon"></div>
				Computer</div>
			<div class="choose-player game-menu-item">
				<div class="gameMenuIcon"></div>
				Someone</div>
		</div>
		<div class="return-button">Return to main menu</div>
	</div>
	
	<div class="game-view" id="game_friend_searcher" >
		<div class="saved-game-list gameMenuContainer" id="game_list">
			<div id="game-friend-seacher-heading" class="game-window-heading"></div>
			<div class="game-deck-info">Category: <br>Chosen Deck: </div>
			<div class="search-find-box">
				<form id="friendSearchForm">
					<div class="search-find-box-label">Find friend: </div>
					<input class="friend-finder-input" type="text" id="friend-finder-input" />
					<div id="friendSearch" class="cmdButton">Search</div>
				</form>
				<form id="friendSearchResults">
					<div style="margin-bottom:5px;font-weight:bold;">Search Results:</div>
					<div id="friendSearchResultsList"></div>
					<div class="cmdButton" id="searchAgain">Search again</div>
					<div style="bottom:0px;right:0px;width:100px;display:none;" id="inviteFriendHolder">
						<div class="cmdButton" id="inviteFriend" style="bottom:5px;right:5px;">Invite friend</div>
					</div>
					<div style="bottom:0px;right:0px;width:100px;display:none;" id="inviteFriendHolder">
						<div class="cmdButton" id="inviteFriend" style="bottom:5px;right:5px;">Invite friend</div>
					</div>
				</form>
			</div>
			<div class="return-button">Return to main menu</div>
		</div>
	</div>
	
	<div class="game-view" id="game_player_searcher">
		<div class="gameMenuContainer gradient-bg" >
			<div id="game-player-searcher-heading" class="game-window-heading"></div>		
			<div class="game-deck-info">Category: <br>Chosen Deck: </div>
			<div class="search-find-box">
				<form>
				<div class="search-find-box-label" style="text-align: center">Searching for online players..</div>
				<div class="progress-indicator">
					<img alt="Loading" src="_site/loading2.gif" />
				</div>	
				</form>
			</div>
			<div class="player-search-progress"></div>
			<div class="return-button">Return to main menu</div>
		</div>
	</div>
	
	<div class="game-view" id="difficulty_level">
		<div id="difficulty-level-heading" class="game-window-heading"></div>	
		
		<div class="gameMenuContainer gradient-bg">
			<div class="chooseCaption">Choose Difficulty</div>	
			<div id="choose-easy" class="game-menu-item">
				<div class="gameMenuIcon"></div>Easy
			</div>
			<div id="choose-normal" class="game-menu-item">
				<div class="gameMenuIcon"></div>Normal
			</div>
			<div id="choose-hard" class="game-menu-item">
				<div class="gameMenuIcon"></div>Hard
			</div>
			<!-- <div id="menu-left-button" class="menu-scroll-button"></div> -->
		</div>
		<div class="return-button">Return to main menu</div>
	</div>
	
</div>