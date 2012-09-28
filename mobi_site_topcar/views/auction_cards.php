<?php
$iCatID = $_GET['category_id'];
$query= "SELECT I.description AS 'path', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
        (SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$user['user_id']." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
        FROM mytcg_market M
        JOIN mytcg_usercard UC USING (usercard_id)
        JOIN mytcg_card C USING (card_id)
        JOIN mytcg_imageserver I ON C.front_imageserver_id = I.imageserver_id
        JOIN mytcg_category CA ON C.category_id = CA.category_id
        JOIN mytcg_user U ON M.user_id = U.user_id
        WHERE C.category_id =".$iCatID."
        ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC ";
$aAuctions=myqu($query);
$iCount = 0;
?>
<?php
	while($iAuctionID=$aAuctions[$iCount]['market_id']){
	$sql = "SELECT MC.* , U.username, U.name
            FROM mytcg_marketcard MC
            JOIN mytcg_user U USING (user_id)
            WHERE MC.market_id = ".$iAuctionID."
            ORDER BY MC.price DESC;";
	$aHistory = myqu($sql);
	$phpdate = strtotime($aAuctions[$iCount]['date_expired']);
	?>
		<ul id="card_list">
			<li><a href="index.php?page=auction_card&category_id=<?php echo($iCatID); ?>">
			<div class="cardBlock">
				<div class="album_card_pic">
					<img src="<?php echo($aAuctions[$iCount]['path']); ?>/cards/jpeg/<?php echo($aAuctions[$iCount]['image']); ?>_web.jpg" title="" >
				</div>
				<div class="album-card-pic-container" style="background-image:url('<?php echo ($aAuctions[$iCount]['path']); ?>/cards/jpeg/thumb.jpg')"></div>
				<div class="album_card_title">
	    			<p style='line-height:16px;padding-top:0px'><?php echo($aAuctions[$iCount]['description']); ?>
	    			&nbsp;<?php $owned = $aAuctions[$iCount]['owned'];
    				if($owned >= 0){
    					echo "(".$owned.")";}
    				elseif ($owned == 0){
    					echo "(".$owned.")";
					}
					?>
	    			<br/ >Opening Bid:&nbsp;<?php echo (sizeof($aHistory)>0) ? $aHistory[0]['price'] : $aAuctions[$iCount]['minimum_bid'] ; ?>&nbsp;TCG
					<br/ >Time Left:&nbsp;<?php echo(date('y-m-d h:i:s', $phpdate)); ?></p>
				</div>
			</div>
			</a></li>
    	</ul>
<?php 
	$iCount++;
    	}
?>   		
