<?php
$iCat = $_GET['category_id'];
$query= "SELECT SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) AS 'owner', I.description AS 'path', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
        (SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$user['user_id']." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
        FROM mytcg_market M
        JOIN mytcg_usercard UC USING (usercard_id)
        JOIN mytcg_card C USING (card_id)
        JOIN mytcg_imageserver I ON C.front_imageserver_id = I.imageserver_id
        JOIN mytcg_category CA ON C.category_id = CA.category_id
        JOIN mytcg_user U ON M.user_id = U.user_id
        WHERE M.markettype_id = 1 AND M.marketstatus_id = 1 AND C.category_id =".$iCat." ";
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
	$timeRemaining = $phpdate-(strtotime("now"));
	if ($timeRemaining>0) {
		$timeRemaining = date("H:i:s",$timeRemaining);
	} else {
		$timeRemaining = "Finished";
	}
	?>
		<ul id="card_list_bid">
			<li><a>
			<div class="cardBlockBid">
				<div class="album_card_pic">
					<img src="<?php echo($aAuctions[$iCount]['path']); ?>/cards/jpeg/<?php echo($aAuctions[$iCount]['image']); ?>_web.jpg" title="" >
				</div>
				<div class="album-card-pic-container" style="background-image:url('<?php echo ($aAuctions[$iCount]['path']); ?>cards/jpeg/thumb.jpg')"></div>
				<div class="album_card_title">
	    			<div>
	    				<?php echo($aAuctions[$iCount]['description']); ?>
		    			&nbsp;<?php $owned = $aAuctions[$iCount]['owned'];
	    				if($owned >= 0){
	    					echo "(".$owned.")";}
	    				elseif ($owned == 0){
	    					echo "(".$owned.")";
						}
						?>
					</div>
	    			<div>Seller:&nbsp;<?php echo($aAuctions[$iCount]['owner']); ?></div>
	    			<div>Time Left:&nbsp;<?php echo $timeRemaining; ?></div>
	    			<div><?php echo (sizeof($aHistory)>0) ? $aHistory[0]['price'] : $aAuctions[$iCount]['minimum_bid'] ; ?>&nbsp;TCG</div>
	    		  	<div>[<?php echo(sizeof($aHistory)); ?>&nbsp;bids]</div>
	    		  	<div><?php echo($aAuctions[$iCount]['price']); ?> TCG</div>
	    		  	<form method="POST" id="submitForm" action="index.php?page=auction_card&category_id="<?php $iCat ?>"">
			            <div class="profile_form">
			            	<input type="text" name="Name" value="<?php echo (sizeof($aHistory)>0) ? $aHistory[0]['price'] : $aAuctions[$iCount]['minimum_bid'] ; ?>" size="35" maxlength="50" class="textbox" />
			           	</div>
						<input type="submit" value="BID" class="button" title="Bid" alt="Bid"/>
						<input type="submit" value="BUY" class="button" title="Buy" alt="Buy"/>
			    	</form>
				</div>
			</div>
			</a></li>
    	</ul>
<?php 
	$iCount++;
	
    	}
?>   		
