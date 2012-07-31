<?php

$query = 'SELECT A.product_id, 
			A.description, 
			A.price, 
			A.no_of_cards, 
			A.image, 
			A.background_position, 
			B.description AS imageserver 
			FROM mytcg_product A 
			INNER JOIN mytcg_imageserver B 
			ON A.full_imageserver_id=B.imageserver_id
			WHERE A.product_id >= 15
			AND A.product_id != 16
			ORDER BY no_of_cards ASC';
$aProducts = myqu($query);
$iCount = 0;
?>

<div class="headTitle">
		<div class="line"></div>
		<div class="head<?php echo $_GET['page']; ?>"></div>
</div>

<div id="shopPlate">
	<div id="shopScrollPane">
		<?php while ($iPackID=$aProducts[$iCount]['product_id']){ ?>
	   <div class="productBlock" >
	    	<img class="shop-pic" id="<?php echo($iPackID); ?>" src="<?php echo($aProducts[$iCount]['imageserver']); ?>products/<?php echo($aProducts[$iCount]['image']); ?>.jpg" title="View potential cards" alt="">
	    	<div class="numberOfCards"><span><?php echo($aProducts[$iCount]['no_of_cards']); ?> Card</span> Booster</div>
	    	<div class="productPrice"><span class="productPriceAmount"><?php echo($aProducts[$iCount]['price']); ?> TCG</span></div>
	    	<div class="buyItemButton" id="<?php echo($iPackID); ?>">
	    	Buy
	    	</div>
	    	<div id="<?php echo($iPackID); ?>" class="view_button">View</div>
	  </div>
	  <?php $iCount++; } ?>
	</div>
</div>