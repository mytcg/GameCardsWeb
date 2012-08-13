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
        ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC;";
$aAuctions=myqu($query);
$iCount = 0;
?>

<div class="headTitle">
		<div class="lineGrey" style="width:525px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="head<?php echo $_GET['page']; ?>"></div>
		<div class="lineGrey" style="width:30px;margin-left: 10px;"></div>
</div>

<div id="auctionPlate">

	<div id="auction_buttons" >
	    		<div class="cmdButton" id="all">All Auctions</div>
	    		<div class="cmdButton" id="other">Other Auctions</div>
	    		<div class="cmdButton" id="mine">My Auctions</div>
	    		<div id="auction_search_container">
	    			<input type="text" id="auction_search_field" size="27" value="search">
	    			<div id="auction_search_button" class="textSearch"></div>
	    		</div>
	</div>
	<div  id="auction_scroll_pane" >
	    	<?php
	    		while($iAuctionID=$aAuctions[$iCount]['market_id']){
	      		$sql = "SELECT MC.marketcard_id, MC.market_id, MC.usercard_id, MC.card_id, (ifnull(MC.Price,0)+ifnull(MC.premium,0)) price, MC.date_of_transaction, MC.user_id, MC.premium , U.username, U.name
	            FROM mytcg_marketcard MC
	            JOIN mytcg_user U USING (user_id)
	            WHERE MC.market_id = ".$iAuctionID."
	            ORDER BY MC.marketcard_id DESC;";
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
					<div class="auction_car_name"><?php echo($aAuctions[$iCount]['description']); ?></div>
	    			<div class="picture-box" id="<?php echo($aAuctions[$iCount]['card_id']); ?>" style="width:64px;height:90px;background-image:url(<?php echo($aAuctions[$iCount]['imageserver']); ?>cards/jpeg/<?php echo($aAuctions[$iCount]['image']); ?>_web.jpg)">
	    			</div>
	    			<div class="auction_block_details">
		    			<div class="auction_seller_name">Seller: <?php echo($aAuctions[$iCount]['owner']); ?></div>
		    			<div class="auction_time_remaining">Time Left: <span><?php echo $timeRemaining; ?></span></div>
		    			<div class="bids-info-container">
		    				<div class="current_bid_price"><span><?php echo (sizeof($aHistory)>0) ? $aHistory[0]['price'] : $aAuctions[$iCount]['minimum_bid']; ?></span> TCG</div>
		    		  	 	<div class="number_of_bids"><span><?php echo(sizeof($aHistory)); ?></span> bids </div>
		    		  	</div>
		    			<div class="buyout"><br><span><?php echo($aAuctions[$iCount]['price']); ?></span> TCG</div>
		    		</div>
		    		<div class="auctionBlockIcons">
			    		<?php  if ($aAuctions[$iCount]['owned']>0) {
			    			echo '<div class="starIcon"></div>';
						} ?>
			    		<div class="offEyeIcon"></div>
			    		<div class="eyeIcon viewAuctionButton" id="<?php echo($iAuctionID); ?>"></div>
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