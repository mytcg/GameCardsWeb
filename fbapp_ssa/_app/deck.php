<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

$userID = $_SESSION['userDetails']['user_id'];
$pre = $db['pre'];

if ((isset($_GET['deck_id']))&&(!isset($_GET['deck_image']))&&(!isset($_GET['deck_delete']))) {
	
	$deckID = $_GET['deck_id'];
	// $deckID = mysql_real_escape_string($_GET['deck_id']);
	
	$query = "SELECT COUNT(*) FROM mytcg_usercard WHERE user_id=$userID AND deck_id=$deckID";
	
	$deckCardsNumber = myqu($query);
	
	//echo $deckCardsNumber[0][0];
	
	if ($deckCardsNumber[0][0] > 0) {
		//echo 'option 1';
		$query = "SELECT U.usercard_id, 
						D.description, 
						C.description AS carddesc,
						U.card_id, 
						C.image,
						D.image AS deckimage, 
						I.description AS path, 
						CAT.description as catdesc 
						FROM mytcg_usercard U INNER JOIN 
						mytcg_deck D ON (U.deck_id = D.deck_id) INNER JOIN 
						mytcg_card C ON (U.card_id = C.card_id) INNER JOIN 
						mytcg_category CAT ON (CAT.category_id = D.category_id) INNER JOIN 
						mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id) 
						WHERE U.deck_id=$deckID AND U.user_id=$userID";
			
		$deckCards = myqu($query);
			
		echo '<cards>'.$sCRLF;
		echo '<count val="'.sizeof($deckCards).'"/>'.$sCRLF;
		echo '<deck val="'.$deckCards[0]['description'].'" />'.$sCRLF;
		echo '<deckimage val="'.$deckCards[0]['deckimage'].'" />'.$sCRLF;
		$count = 0;
		foreach($deckCards as $deckCard){
			echo $sTab.'<card_'.$count.'>'.$sCRLF;
			echo $sTab.$sTab.'<carddescription val="'.$deckCard['carddesc'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<usercardid val="'.$deckCard['usercard_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<imageid val="'.$deckCard['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.'<imagepath val="'.$deckCard['path'].'" />'.$sCRLF;
			echo $sTab.'</card_'.$count.'>'.$sCRLF;
			$count++;
		}
		echo '</cards>'.$sCRLF;
		
		exit;
	} else {
		
		//echo 'option 2';
		$query = "SELECT D.description, 
					D.image AS deckimage, 
					CAT.description as catdesc 
					FROM mytcg_deck D INNER JOIN 
					mytcg_category CAT ON (CAT.category_id = D.category_id) 
					WHERE D.deck_id=$deckID AND D.user_id=$userID";
					
		$deck = myqu($query);
		
		// print_r($deck);
		
		echo '<cards>'.$sCRLF;
		echo '<count val="0"/>'.$sCRLF;
		echo '<deck val="'.$deck[0]['description'].'" />'.$sCRLF;
		echo '<deckimage val="'.$deck[0]['deckimage'].'" />'.$sCRLF;
		echo '</cards>'.$sCRLF;
		
		exit;
	
	}
	
} 

if (isset($_GET['available_cards'])) {

	//get all available cards for the user (usercards with no deck id)
	
	$query =  "SELECT U.usercard_id, 
					U.card_id, 
					C.description, 
					C.image, 
					U.deck_id, 
					I.description AS path 
					FROM mytcg_usercard U 
					INNER JOIN mytcg_card C ON (U.card_id = C.card_id) 
					INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id) 
					WHERE U.user_id=$userID
					AND U.deck_id IS null";
	$aUserCards = myqu($query);
	
	if(sizeof($aUserCards) > 0){
	
		echo '<available_cards>'.$sCRLF;
		$count = 0;
		foreach($aUserCards as $aUserCard){
				echo $sTab.'<card_'.$count.'>'.$sCRLF;
				echo $sTab.$sTab.'<carddescription val="'.$aUserCard['description'].'" />'.$sCRLF;
				echo $sTab.$sTab.'<usercardid val="'.$aUserCard['usercard_id'].'" />'.$sCRLF;
				echo $sTab.$sTab.'<imageid val="'.$aUserCard['image'].'" />'.$sCRLF;
				echo $sTab.$sTab.'<imagepath val="'.$aUserCard['path'].'" />'.$sCRLF;
				echo $sTab.'</card_'.$count.'>'.$sCRLF;
				$count++;
			}
		echo '<count val="'.$count.'"/>'.$sCRLF;
		echo '</available_cards>'.$sCRLF;
	}

}

//if parameter is preset, write new deck name

if (isset($_GET['deckname'])) {
		
	if (isset($_GET['deckid'])) {
	
		$deck_id = $_GET['deckid'];
	
		$new_deck_name = $_GET['deckname'];
	
		$sql = "UPDATE {$pre}_deck 
						SET description = '{$new_deck_name}'
						WHERE deck_id = {$deck_id}
						AND user_id = {$userID}";
						
		myqu($sql);
	}
}

//if parameter is present, set new deck cover image 

if (isset($_GET['deckimage'])) {
	if (isset($_GET['deckid'])) {
		
		$deck_id = $_GET['deckid'];
		
		$new_deck_image = $_GET['deckimage'];
		
		$sql = "UPDATE {$pre}_deck 
						SET image = {$new_deck_image}
						WHERE deck_id = {$deck_id}
						AND user_id = {$userID}";
						
		myqu($sql);
	
	}
}

//if add parameter is present, add card to deck

if (isset($_GET['add'])) {
	
	$deck_id = $_GET['deckid'];
	
	$usercard_id = $_GET['cardid'];
	
	if (count($usercard_id) > 0) {
		
		$sql = "SELECT COUNT(usercard_id) 
			FROM mytcg_usercard 
			WHERE user_id = $userID
			AND deck_id = $deck_id";
				
		$cardsInDeck = myqu($sql);
				
		if ($cardsInDeck[0][0]<10) {
				
			$sql = "UPDATE ".$pre."_usercard 
					SET
						deck_id = ".$deck_id.",
						usercardstatus_id = 1 
					WHERE usercard_id = ".$usercard_id."
					AND user_id = ".$userID;
					
			myqu($sql);
			
			echo $usercard_id;
		} else {
			echo '<error val="Deck is full" />';
		}
	} else {
		echo '<error val="No card specified" />';
	}
}

//if 'remove' parameter is present, remove card from deck

if (isset($_GET['remove'])) {
	$usercard_id = $_GET['cardid'];
	
	$sql = "UPDATE ".$pre."_usercard UC 
			SET UC.deck_id = NULL
			WHERE UC.usercard_id = ".$usercard_id."
			AND UC.user_id = ".$userID.";";
	myqu($sql);
	
	$sql = "SELECT deck_id FROM ".$pre."_usercard UC WHERE UC.usercard_id = ".$usercard_id.";";
	$deck = myqu($sql);
	if(is_null($deck[0][0]))
	{
		echo '1';
	}
	else
	{
		echo '0';
	}
}

//if parameter is present, save information about deck

if (isset($_GET['save'])) {
	
	$deckDescription = addslashes($_GET['description']);
	$deckImage = $_GET['image'];
	$deckCategory = $_GET['category'];
	
		//insert new deck
		$sql = "INSERT INTO ".$pre."_deck (
					user_id, 
					category_id, 
					imageserver_id, 
					description, 
					image
				) VALUES (
					{$userID},
					{$deckCategory},
					1,
					'{$deckDescription}',
					'{$deckImage}'
				)";
				
		$result = myqu($sql);
		
		echo '<deck_id val="'.$result.'" />';
	
}

//get deck cover image using deckID

if (isset($_GET['deck_image'])) {
	
	$deckID = $_GET['deck_id'];
	$sql = "SELECT mytcg_imageserver.description, image 
				FROM mytcg_deck 
				INNER JOIN mytcg_imageserver 
				ON mytcg_imageserver.imageserver_id = mytcg_deck.imageserver_id
				WHERE deck_id={$deckID}";
	$result = myqu($sql);
	
	echo "<deck_image>";
	echo "<deck_image_url val=\"".$result[0][0]."decks/".$result[0][1].".png\" />";
	echo "</deck_image>";
	
}

if (isset($_GET['deck_delete'])) {
	
	$deckID = $_GET['deck_id'];
	$sql = "SELECT COUNT(*) FROM mytcg_gameplayer WHERE deck_id=$deckID";
	$result = myqu($sql);
	
	if ($result[0][0]<0) {
		echo "<deck_delete>";
		echo "<result val=\"0\" />";
		echo "<result_message val=\"Can\'t delete deck; deck is in active game.\" />";
		echo "</deck_delete>";
	} else {
		
		$sql = "UPDATE mytcg_usercard SET deck_id=NULL WHERE deck_id=$deckID";
		$result1 = myqu($sql);
		
		$sql = "DELETE FROM mytcg_deck WHERE deck_id=$deckID";
		$result2 = myqu($sql);
		
		echo $result1[0];
		echo $result2[0];
	}
	
}

if (isset($_GET['draw_selection'])) {
	
	$iUserID = $userID;

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
	
	// $deckCount = count($aDecks[0]);
	
	echo '<decks>'.$sCRLF;
	echo $sCRLF;
	$count=0;
	foreach ($aDecks as $aDeck) {
		$iDeckID = $aDeck['deck_id'];
		$query =  "SELECT COUNT(usercard_id) FROM mytcg_usercard U WHERE U.deck_id=$iDeckID";	  
		$aCardCount = myqu($query);
		
		echo '<deck_'.$count.'>'.$sCRLF;
		echo '<deck_id val="'.$aDeck['deck_id'].'" />'.$sCRLF;
		echo '<description val="'.$aDeck['description'].'" />'.$sCRLF;
		echo '<image_path val="'.$aDeck['path'].'decks/'.$aDeck['image'].'.png'.'" />'.$sCRLF;
		echo '<deck_category val="'.$aDeck['catdesc'].'" />'.$sCRLF;
		echo '<card_count val="'.$aCardCount[0][0].'" />'.$sCRLF;
		echo '</deck_'.$count.'>'.$sCRLF;
		echo $sCRLF;
		
		$count++;
	}
	echo '<deck_count val="'.$count.'" />'.$sCRLF;
	echo '</decks>'.$sCRLF;
	echo $sCRLF;
}


?>