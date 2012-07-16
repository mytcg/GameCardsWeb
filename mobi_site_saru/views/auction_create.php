<?php
$userID = $user['user_id'];
if (isset($_POST['buy']))
{
	$marketID = $_GET['market'];
	$newowner = $userID;
	$now = date('Y-m-d H:i:s');
	//Retrieve details of auction
	$sql = "SELECT usercard_id, user_id, price FROM mytcg_market WHERE market_id=".$marketID.";";
	$uc = myqu($sql);
	$usercardID = $uc[0]['usercard_id'];
	$oldowner = $uc[0]['user_id'];
	$price = $uc[0]['price'];
	
	//get user's available credits
	$query = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, credits, premium prem FROM mytcg_user WHERE user_id = ".$userID);
	$available_credits = $query[0]['premium'];
	$tcg_credits = $query[0]['credits'];
	$tcg_premium = $query[0]['prem'];
   
  if($available_credits < $price)
	{
		//user has insuffient credits to place the bid
		echo ("You Have Insuffient credits<br/>Return to <a href='index.php?page=home'>main menu</a>");
		exit;
	}
	else
		{
		 $sql = "SELECT C.description FROM mytcg_usercard UC JOIN mytcg_card C USING(card_id) WHERE UC.usercard_id=".$usercardID;
		 $carName = myqu($sql);
		 $carName = $carName[0]['description'];
		  
		 $temp = array();
		
		//give credits back to current highest bidder
		$sql = "SELECT MC.user_id, MC.price, C.description, MC.premium
		  FROM mytcg_market M
		  JOIN mytcg_marketcard MC USING (market_id)
		  LEFT JOIN mytcg_usercard UC ON (M.usercard_id = UC.usercard_id)
		  LEFT JOIN mytcg_card C ON (UC.card_id = C.card_id)
		  WHERE M.market_id = ".$marketID."
		  ORDER BY MC.marketcard_id DESC
		  LIMIT 1;";
		$lastBidderQuery = myqu($sql);
	  
		if(sizeof($lastBidderQuery) > 0){
			foreach($lastBidderQuery as $lastBidder){
				$total = $lastBidder['premium'] + $lastBidder['price'];
				myqu("UPDATE mytcg_user SET premium = premium+".$lastBidder['premium'].", credits = credits+".$lastBidder['price']." WHERE user_id = ".$lastBidder['user_id']);
				//add transaction log
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
						VALUES(".$lastBidder['user_id'].", 'Refunded with ".$total." credits for bid on bought out ".$carName."', NOW(), ".$total.")");
						
				myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
						VALUES(".$lastBidder['user_id'].", NULL, (SELECT usercard_id FROM mytcg_market WHERE market_id = ".$marketID."), (SELECT card_id FROM mytcg_usercard a, mytcg_market b WHERE a.usercard_id = b.usercard_id AND market_id = ".$marketID."), 
								now(), 'Refunded with ".$total." credits for bid on bought out ".$carName."', ".$total.", ".$lastBidder['price'].", ".$lastBidder['premium'].",  NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$lastBidder['user_id']."), 8)");
			}
		}
		
		$premium_cost = 0;
		$freemium_cost = 0;
		
		if ($tcg_credits <= $price) {
			$freemium_cost = $tcg_credits;
			$remainder = $price - $tcg_credits;
			$tcg_credits = 0;
			$premium_cost = $remainder;
			$tcg_premium = $tcg_premium - $remainder;
		} else {
			$tcg_credits = $tcg_credits - $price;
			$freemium_cost = $price;
		}
		
		//Change ownership of card
		$sql = "UPDATE mytcg_usercard SET user_id=".$newowner.", usercardstatus_id=1 WHERE usercard_id=".$usercardID.";";
		$temp[]=$sql;
		myqu($sql);
		
		//Record transaction
		$sql = "INSERT INTO mytcg_marketcard (market_id, usercard_id, price, premium, date_of_transaction, user_id) ".
				"VALUES(".$marketID.",".$usercardID.",".$freemium_cost.",".$premium_cost.",'".$now."',".$userID.");";
		$temp[]=$sql;
		myqu($sql);
		
		//Subtract credits from buyer
		$sql = "UPDATE mytcg_user SET premium=".$tcg_premium.",credits=".$tcg_credits." WHERE user_id=".$userID.";";
		$temp[]=$sql;
		myqu($sql);
		//Add transaction log
		myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
				VALUES(".$userID.", 'Spent ".$price." credits on buyout of ".$carName."', NOW(), -".$price.")");
				
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits,tcg_freemium,tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
				VALUES(".$userID.", NULL, ".$usercardID.", (SELECT card_id FROM mytcg_usercard WHERE usercard_id = ".$usercardID."), 
						now(), 'Spent ".$price." credits on buyout of ".$carName."', -".$price.", -".$freemium_cost.", -".$premium_cost.", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 13)");

						
		$our_freemium_fee = $freemium_cost * 0.1;
		$our_premium_fee = $premium_cost * 0.1;
		
		$freemium_cost = $freemium_cost - $our_freemium_fee;
		$premium_cost = $premium_cost - $our_premium_fee;
		$price = $freemium_cost + $premium_cost;
		
		//Add credits to seller
		$sql = "UPDATE mytcg_user SET premium=(premium+".$premium_cost."),credits=(credits+".$freemium_cost.") WHERE user_id=".$oldowner.";";
		$temp[]=$sql;
		myqu($sql);
		//Add transaction log
		myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
				VALUES(".$oldowner.", 'Received ".$price." credits for buyout on ".$carName."', NOW(), ".$price.")");
				
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits,tcg_freemium,tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
				VALUES(".$oldowner.", NULL, ".$usercardID.", (SELECT card_id FROM mytcg_usercard WHERE usercard_id = ".$usercardID."), 
					now(), 'Received ".$price." credits for buyout on ".$carName."', ".$price.",".$freemium_cost.",".$premium_cost.", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$oldowner."), 14)");
		
		$auction_fee = $our_freemium_fee + $our_premium_fee;
		
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits,tcg_freemium,tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
				VALUES(".$oldowner.", NULL, ".$usercardID.", (SELECT card_id FROM mytcg_usercard WHERE usercard_id = ".$usercardID."), 
					now(), 'Transaction fee of ".$auction_fee." credits for buyout on ".$carName."', ".$auction_fee.",".$our_freemium_fee.",".$our_premium_fee.", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$oldowner."), 17)");

		
		//Close auction
		$sql = "UPDATE mytcg_market SET marketstatus_id=2 WHERE market_id=".$marketID.";";
		$temp[]=$sql;
		myqu($sql);
		
		//print_r($temp);
		
		//Return user premium
		$sql = "SELECT ifnull(premium,0)+ifnull(credits,0) premium FROM mytcg_user WHERE user_id=".$userID.";";
		$u = myqu($sql);
		echo ("You now have ".$iCreditsAfterPurchase = $u[0]['premium']." credits<br/>");
		echo ("You have successfully purchased this auction.<br/>");
		echo ( $carName." belongs to you. <br/>Return to <a href='index.php?page=home'>main menu</a>");
		exit;
	}
}

if (isset($_POST['bid']))
{
	$market_id = $_GET['market'];
  	
	if (isset($_POST['bid']))
	{
		$bid_amount = $_POST['value'];
		// echo($bid_amount);
		// exit;
		//get user's available premium
		$query = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, ifnull(premium,0) prem, ifnull(credits,0) cred FROM mytcg_user WHERE user_id = ".$userID);
		$available_credits = $query[0]['premium'];
		$tcg_credits = $query[0]['cred'];
		$tcg_premium = $query[0]['prem'];
		
		if($available_credits < $bid_amount)
		{
			//user has insuffient credits to place the bid
			echo ("You Have Insuffient credits<br/>Return to <a href='index.php?page=home'>main menu</a>");
			exit;
		}
		else
		{
		  $sql = "SELECT C.description, M.user_id
	      FROM mytcg_market M
	      LEFT JOIN mytcg_usercard UC ON (M.usercard_id = UC.usercard_id)
	      LEFT JOIN mytcg_card C ON (UC.card_id = C.card_id)
	      WHERE M.market_id = ".$market_id."
	      LIMIT 1";
		  
	      $carName = myqu($sql);
		  $selectId = $carName[0]['user_id'];
	      $carName = $carName[0]['description'];
		
			if ($selectId == $userID) {
				echo 'Oops, this is your own auction, why not go bid on another auction? <br/>Return to <a href="index.php?page=auction_cards">Auctions</a>';
				exit;
			}
			//give premium back to current highest bidder
			$sql = "SELECT MC.user_id, MC.price, MC.premium 
					FROM mytcg_market M
					JOIN mytcg_marketcard MC USING (market_id)
					WHERE M.market_id = ".$market_id."
					ORDER BY MC.marketcard_id DESC
					LIMIT 1;";
			$lastBidderQuery = myqu($sql);
			if(sizeof($lastBidderQuery) > 0){
				foreach($lastBidderQuery as $lastBidder){
					$total = $lastBidder['price'] + $lastBidder['premium'];
					if ($bid_amount <= $total) {
						//user has insuffient credits to place the bid
						echo "Oops, the bid you tried to place was less than the current highest bidder. You need to bid more than ".$lastBidder['price'];
						exit;
					}
					
					if ($lastBidder['user_id'] == $userID) {
						echo "You are already the highest bidder, no reason to spend more credits." ;
						exit;
					}
					
					myqu("UPDATE mytcg_user SET premium = premium+".$lastBidder['premium'].", credits = credits+".$lastBidder['price']." WHERE user_id = ".$lastBidder['user_id']);
				
					//add transaction log
					myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
							VALUES(".$lastBidder['user_id'].", 'Refunded with ".$total." credits for losing highest bid on ".$carName."', NOW(), ".$total.")");
							
					myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
							VALUES(".$lastBidder['user_id'].", NULL, (SELECT usercard_id FROM mytcg_market WHERE market_id = ".$market_id."), (SELECT card_id FROM mytcg_usercard a, mytcg_market b WHERE a.usercard_id = b.usercard_id AND market_id = ".$market_id."), 
							now(), 'Refunded with ".$total." credits for losing highest bid on ".$carName."', ".$total.", ".$lastBidder['price'].", ".$lastBidder['premium'].", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$lastBidder['user_id']."), 8)");
				}
			}
			
			$freemium_cost = 0;
			$premium_cost = 0;
			
			if ($tcg_credits <= $bid_amount) {
				$freemium_cost = $tcg_credits;
				
				$remainder = $bid_amount - $tcg_credits;
				$tcg_premium = $tcg_premium - $remainder;
				$tcg_credits = 0;
				
				$premium_cost = $remainder;
			} else {
				$tcg_credits = $tcg_credits - $bid_amount;
				$freemium_cost = $bid_amount;
			}
			
			//subtract credits from this user
			myqu("UPDATE mytcg_user SET premium = ".$tcg_premium.", credits = ".$tcg_credits." WHERE user_id = ".$userID);
			//add transaction log
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Placed bid of ".$bid_amount." credits on ".$carName."', NOW(), -".$bid_amount.")");
					
			myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
				VALUES(".$userID.", NULL, (SELECT usercard_id FROM mytcg_market WHERE market_id = ".$market_id."), (SELECT card_id FROM mytcg_usercard a, mytcg_market b WHERE a.usercard_id = b.usercard_id AND market_id = ".$market_id."), 
						now(), 'Placed bid of ".$bid_amount." credits on ".$carName."', -".$bid_amount.", -".$freemium_cost.", -".$premium_cost.", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 7)");
			
			//place the bid
			$sql = "INSERT INTO mytcg_marketcard (market_id, user_id, price, date_of_transaction, premium)
					VALUES (".$market_id.",$userID,$freemium_cost,now(),$premium_cost);";
			myqu($sql);
		}
	}
	
	//get user's credits
	$query = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, ifnull(premium,0) prem, ifnull(credits,0) cred FROM mytcg_user WHERE user_id = ".$userID);
	$user_credits = $query[0]['premium'];
	
	// return auction data - xml
	$sql = "SELECT SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) AS 'owner', I.description AS 'path', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
        	(SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$user['user_id']." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
			FROM mytcg_market M
			JOIN mytcg_usercard UC USING (usercard_id)
			JOIN mytcg_card C USING (card_id)
			JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
			JOIN mytcg_category CA ON C.category_id = CA.category_id
			JOIN mytcg_user U ON M.user_id = U.user_id
			WHERE M.market_id = ".$market_id."
			ORDER BY M.date_expired ASC";
	$aDetails = myqu($sql);
	
	echo ("You now have ".$user_credits." credits.<br/>");
	echo ("You have successfully placed your bid.<br/>");
	
	if(sizeof($aDetails) > 0)
	{
		$aAuction = $aDetails[0];
		
		$sql = "SELECT MC.date_of_transaction, (ifnull(MC.price,0) + ifnull(MC.premium,0)) price, SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) as username, U.name
				FROM mytcg_marketcard MC
				JOIN mytcg_user U USING (user_id)
				WHERE MC.market_id = ".$market_id."
				ORDER BY MC.price DESC;";
		$aHistory = myqu($sql);
		?>
		<ul id="card_list_bid">
			<li>
			<div class="cardBlockBid">
				<div class="album_card_pic">
					<img src="<?php echo($aAuction['path']); ?>/cards/jpeg/<?php echo($aAuction['image']); ?>_web.jpg" title="" >
				</div>
				<div class="album-card-pic-container" style="background-image:url('<?php echo ($aAuction['path']); ?>cards/jpeg/thumb.jpg')"></div>
				<div class="album_card_title">
	    			<div>
	    				<?php echo($aAuction['description']); ?>
		    			&nbsp;<?php $owned = $aAuction['owned'];
	    				if($owned >= 0){
	    					echo "(".$owned.")";
						}
	    				elseif ($owned == 0){
	    					echo "(".$owned.")";
						}
						?>
					</div>
	    			<div>Seller:&nbsp;<?php echo($aAuction['owner']); ?></div>
	    			<div><?php echo (sizeof($aHistory)>0) ? $aHistory[0]['price'] : $aAuction['minimum_bid'] ; ?>&nbsp;TCG&nbsp;&nbsp;[<?php echo(sizeof($aHistory)); ?>&nbsp;bids]</div>
	    		  	<div><?php if ($aHistory[0]['username'] != null){
	    		  		echo ("Highest Bidder:&nbsp;".$aHistory[0]['username']."&nbsp;");
	    		  	} ?>
	    		  	</div>
	    		  	<div><?php echo($aAuction['price']); ?> TCG</div>
	    		  	<a href="index.php?page=auction_card"><div class="cmdButton">Back</div></a>
				</div>
			</div>
			</li>
    	</ul>
    	<?php
	}
exit;
}
?>