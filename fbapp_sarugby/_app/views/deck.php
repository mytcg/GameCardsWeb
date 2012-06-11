<?php
$iUserID = $user['user_id'];

//get rows for all decks belonging to user

$query = "SELECT D.description, 
				D.image, 
				CAT.description as catdesc, 
				D.category_id, 
				D.deck_id, 
				I.description AS path 
				FROM mytcg_deck D 
				INNER JOIN mytcg_imageserver I 
				ON (D.imageserver_id = I.imageserver_id) 
				INNER JOIN mytcg_category CAT 
				ON (D.category_id = CAT.category_id) 
				WHERE D.user_id=$iUserID";	
  
$aDecks = myqu($query);

?>

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

<!-- <div id="deck-create-modal-window" class="modal-window">
	<div class="closeButtonContainer">
		<div class="close-button"></div>
		<div class="half" id="topHalf"></div>
		<div class="half" id="bottomHalf"></div>
	</div>
	<form>
		<label for="name-deck" id="name-deck-label">Deck Name</label>
		<input type="text"size="30" id="name-deck"/>
		<div class="deck-image-label">Deck Image</div>
		<div class="deck-create-screenflow-container">
			<div class="deck-modal-button" id="deck-modal-left-button"></div>
			<div class="deck-create-image-container">
				<img class="deck-create-image" id="deckImage11" height="200" src="http://www.mytcg.net/img/decks/11.png" />
				<img class="deck-create-image" id="deckImage12" height="200" src="http://www.mytcg.net/img/decks/12.png" />
				<img class="deck-create-image" id="deckImage13" height="200" src="http://www.mytcg.net/img/decks/13.png" />
				<img class="deck-create-image" id="deckImage14" height="200" src="http://www.mytcg.net/img/decks/14.png" />
			</div>
			<div class="deck-modal-button" id="deck-modal-right-button"></div>
		</div>
		<div class="deck-modal-button-container">
			<div class="cmdButton" id="createSaveDeckButton"></div>
		</div>
	</form>
	<div id="deck-select"></div>
	<div class="deck-available-heading"></div>
	<div id="deck-available"></div>
</div> -->