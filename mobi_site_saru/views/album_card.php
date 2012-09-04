<?php
$iCatID = $_GET['category_id'];
$query = 	'SELECT C.card_id, I.description AS path, C.category_id, CQ.description AS cardr, C.image, C.description, C.ranking ' 
			.'FROM mytcg_card AS C '
			.'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) '
			.'INNER JOIN mytcg_cardquality CQ ON (C.cardquality_id = CQ.cardquality_id) '
			.'WHERE C.category_id =  '.$iCatID.' ';

$aCards=myqu($query);
$iCount = 0;
?>
<?php
while($iCardID=$aCards[$iCount]['card_id']){
$iCC = getCardOwnedCount($iCardID,$user['user_id']);
?>
		<ul id="card_list">
			<li>
				<div class="cardBlock">
					<div class="album_card_pic">
					<?php if($iCC > 0){ ?>
						<a href="index.php?page=card_display_front&card_id=<?php echo($iCardID); ?>">
							<img class="image_size" border="0" src="<?php echo($aCards[$iCount]['path']); ?>cards/jpeg/<?php echo ($aCards[$iCount]['image']); ?>_web.jpg" title="" >
						</a>
					<?php }else{ ?>
							<img class="image_size" border="0" src="<?php echo($aCards[$iCount]['path']); ?>cards/jpeg/<?php echo ($aCards[$iCount]['image']); ?>_web.jpg" title="" >
					<?php } ?>
					</div>
					<div class="album-card-pic-container"></div>
					<div class="album_card_title">
						<?php echo $aCards[$iCount]['description']; ?>
						&nbsp;<?php echo ($iCC >= 0) ? "(".$iCC.")" : "" ; ?>
						<br /><?php echo $aCards[$iCount]['cardr']; ?>
						<br />Rating:&nbsp;<?php echo $aCards[$iCount]['ranking']; ?>
						<?php if($iCC > 0){ ?>
						<div id="buttonContainer">
							<a href="index.php?page=auction_card&auction_card=<?php echo($iCardID); ?>">
								<div class="cmdButton" style="width:90px">Auction Card</div>
							</a>
						</div>
						<?php } ?>
					</div>
				</div>
			</li>
		</ul>
<?php
$iCount++;
}
?>
<div><a href="index.php?page=album_list"><div class="cmdButton" style="margin-top:5px;padding-top:8px;height:17px;">BACK</div></a></div>
