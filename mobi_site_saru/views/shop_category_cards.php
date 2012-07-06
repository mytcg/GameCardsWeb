<p class="text_important">Current Credits:&nbsp;(User Credits)</p>
<?php
while ($iPackID=$aProducts[$iCount]['product_id']){
?>
	<ul id="card_list">
	<li><a href="home.php?page=text_important&common='<?php echo ($iPackID); ?>' ">
		<div class="cardBlock">
      		<div class="album_card_pic">
	        <img src=" <?php echo($aProducts[$iCount]['path']); ?>products/<?php echo($aProducts[$iCount]['image']); ?>.png" width="64" height="90" title="View potential cards">
	        </div>
	        <div class="album-card-pic-container"></div>
	        <div class="album_card_title">
	          <?php echo($aProducts[$iCount]['description']); ?>
	          <br />Credits:&nbsp;<?php echo($aProducts[$iCount]['price']); ?>
	          <br />Cards:&nbsp;<?php echo($aProducts[$iCount]['no_of_cards']); ?>
	          <br />Type:&nbsp;Booster
	        </div>
		</div>
	</a></li>
	</ul>
<?php
$iCount++;
}
?>
