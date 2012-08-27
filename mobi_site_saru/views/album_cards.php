<?php
$query= 'SELECT C.category_id, C.card_id, C.image, C.description, CQ.description AS cardr, I.description AS path, C.value, C.ranking '
        .'FROM mytcg_card C '
        .'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) '
        .'INNER JOIN mytcg_cardquality CQ ON (C.cardquality_id = CQ.cardquality_id) '
        .'ORDER BY C.description ASC ';
$aCards=myqu($query);
$iCount = 0;
?>
<?php
while($iCardID=$aCards[$iCount]['card_id']){
$iCC = getCardOwnedCount($iCardID,$user['user_id']);
?>
		<ul id="card_list">
		<li><a href="index.php?page=card_display_front&card_id=<?php echo($iCardID); ?>">
			<div class="cardBlock">
				<div class="album_card_pic">
				<?php if($iCC > 0){ ?>
					<img border="0" src="<?php echo($aCards[$iCount]['path']); ?>cards/jpeg/<?php echo ($aCards[$iCount]['image']); ?>_web.jpg" title="" >
				<?php } ?>
				</div>
	            <div class="album-card-pic-container"></div>
	            <div class="album_card_title">
	            	<?php echo $aCards[$iCount]['description']; ?>
	            	&nbsp;<?php echo ($iCC >= 1) ? "(".$iCC.")" : "" ; ?>
	            	<br /><?php echo $aCards[$iCount]['cardr']; ?>
	            	<br />Rating:&nbsp;<?php echo $aCards[$iCount]['ranking']; ?>
	            </div>
            </div>
		</a></li>
		</ul>
<?php
$iCount++;
}
?>

