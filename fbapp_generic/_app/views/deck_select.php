<div id="deckPlate">
	<div class="deck-top-menu-bar">
		<div id="createDeckModalButton">Create a New Deck</div>
	</div>
	
	<?php 
	
	//for each deck in user's collection, get the deck info and count the cards in the deck,
	//then print the info and the card count for each deck.
	
	foreach ($aDecks as $aDeck) {
		$iDeckID = $aDeck['deck_id'];
		$query =  "SELECT COUNT(usercard_id) FROM mytcg_usercard U WHERE U.deck_id=$iDeckID";	  
		$aCardCount = myqu($query);
	?>
	<div class="deck-container" id="<?php echo $aDeck['deck_id']; ?>">
		<div class="deck-title"><?php echo $aDeck['description']; ?></div>
		<div class="deckDeleteButton"></div>
		<div class="deck-icons">
			<div class="editIcon"></div>
			<div class="deleteIcon"></div>
		</div>
		<div class="deck-image-container">
				<img src="<?php echo $aDeck['path'].'decks/'.$aDeck['image'].'.png'; ?>" height='140' alt="deck-image" />
		</div>
		<div class="deck-attributes">
			<table class="deck-attributes-table">
				<tr>
					<td>Cards: </td><td id="deck-cards"><?php echo($aCardCount[0][0]); ?>/10</td>
				</tr>
				<tr>
					<td>Category: </td><td><?php echo $aDeck['catdesc'];?></td>
				</tr>
				<tr>
					<td>Ranking: </td><td></td>
				</tr>
				<tr>
					<td>Value: </td><td id="deck-value">0 TCG</td>
				</tr>
			</table>
		</div>
	</div>
	<?php }?>
</div>