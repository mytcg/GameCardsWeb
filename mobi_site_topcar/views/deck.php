<?php
$iUserID = $user['user_id'];

if ($_GET['newdeck'] == 1)
 	{ ?>
 	<form method="GET" action="index.php?page=deck&createdeck=1" id="submitForm">
            <div class="profile_form">
            	Name<br />
            	<input type="text" name="deckname" value="<?php echo($aProfile[0]['answer']); ?>" size="35" maxlength="50" class="textbox" />
            </div>
            <input type="submit" value="SAVE" class="button" title="Login"/>
    </form>
    <?php 
	// echo "Your Deck ".$_GET['deckname']." has been created";
 	exit;}

if ($_GET['createdeck'] == 1){
	$iDescription=base64_decode($_GET['description']);
	$iCategoryID=$_GET['category_id'];
	$sDeck = createDeck($iUserID,$iCategoryID,$iDescription);
	echo $sDeck ;
	exit;
}
?>
<?php
if ($_GET['card']){
	$iCardID = $_GET['card'];
	$query= 'SELECT C.card_id, I.description AS path, C.image, C.description, C.ranking ' 
			.'FROM mytcg_card AS C '
			.'INNER JOIN mytcg_usercard AS UC ON C.card_id = UC.card_id '
			.'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) '
			.'WHERE C.card_id =  '.$iCardID.' AND UC.user_id = '.$user['user_id'];
	$aCards=myqu($query);
	$iCount = 0;
	?>
		<div id="card_display">
			<a href="index.php?page=card_display_back&card_id=<?php echo($iCardID); ?>"><img src="<?php echo($aCards[$iCount]['path']); ?>cards/jpeg/<?php echo ($aCards[$iCount]['image']); ?>_front.jpg" border="0" width="95%" ></a>
		</div>
<?php exit; }

if ($_GET['deletedeck'] == 1){
	$iDeckID=$_GET['deck'];
	myqu('UPDATE mytcg_usercard 
			SET deck_id = NULL  
			WHERE deck_id = '.$iDeckID);
	
	myqu('DELETE FROM mytcg_deck 
			WHERE deck_id = '.$iDeckID);

	echo "deck deleted";
	exit;
}
if ($_GET['addcardtodeck'] == 1){
	$iDeckID=$_GET['deck_id'];
	$iCardID=$_GET['card_id'];
	
	$cardQuery = myqu('SELECT usercard_id 
		FROM mytcg_usercard 
		WHERE user_id = '.$iUserID.' 
		AND card_id = '.$iCardID.' 
		AND deck_id IS NULL 
		AND usercardstatus_id = 1 
		LIMIT 1');
	
	$iUserCardID = $cardQuery[0]['usercard_id'];
	
	myqu('UPDATE mytcg_usercard 
			SET deck_id = '.$iDeckID.'  
			WHERE usercard_id = '.$iUserCardID);
	
	$returnCard = "<result>Card added to Deck!</result>";

	echo $returnCard;
	exit;
}

if (!$_GET['deck']){
	
	$query =  'SELECT D.description, D.image, C.description as catdesc, D.category_id, D.deck_id, I.description AS path '
			  .'FROM mytcg_deck D '
			  .'INNER JOIN mytcg_imageserver I ON (D.imageserver_id = I.imageserver_id) '  
			  .'INNER JOIN mytcg_category C ON (D.category_id = C.category_id) '         
			  ."WHERE D.user_id=$iUserID ";
	$aDecks = myqu($query);
	?>
	
		<ul id="item_list">
			<li><a href="index.php?page=deck&newdeck=1"><p>New Deck</p></a></li>
				<?php 
				foreach ($aDecks as $aDeck) {
					$iDeckID = $aDeck['deck_id'];
					$query =  	'SELECT COUNT(usercard_id)'
					   			.'FROM mytcg_usercard U '         
								."WHERE U.deck_id=$iDeckID";	  
					$aCardCount = myqu($query);
			
				?>
				<li><a href="index.php?page=deck&deck=<?php echo $aDeck['deck_id']; ?>">
					<p><?php echo $aDeck['description']; ?></p>
				</a></li>
				<?php
				}
				?>
		</ul>
<?php exit;}
else
	 {
		$iDeckID = intval($_GET['deck']);
		$query =  	'SELECT D.deck_id, U.usercard_id, CQ.description AS cardr, D.description, C.description as carddesc,U.card_id, C.image, I.description AS path, CAT.description as catdesc, C.ranking '
				   	.'FROM mytcg_usercard U '
					.'INNER JOIN mytcg_deck D ON (U.deck_id = D.deck_id)'
					.'INNER JOIN mytcg_card C ON (U.card_id = C.card_id)'
					.'INNER JOIN mytcg_cardquality CQ ON (C.cardquality_id = CQ.cardquality_id) '
					.'INNER JOIN mytcg_category CAT ON (CAT.category_id = D.category_id)' 
					.'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id) '           
					.'WHERE U.deck_id='.$iDeckID. ' ';
		$aDeck = myqu($query);
		
		?>
		<ul id="item_list">
			<li><a href="index.php?page=deck&deck=<?php echo $aDeck[0]['deck_id']; ?>&addcardtodeck=1"><p>Add Card</p></a></li>
			<li><a href="index.php?page=deck&deck=<?php echo $aDeck[0]['deck_id']; ?>&deletedeck=1"><p>Delete Deck</p></a></li>
		</ul>
		<ul id="card_list">
				<?php
					for($iCount = 0;$iCount < 10;$iCount++){
					$iCardID=$aDeck[$iCount]['card_id'];
					$iCC = getCardOwnedCount($iCardID,$iUserID);
					
				?>
				<li><a href="index.php?page=deck&card=<?php echo $aDeck[$iCount]['card_id']; ?>">
					<div class="cardBlock">
						<div class="album_card_pic">
							<?php if($iCardID != null){
							?>
								<img src="<?php echo($aDeck[$iCount]['path']); ?>/cards/jpeg/<?php echo($aDeck[$iCount]['image']); ?>_web.jpg" title="" >
							<?php } ?>
						</div>
						<div class="album-card-pic-container" style="background-image:url('<?php echo ($aDeck[$iCount]['path']); ?>/cards/jpeg/thumb.jpg')"></div>
						<div class="album_card_title">
			            	<?php echo $aDeck[$iCount]['carddesc']; ?>
			            	&nbsp;<?php echo ($iCC >= 1) ? "(".$iCC.")" : "" ; ?>
			            	<br />Quality:&nbsp;<?php echo $aDeck[$iCount]['cardr']; ?>
			            	<br />Rating:&nbsp;<?php echo $aDeck[$iCount]['ranking']; ?>
			            </div>
					</div>
				</a></li>
				<?php
				}
				?>
		</ul>
<?php
		exit;
	}?>
	


