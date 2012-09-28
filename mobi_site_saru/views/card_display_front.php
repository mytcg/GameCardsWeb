<?php
$iCardID = $_GET['card_id'];
$query= 'SELECT C.card_id, I.description AS path, C.image, C.description, C.ranking ' 
		.'FROM mytcg_card AS C '
		.'INNER JOIN mytcg_usercard AS UC ON C.card_id = UC.card_id '
		.'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) '
		.'WHERE C.card_id = '.$iCardID.' AND UC.user_id = '.$user['user_id'];
$aCards=myqu($query);
$iCount = 0;
?>
<div id="card_display">
	<a href="index.php?page=card_display_back&card_id=<?php echo($iCardID); ?>">
		<img src="<?php echo($aCards[$iCount]['path']); ?>/cards/jpeg/<?php echo ($aCards[$iCount]['image']); ?>_front.jpg" border="0" width="95%" >
	</a>
</div>

