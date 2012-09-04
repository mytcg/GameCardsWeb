<?php
$iUserID = $user['user_id'];
$query =  "SELECT competitiondeck_id, imageserver_id, description, image, end_date FROM mytcg_competitiondeck WHERE active IN (1,2)";	  
$aCD = myqu($query);
?>

<div class="headTitle">
		<div class="head<?php echo $_GET['page']; ?>">
			<span>DECK</span>
		</div>
</div>

<div id="deckPlate">
	<div class="deck-top-menu-bar">
	</div>
	<div class="scroll_pane">	
	<?php 
	foreach ($aCD as $aCData) {
		$iCompDeckID = $aCData['competitiondeck_id'];
		$query = "SELECT D.*, I.description AS path
				  FROM mytcg_deck D
				  INNER JOIN mytcg_imageserver I ON (D.imageserver_id = I.imageserver_id)
				  WHERE competitiondeck_id = ".$iCompDeckID." AND user_id = ".$iUserID;
		$aGetDeck = myqu($query);
		if(sizeof($aGetDeck)==0){
			$query = "INSERT INTO mytcg_deck (user_id, category_id, imageserver_id, description, image, type, competitiondeck_id)
					  VALUES ({$iUserID},1,{$aCData['imageserver_id']},'{$aCData['description']}','{$aCData['image']}',2,{$iCompDeckID})";
			$rInsert = myqu($query);
		}
	}
	$query = "SELECT D.*, I.description AS path, CD.end_date
			  FROM mytcg_deck D
			  INNER JOIN mytcg_imageserver I ON (D.imageserver_id = I.imageserver_id)
			  INNER JOIN mytcg_competitiondeck CD ON (D.competitiondeck_id = CD.competitiondeck_id)
			  WHERE user_id = ".$iUserID;
	$aDecks = myqu($query);
	for($i=0;$i < sizeof($aDecks);$i++){
		$totals = getCardInDeckCount($aDecks[$i]['deck_id'],$iUserID);
	?>
	<div class="deck-container">
		<?php echo $aDecks[$i]['description']; ?>
		<div class="deck-image-container">
			<a href="index.php?page=deckbuild&deck=<?php echo $aDecks[$i]['deck_id']; ?>">
				<img src="<?php echo $aDecks[$i]['path'].'decks/'.$aDecks[$i]['image'].'.png'; ?>" height='140' alt="deck-image" />
			</a>
		</div>
		<div class="deck-attributes">
			<table class="deck-attributes-table">
				<tr>
					<td>Cards: </td><td id="deck-cards"><?php echo($totals[0]); ?> / <?php echo($totals[1]); ?></td>
				</tr>
			</table>
		</div>
	</div>
	<?php }?>
	</div>
</div>