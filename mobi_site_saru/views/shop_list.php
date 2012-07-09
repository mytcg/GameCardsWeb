<?php
	$iUserID = $user['user_id'];
	$qu = 'SELECT (ifnull(premium,0)+ifnull(credits,0)) credits, credits cred, premium prem FROM mytcg_user WHERE user_id='.$iUserID;
	$aCreditsVal=myqu($qu);
$query='SELECT A.product_id, A.description, A.premium, A.no_of_cards, A.image, '
      .'PT.description AS type, B.description AS imageserver '
      .'FROM mytcg_product A '
      .'INNER JOIN mytcg_imageserver B '
      .'INNER JOIN mytcg_producttype PT '
      .'ON A.thumbnail_imageserver_id=B.imageserver_id '
      .'WHERE A.producttype_id=PT.producttype_id '
      .'ORDER BY A.product_id ASC ';
$aProducts=myqu($query);

$iCount = 0;

?>
<p class="text_important">Current Credits:&nbsp;<?php echo ($aCreditsVal[$iCount]['credits']) ?></p>
<?php
while ($iPackID=$aProducts[$iCount]['product_id']){
?>
	<ul id="card_list">
	<li><a href='index.php?page=shop_category&product_id=<?php echo($iPackID); ?>'>
		<div class="cardBlock">
			<div class="album_card_pic">
			<img src="<?php echo($aProducts[$iCount]['imageserver']); ?>products/<?php echo($aProducts[$iCount]['image']); ?>.jpg" width="64" height="90" title="View potential cards">
	        </div>
	        <div class="album-card-pic-container"></div>
	        <div class="album_card_title">
	          <?php echo($aProducts[$iCount]['description']); ?>
	          <br />Credits:&nbsp;<?php echo($aProducts[$iCount]['premium']); ?>
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
 