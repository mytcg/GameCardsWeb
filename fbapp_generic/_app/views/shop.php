<?php
$query='SELECT A.product_id, A.description, A.price, A.no_of_cards, A.image, A.background_position, B.description AS imageserver FROM mytcg_product A INNER JOIN mytcg_imageserver B ON A.full_imageserver_id=B.imageserver_id';
$aProducts=myqu($query);
$iCount = 0;
?>

<div class="headTitle">
		<div class="lineGrey" style="width:565px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="head<?php echo $_GET['page']; ?>"></div>
		<div class="lineGrey" style="width:30px;margin-left: 10px;"></div>
</div>

<div id="shopPlate">
	<div id="shopScrollPane">
		<?php while ($iPackID=$aProducts[$iCount]['product_id']){ ?>
	   <div class="productBlock" >
	  		<div class="productDescription"><?php echo($aProducts[$iCount]['description']); ?></div>
	    	<img class="shop-pic" id="<?php echo($iPackID); ?>" src="<?php echo($aProducts[$iCount]['imageserver']); ?>products/<?php echo($aProducts[$iCount]['image']); ?>.png" title="View potential cards">
	    	<div class="numberOfCards"><?php echo($aProducts[$iCount]['no_of_cards']); ?> Cards in pack</div>
	    	<div class="productPrice"><span class="productPriceAmount"><?php echo($aProducts[$iCount]['price']); ?></span> TCG</div>
	    	<div class="buyItemButton" id="<?php echo($iPackID); ?>">
	    	Buy
	    </div>
	  </div>
	  <?php $iCount++; } ?>
	</div>
</div>