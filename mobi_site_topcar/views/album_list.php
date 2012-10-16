<?php 
$vals = getCardInAlbumCount($user['user_id']);
?>
<ul id="item_list">
  <li><p>All (<?php echo($vals[0]."/".$vals[1]) ?>)</p></li>
	<?php 
  $query=	 '	SELECT DISTINCT C.category_id, CA.description  
			    FROM mytcg_usercard UC 
			    INNER JOIN mytcg_card C ON UC.card_id = C.card_id 
			    INNER JOIN mytcg_category CA ON C.category_id = CA.category_id 
			    INNER JOIN mytcg_usercardstatus UCS ON UC.usercardstatus_id = UCS.usercardstatus_id 
			    ORDER BY CA.description ASC ';
			
  $aAlbums=myqu($query);
  $iCount = 0;
  
  $yourCards = 0;
  $allCards = 0;
  
  while ($iCatID=$aAlbums[$iCount]['category_id']){
    $vals = getCardInAlbumCount($user['user_id'],$iCatID);
	?>
		<li><a href='index.php?page=album_card&category_id=<?php echo $iCatID ?>'><p><?php echo $aAlbums[$iCount]['description']?> (<?php echo $vals[0]?>/<?php echo $vals[1]?>)</p></a></li>
	<?php
		$iCount++;
	}
	?>
</ul>