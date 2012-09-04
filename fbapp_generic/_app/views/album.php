<?php
$query = 'SELECT C.card_id, 
	C.image, 
	C.description, 
	I.description AS path, 
	C.value 
	FROM mytcg_card C 
	INNER JOIN mytcg_imageserver I 
	ON (C.front_imageserver_id = imageserver_id) 
	
	ORDER BY C.description ASC';
	
$aCards=myqu($query);
$iCount = 0;
?>

<div class="headTitle">
		<div class="lineGrey" style="width:535px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="head<?php echo $_GET['page']; ?>"></div>
		<div class="lineGrey" style="width:30px;margin-left: 10px;"></div>
</div>

<div id="albumPlate">
	<div id="album_scroll_pane">
	<?php
	while($iCardID=$aCards[$iCount]['card_id']){
	$iCC = getCardOwnedCount($iCardID,$user['user_id']);
	?>
			<div class="cardBlock" id="<?php echo($iCount); ?>">
				<div class="albumCardCount<?php if ($iCC > 1)echo ' active' ?>" id="count_<?php echo($iCount); ?>"><?php if ($iCC > 1)echo $iCC; ?></div>
				<div class="album-card-drop-shadow"></div>
				<div class="album_card_pic">
				<?php if($iCC > 0){ ?>
					<img id="img_<?php echo($iCardID); ?>" src="<?php echo($aCards[$iCount]['path']); ?>/cards/jpeg/<?php echo($aCards[$iCount]['image']); ?>_web.jpg" title="" >
				<?php } ?>
				</div>
				<div id="<?php echo($iCardID); ?>" class="album-card-pic-container" style="background-image:url('<?php echo($aCards[$iCount]['path']); ?>/cards/jpeg/<?php echo($aCards[$iCount]['image']); ?>_web.jpg')"></div>
				<div class="album_card_title"><?php echo $aCards[$iCount]['description']; ?></div>
			</div>
	<?php
	$iCount++;
	}
	$vals = getCardInAlbumCount($user['user_id']);
	?>
	</div>
	<div id="menu-left-button" class="menu-scroll-button"></div>
	<div id="menu-right-button" class="menu-scroll-button"></div>
</div>
<div id="right_menu">
  <div id='all' class='right_menu_item sub1'>All (<?php echo($vals[0]."/".$vals[1]) ?>)</div>
	<?php 
	$query='SELECT DISTINCT category_id, description, level 
	        FROM mytcg_category  
	        WHERE category_id IN (2,52,58)';
	$aAlbums=myqu($query);
	
	//LEVEL 1
	for($a = 0;$a < sizeof($aAlbums);$a++){
		$vals = getCardInAlbumCount($user['user_id'],$aAlbums[$a]['category_id']);
		echo "<div id='".$aAlbums[$a]['category_id']."' class='right_menu_item sub1'>".$aAlbums[$a]['description']." (".$vals[0]."/".$vals[1].")</div>";
	
		//LEVEL 2
		$query='SELECT DISTINCT category_id, description, level 
			    FROM mytcg_category  
			    WHERE parent_id = '.$aAlbums[$a]['category_id'];
		$aSub1=myqu($query);
		for($b = 0;$b < sizeof($aSub1);$b++){
			$vals = getCardInAlbumCount($user['user_id'],$aSub1[$b]['category_id']);
			$css = ($aSub1[$b]['level']=="2")? "sub2" : "sub3" ;
			echo "<div id='".$aSub1[$b]['category_id']."' class='right_menu_item {$css}'>".$aSub1[$b]['description']." (".$vals[0]."/".$vals[1].")</div>";
		
			//LEVEL 3
			$query='SELECT DISTINCT category_id, description, level 
				    FROM mytcg_category  
				    WHERE parent_id = '.$aSub1[$b]['category_id'];
			$aSub2=myqu($query);
			for($c = 0;$c < sizeof($aSub2);$c++){
				$vals = getCardInAlbumCount($user['user_id'],$aSub2[$c]['category_id']);
				$css = ($aSub2[$c]['level']=="2")? "sub2" : "sub3" ;
				echo "<div id='".$aSub2[$c]['category_id']."' class='right_menu_item {$css}'>".$aSub2[$c]['description']." (".$vals[0]."/".$vals[1].")</div>";
			}
		
		}
	}
?>
</div>
<div id="cardBigView">
	<div class="eyeIcon"></div>
	<div class="auctionIcon"></div>
</div>
		