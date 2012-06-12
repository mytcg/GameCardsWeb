<?php
$query= "SELECT IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
        (SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$user['user_id']." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
        FROM mytcg_market M
        JOIN mytcg_usercard UC USING (usercard_id)
        JOIN mytcg_card C USING (card_id)
        JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
        JOIN mytcg_category CA ON C.category_id = CA.category_id
        JOIN mytcg_user U ON M.user_id = U.user_id
        WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
        ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC";
$aAuctions=myqu($query);
$iCount = 0;
?>
<div class="headTitle">
		<div class="head<?php echo $_GET['page']; ?>">
			<span>card</span> auctions
		</div>
</div>

<div id="auctionPlate">

	<div id="auction_buttons" >
	    		
	    		<div class="cmdButton" id="other">All Auctions</div>
	    		<div class="cmdButton" id="mine">My Auctions</div>
	    		<div id="auction_search_container">
	    			<input type="text" id="auction_search_field" size="27" value="search">
	    			<div id="auction_search_button" class="textSearch"></div>
	    		</div>
	</div>
	<div  id="auction_scroll_pane" >
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
					<div class="auctionBlockLarge" id="win_<?php echo($iAuctionID); ?>">
					
	    			<div class="picture-box-container">
	    				<div class="picture-box" id="<?php echo($iAuctionID); ?>" style="width:64px;height:90px;background-image:url(<?php echo($aAuctions[$iCount]['imageserver']); ?>cards/jpeg/<?php echo($aAuctions[$iCount]['image']); ?>_web.jpg)"></div>
	    			</div>
	    			<div class="auction_block_details">
	    				<div class="auction_car_name"><span style="color:#EFEFEF;"><?php echo($aAuctions[$iCount]['description']); ?></span></div>
		    			<div class="auction_seller_name">Seller: <?php echo($aAuctions[$iCount]['owner']); ?></div>
		    			<div class="auction_time_remaining">Time Left: <?php echo $timeRemaining; ?></div>
		    			<div class="bids-info-container"><br/>
		    				<div class="current_bid_price"><span><?php echo (sizeof($aHistory)>0) ? $aHistory[0]['price'] : $aAuctions[$iCount]['minimum_bid']; ?> TCG</span>&nbsp;&nbsp;&nbsp;&nbsp;[<?php echo(sizeof($aHistory)); ?> bids] </div>
		    		  	</div>
						<?php if (($aAuctions[$iCount]['price']) > 0) { 
							echo '<div class="buyout"><span>'.$aAuctions[$iCount]['price'].'</span> TCG</div>';
							echo '<div class="trolleyIcon"></div>';
						} else {
							echo '<div class="blank"></div>';
						}?>
						
						<div class="offEyeIcon"></div>
			    		<div class="eyeIcon viewAuctionButton" id="<?php echo($iAuctionID); ?>">view</div>
		    		</div>
		    		<div class="auctionBlockIcons">
			    		<?php  if ($aAuctions[$iCount]['owned']>0) {
			    			echo '<div class="starIcon"></div>';
						} ?>
		    		</div>
	    		</div>
	    		<?php 
				$iCount++;
	    			}
	    	 	?>   		
	</div>
	<div id="menu-left-button" class="menu-scroll-button"></div>
	<div id="menu-right-button" class="menu-scroll-button"></div>
</div>