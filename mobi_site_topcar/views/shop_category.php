<?php
$iPackID = $_GET['product_id'];
$iUserID = $user['user_id'];
	$qu = 'SELECT credits
		FROM mytcg_user where user_id = '.$iUserID;
	$aCreditsVal=myqu($qu);
$query='SELECT A.product_id, A.description, A.price, A.no_of_cards, A.image, '
      .'PT.description AS type, B.description AS imageserver '
      .'FROM mytcg_product A '
      .'INNER JOIN mytcg_imageserver B '
      .'INNER JOIN mytcg_producttype PT '
      .'ON A.thumbnail_imageserver_id=B.imageserver_id '
      .'WHERE A.product_id='.$iPackID.' AND A.producttype_id=PT.producttype_id ';
$aProducts=myqu($query);
$iCount = 0;
?>
<p class="text_important">Current Credits:&nbsp;<?php echo ($aCreditsVal[$iCount]['credits']) ?></p>
<?php
while ($iPackID=$aProducts[$iCount]['product_id']){
?>
	<ul id="card_list">
	<li><a href="home.php?page=shop_category_cards&product_id=<?php echo($iPackID); ?>">
		<div class="cardBlock">
      		<div class="album_card_pic">
	        <img src=" <?php echo($aProducts[$iCount]['imageserver']); ?>products/<?php echo($aProducts[$iCount]['image']); ?>.png" width="64" height="90" title="View potential cards">
	        </div>
	        <div class="album-card-pic-container"></div>
	        <div class="album_card_title">
	          <?php echo($aProducts[$iCount]['description']); ?>
	          <br />Credits:&nbsp;<?php echo($aProducts[$iCount]['price']); ?>
	          <br />Cards:&nbsp;<?php echo($aProducts[$iCount]['no_of_cards']); ?>
	          <br />Type:&nbsp;<?php echo($aProducts[$iCount]['type']); ?>
	        </div>
		</div>
	</a></li>
	</ul>
<?php
$iCount++;
}
?>