<?php
$query = 'SELECT C.card_id, 
	C.image, 
	C.description, 
	I.description AS path, 
	C.value 
	FROM mytcg_card C 
	INNER JOIN mytcg_imageserver I 
	ON (C.front_imageserver_id = imageserver_id) 
	WHERE C.category_id > '.$catID.'
	ORDER BY C.description ASC';
	
$aCards=myqu($query);
$iCount = 0;
?>

<div class="headTitle">
		<div class="line"></div>
		<div class="head<?php echo $_GET['page']; ?>">
		</div>
</div>

<div id="albumPlate">
	<div id="album_scroll_pane">
	<?php
	while($iCardID=$aCards[$iCount]['card_id']){
	$iCC = getCardOwnedCount($iCardID,$user['user_id']);
	$showID = ($iCC == 0)? 0 : $iCardID ;
	$opacity = ($iCC == 0)? 0.1 : 1 ;
	$cursor = ($iCC == 0)? "" : "cursor:pointer;" ;
	?>
			<div class="cardBlock" id="<?php echo($iCardID); ?>">
				<div class="albumCardCount<?php if ($iCC > 1)echo ' active' ?>" id="count_<?php echo($iCardID); ?>"><?php if ($iCC > 1)echo $iCC; ?></div>
				<div class="album-card-drop-shadow"></div>
				<div class="album_card_pic">
				<?php if($iCC > 0){ ?>
					<img id="img_<?php echo($iCardID); ?>" src="<?php echo($aCards[$iCount]['path']); ?>/cards/jpeg/<?php echo($aCards[$iCount]['image']); ?>_web.jpg" title="" alt=''>
				<?php } ?>
				</div>
				<div id="<?php echo($showID); ?>" class="album-card-pic-container" style="<?php echo($cursor); ?>background-image:url('<?php echo($aCards[$iCount]['path']); ?>/cards/jpeg/<?php echo($aCards[$iCount]['image']); ?>_web.jpg')"></div>
				<span class="album_card_title" style="opacity:<?php echo($opacity); ?>;"><?php echo $aCards[$iCount]['description']; ?></span>
			</div>
	<?php
	$iCount++;
	}
	$vals = getCardInAlbumCount($user['user_id']);
	?>
	</div>
	<!-- <div id="menu-left-button" class="menu-scroll-button"></div>
	<div id="menu-right-button" class="menu-scroll-button"></div> -->
</div>
<div id="right_menu">
  <div id='all' class='right_menu_item'>All (<?php echo($vals[0]."/".$vals[1]) ?>)</div>
	<?php 
	
  $query = 'SELECT DISTINCT C.category_id, 
		  	CA.description  
		  	FROM mytcg_usercard UC 
			INNER JOIN mytcg_card C 
			ON UC.card_id = C.card_id 
			INNER JOIN mytcg_category CA 
			ON C.category_id = CA.category_id 
			INNER JOIN mytcg_usercardstatus UCS 
			ON UC.usercardstatus_id = UCS.usercardstatus_id 
			WHERE UC.user_id = '.$user['user_id'].' 
			AND C.category_id >= '.$catID.' 
			AND UCS.description = "Album" 
			ORDER BY CA.description ASC';
  
  $aAlbums=myqu($query);
  $iCount = 0;
  
  $yourCards = 0;
  $allCards = 0;
  
  while ($iCatID=$aAlbums[$iCount]['category_id']){
    $vals = getCardInAlbumCount($user['user_id'],$iCatID);
		echo "<div id='".$iCatID."' class='right_menu_item'>".$aAlbums[$iCount]['description']." (".$vals[0]."/".$vals[1].")</div>";
		$iCount++;
	}
	?>
</div>
		