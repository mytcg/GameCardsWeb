<?php
$iDeckID = intval($_GET['deck']);
$query =  	'SELECT U.usercard_id, CQ.description AS cardr, D.description, C.description as carddesc,U.card_id, C.image, I.description AS path, CAT.description as catdesc, C.ranking '
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
	<li><a href="#"><p>Add Card</p></a></li>
	<li><a href="#"><p>Delete Deck</p></a></li>
</ul>
<ul id="card_list">
		<?php
			for($iCount = 1;$iCount <= 10;$iCount++){
			$iCardID=$aDeck[$iCount]['card_id'];
			$iCC = getCardOwnedCount($iCardID,$user['user_id']);
		?>
		<li><a href="index.php?page=deck_select_card&card<?php echo $aDeck['card_id']; ?>">
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