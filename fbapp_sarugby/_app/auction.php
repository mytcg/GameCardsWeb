<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

if (isset($_GET['market']))
{
	$market_id = $_GET['market'];
  	$userID = $_SESSION['userDetails']['user_id'];
  
	if (isset($_GET['placebid']))
	{
		$bid_amount = $_GET['placebid'];
		
		//get user's available credits
		$query = myqu("SELECT premium FROM mytcg_user WHERE user_id = ".$userID);
		$available_credits = $query[0]['premium'];
		
		if($available_credits < $bid_amount)
		{
			//user has insuffient credits to place the bid
			echo '<result>'.$sCRLF;
			echo $sTab.'<value val="0" />'.$sCRLF;
			echo $sTab.'<message val="Insuffient credits to place bid. Only '.$available_credits.' TCG credits left." />'.$sCRLF;
			echo '</result>'.$sCRLF;
			exit;
		}
		else
		{
		  $sql = "SELECT C.description
      FROM mytcg_market M
      LEFT JOIN mytcg_usercard UC ON (M.usercard_id = UC.usercard_id)
      LEFT JOIN mytcg_card C ON (UC.card_id = C.card_id)
      WHERE M.market_id = ".$market_id."
      LIMIT 1";
      $carName = myqu($sql);
      $carName = $carName[0]['description'];
    
			//give credits back to current highest bidder
			$sql = "SELECT MC.user_id, MC.price
					FROM mytcg_market M
					JOIN mytcg_marketcard MC USING (market_id)
					WHERE M.market_id = ".$market_id."
					ORDER BY MC.price DESC
					LIMIT 1;";
			$lastBidderQuery = myqu($sql);
			if(sizeof($lastBidderQuery) > 0){
				foreach($lastBidderQuery as $lastBidder){
					myqu("UPDATE mytcg_user SET premium = premium+".$lastBidder['price']." WHERE user_id = ".$lastBidder['user_id']);
					//add transaction log
					myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
							VALUES(".$lastBidder['user_id'].", 'Refunded with ".$lastBidder['price']." credits for losing highest bid on ".$carName."', NOW(), ".$lastBidder['price'].")");
				}
			}
			
			//subtract credits from this user
			myqu("UPDATE mytcg_user SET premium = premium-".$bid_amount." WHERE user_id = ".$userID);
			//add transaction log
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Placed bid of ".$bid_amount." credits on ".$carName."', NOW(), -".$bid_amount.")");
							
			/*myqu("INSERT INTO sql_capture (sql_text) VALUES ('".$sql."')";
			myqu($sql);*/
			
			//place the bid
			$sql = "INSERT INTO mytcg_marketcard (market_id, user_id, price, date_of_transaction)
					VALUES (
						".$market_id.", 
						$userID, 
						$bid_amount,
						now()
					);";
			myqu($sql);
		}
	}
	
	//get user's credits
	$query = myqu("SELECT premium FROM mytcg_user WHERE user_id = ".$userID);
	$user_credits = $query[0]['premium'];
	
	// return auction data - xml
	$sql = "SELECT IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.card_id, M.* 
			FROM mytcg_market M
			JOIN mytcg_usercard UC USING (usercard_id)
			JOIN mytcg_card C USING (card_id)
			JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
			JOIN mytcg_category CA ON C.category_id = CA.category_id
			JOIN mytcg_user U ON M.user_id = U.user_id
			WHERE M.market_id = ".$market_id."
			ORDER BY M.date_expired ASC;";
	$aDetails = myqu($sql);
	
	echo '<result>'.$sCRLF;
	echo $sTab.'<credits val="'.$user_credits.'" />'.$sCRLF;
	echo $sTab.'<value val="1" />'.$sCRLF;
	echo $sTab.'<details>'.$sCRLF;
	if(sizeof($aDetails) > 0)
	{
		$auction = $aDetails[0];
		
		$sql = "SELECT MC.*, IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)), U.name
				FROM mytcg_marketcard MC
				JOIN mytcg_user U USING (user_id)
				WHERE MC.market_id = ".$market_id."
				ORDER BY MC.price DESC;";
		$aHistory = myqu($sql);
		
		echo $sTab.$sTab.'<card_id val="'.$auction['card_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<owner val="'.$auction['owner'].'" />'.$sCRLF;
		$mine = '0';
		if($auction['user_id'] == $userID){
			$mine = '1';
			$myAuctionsCount++;
		}
		echo $sTab.$sTab.'<mine val="'.$mine.'" />'.$sCRLF;
		$owned = ($auction['user_id'] == $userID) ? '1' : '0';
		echo $sTab.$sTab.'<owned val="'.$owned.'" />'.$sCRLF;
		echo $sTab.$sTab.'<market_id val="'.$auction['market_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<category_id val="'.$auction['category_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<category val="'.$auction['category'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<description val="'.$auction['description'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<image val="'.$auction['image'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<imageserver val="'.$auction['imageserver'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<price val="'.$auction['price'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<starting_bid val="'.$auction['minimum_bid'].'" />'.$sCRLF;
		$phpdate = strtotime($auction['date_expired']);
		echo $sTab.$sTab.'<expire val="'.date('D, j M Y H:i:s', $phpdate).'" />'.$sCRLF;
		echo $sTab.$sTab.'<date_expired val="'.$auction['date_expired'].'" />'.$sCRLF;
		$phpdate = strtotime($auction['date_created']);
		echo $sTab.$sTab.'<started val="'.date('D, j M Y', $phpdate).'" />'.$sCRLF;
		echo $sTab.$sTab.'<date_created val="'.$auction['date_created'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<history>'.$sCRLF;
		echo $sTab.$sTab.$sTab.'<bid_count val="'.sizeof($aHistory).'" />'.$sCRLF;
		if(sizeof($aHistory) > 0)
		{
			$b = 0;
			foreach($aHistory as $bid)
			{
				echo $sTab.$sTab.$sTab.'<bid_'.$b.'>'.$sCRLF;
				$bidder = ($bid['user_id'] == $userID) ? 'You' : $bid['username'];
				echo $sTab.$sTab.$sTab.$sTab.'<user val="'.$bidder.'" />'.$sCRLF;
				echo $sTab.$sTab.$sTab.$sTab.'<amount val="'.$bid['price'].'" />'.$sCRLF;
				echo $sTab.$sTab.$sTab.$sTab.'<date val="'.$bid['date_of_transaction'].'" />'.$sCRLF;
				echo $sTab.$sTab.$sTab.'</bid_'.$b.'>'.$sCRLF;
				$b++;
			}
		}
		echo $sTab.$sTab.'</history>'.$sCRLF;
	
	}
	echo $sTab.'</details>'.$sCRLF;
	echo '</result>'.$sCRLF;
}
if (isset($_GET['bid']))
{
	$market_id = $_GET['bid'];
	$bid_amount = $_GET['val'];
	$userID = $_SESSION['userDetails']['user_id'];
	//get user's available credits
	$query = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, ifnull(premium,0) prem, ifnull(credits,0) cred FROM mytcg_user WHERE user_id = ".$userID);
	$available_credits = $query[0]['premium'];
	$tcg_credits = $query[0]['cred'];
	$tcg_premium = $query[0]['prem'];
		
	if($available_credits < $bid_amount)
	{
		//user has insuffient credits to place the bid
		echo '<result>'.$sCRLF;
		echo $sTab.'<value val="0" />'.$sCRLF;
		echo $sTab.'<message val="Insuffient credits to place bid. Only '.$available_credits.' TCG credits remaining." />'.$sCRLF;
		echo '</result>'.$sCRLF;
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
			echo '<result>'.$sCRLF;
			echo $sTab.'<value val="0" />'.$sCRLF;
			echo $sTab.'<message val="Oops, this is your own auction, why not go bid on another auction?" />'.$sCRLF;
			echo '</result>'.$sCRLF;
			exit;
		}
    
		//give credits back to current highest bidder
		$sql = "SELECT MC.user_id, MC.price, MC.premium 
				FROM mytcg_market M
				JOIN mytcg_marketcard MC USING (market_id)
				WHERE M.market_id = ".$market_id."
				ORDER BY MC.price DESC
				LIMIT 1;";
		
		
		
		$lastBidderQuery = myqu($sql);
		
		$total = $lastBidder['price'] + $lastBidder['premium'];
		
		if(sizeof($lastBidderQuery) > 0){
			foreach($lastBidderQuery as $lastBidder){
				if ($bid_amount <= $total) {
					//user has insuffient credits to place the bid
					echo '<result>'.$sCRLF;
					echo $sTab.'<value val="0" />'.$sCRLF;
					echo $sTab.'<message val="Oops, the bid you tried to place was less than the current highest bidder. You need to bid more than '.$lastBidder['price'].'." />'.$sCRLF;
					echo '</result>'.$sCRLF;
					exit;
				}
				
				if ($lastBidder['user_id'] == $userID) {
					echo '<result>'.$sCRLF;
					echo $sTab.'<value val="0" />'.$sCRLF;
					echo $sTab.'<message val="You are already the highest bidder, no reason to spend more credits." />'.$sCRLF;
					echo '</result>'.$sCRLF;
					exit;
				}
				
				myqu("UPDATE mytcg_user SET premium = premium+".$lastBidder['premium'].", credits = credits+".$lastBidder['price']." WHERE user_id = ".$lastBidder['user_id']);
				
				//add transaction log
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
						VALUES(".$lastBidder['user_id'].", 'Refunded with ".$total." credits for losing highest bid on ".$carName."', NOW(), ".$total.")");
						
				myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
						VALUES(".$lastBidder['user_id'].", NULL, (SELECT usercard_id FROM mytcg_market WHERE market_id = ".$market_id."), (SELECT card_id FROM mytcg_usercard a, mytcg_market b WHERE a.usercard_id = b.usercard_id AND market_id = ".$market_id."), 
						now(), 'Refunded with ".$total." credits for losing highest bid on ".$carName."', ".$total.", ".$lastBidder['price'].", ".$lastBidder['premium'].", NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$lastBidder['user_id']."), 8)");
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
					now(), 'Placed bid of ".$bid_amount." credits on ".$carName."', -".$bid_amount.", -".$freemium_cost.", -".$premium_cost.", NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 7)");
		
		//place the bid
		$sql = "INSERT INTO mytcg_marketcard (market_id, user_id, price, date_of_transaction, premium)
				VALUES (".$market_id.",$userID,$freemium_cost,now(),$premium_cost);";
		myqu($sql);
		
		$sql = "SELECT MC.date_of_transaction, (ifnull(MC.price,0) + ifnull(MC.premium,0)) price, IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)) as username, U.name
				FROM mytcg_marketcard MC
				JOIN mytcg_user U USING (user_id)
				WHERE MC.market_id = ".$market_id."
				ORDER BY MC.price DESC;";
		$aHistory = myqu($sql);
		echo $sTab.$sTab.'<history>'.$sCRLF;
		echo $sTab.$sTab.$sTab.'<bid_count val="'.sizeof($aHistory).'" />'.$sCRLF;
		if(sizeof($aHistory) > 0)
		{
			$b = 0;
			foreach($aHistory as $bid)
			{
				echo $sTab.$sTab.$sTab.'<bid_'.$b.'>'.$sCRLF;
				$bidder = ($bid['user_id'] == $userID) ? 'You' : $bid['username'];
				echo $sTab.$sTab.$sTab.$sTab.'<user val="'.$bidder.'" />'.$sCRLF;
				echo $sTab.$sTab.$sTab.$sTab.'<amount val="'.$bid['price'].'" />'.$sCRLF;
				echo $sTab.$sTab.$sTab.$sTab.'<date val="'.$bid['date_of_transaction'].'" />'.$sCRLF;
				echo $sTab.$sTab.$sTab.'</bid_'.$b.'>'.$sCRLF;
				$b++;
			}
		}
		echo $sTab.$sTab.'</history>'.$sCRLF;
		
	}
}

if(isset($_GET['create']))
{
	$userID = $_SESSION['userDetails']['user_id'];
	$card_id = $_GET['card_id'];
	$minimum_bid = $_GET['minimum_bid'];
	$price = $_GET['price'];
	$date_expired = $_GET['date_expired'].' 23:59:59';
	
	//Get first available usercard
	$sql = "SELECT UC.`usercard_id`, UC.`deck_id`, C.`description`
			FROM mytcg_usercard UC 
			JOIN mytcg_card C USING (card_id)
			WHERE UC.`user_id`=".$userID." 
			AND UC.`card_id`=".$card_id."
			AND UC.`usercardstatus_id`=1
			ORDER BY UC.`deck_id` ASC, UC.`usercard_id` ASC
			LIMIT 1";
	$uc = myqu($sql);
	$usercard_id = $uc[0]['usercard_id'];
	
	//Create new auction record
	$sql = "INSERT INTO `mytcg_market` (
				`markettype_id`,
				`marketstatus_id`,
				`usercard_id`,
				`date_created`,
				`date_expired`,
				`price`,
				`minimum_bid`,
				`user_id`
			) VALUES (
				1,
				1,
				".$usercard_id.",
				'".date('Y-m-d H:i:00')."',
				'".$date_expired."',
				".$price.",
				".$minimum_bid.",
				".$userID."
			)";
			
	echo '<auction>'.$sCRLF;
	
	$auctionCost = intval(intval($minimum_bid) * 0.1);
	if(intval($price) > 0){
		$auctionCost = intval(intval($price) * 0.1);
	}
	$auctionCost = ($auctionCost < 5) ? 5 : $auctionCost;
	$userQuery = myqu("SELECT premium FROM mytcg_user WHERE user_id=".$userID);
	$userCredits = $userQuery[0]['premium'];
	//if ($userCredits > $auctionCost) {
		if($market_id = myqu($sql))
		{
			//Update usercard status
			$sql = "UPDATE mytcg_usercard SET usercardstatus_id=2, deck_id=NULL WHERE usercard_id=".$usercard_id." AND user_id=".$userID.";";
			myqu($sql);
			
			//Subtract auction creation cost from user
			$auctionCost = 0;//intval(intval($minimum_bid) * 0.1);
			//if(intval($price) > 0){
				//$auctionCost = intval(intval($price) * 0.1);
			//}
			//$auctionCost = ($auctionCost < 5) ? 5 : $auctionCost;
			//myqu("UPDATE mytcg_user SET premium = premium-".$auctionCost." WHERE user_id = ".$userID);
			
			//Add transactionlog entry
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Created an auction for ".$uc[0]['description']."', NOW(), 0)");
			
			$sql = "INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
					VALUES(".$userID.", NULL, ".$usercard_id.", (SELECT card_id FROM mytcg_usercard WHERE usercard_id = ".$usercard_id."), 
							now(), 'Created an auction for ".$uc[0]['description']."', 0, NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 6)";
			myqu($sql);
			
			//Get user credits
			$userQuery = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium FROM mytcg_user WHERE user_id=".$userID);
			$userCredits = $userQuery[0]['premium'];
			
			$sql = "SELECT COUNT(usercard_id) AS counted
				FROM mytcg_usercard UC 
				WHERE user_id=".$userID." 
				AND card_id=".$card_id."
				AND usercardstatus_id=1
				GROUP BY card_id";
			$cardcount = myqu($sql);
			$cardcount = $cardcount[0]['counted'];
			
			//Success
			echo $sTab.'<result val="success" />'.$sCRLF;
			echo $sTab.'<cost val="'.$auctionCost.'" />'.$sCRLF;
			echo $sTab.'<count val="'.$cardcount.'" />'.$sCRLF;
			echo $sTab.'<credits val="'.$userCredits.'" />'.$sCRLF;
		}
	/*}
	else
	{
		//Failed to create auction
		echo $sTab.'<result val="fail" />'.$sCRLF;
	}*/
	echo '</auction>';
}

if (isset($_GET['buy']))
{
	$marketID = $_GET['buy'];
  	$userID = $_SESSION['userDetails']['user_id'];
	$now = date('Y-m-d H:i:s');
	
	//Retrieve details of auction
	$sql = "SELECT usercard_id, user_id, price FROM mytcg_market WHERE market_id=".$marketID;
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
		echo '<result>'.$sCRLF;
		echo $sTab.'<value val="0" />'.$sCRLF;
		echo $sTab.'<message val="Insuffient credits to place bid. Only '.$available_credits.' TCG credits left." />'.$sCRLF;
		echo '</result>'.$sCRLF;
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
      ORDER BY MC.price DESC
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
							now(), 'Refunded with ".$total." credits for bid on bought out ".$carName."', ".$total.", ".$lastBidder['price'].", ".$lastBidder['premium'].",  NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$lastBidder['user_id']."), 8)");

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
	$sql = "UPDATE mytcg_usercard SET user_id=".$userID.", usercardstatus_id=1 WHERE usercard_id=".$usercardID.";";
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
					now(), 'Spent ".$price." credits on buyout of ".$carName."', -".$price.", -".$freemium_cost.", -".$premium_cost.", NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 13)");

	
	//Add credits to seller
	$sql = "UPDATE mytcg_user SET premium=(premium+".$freemium_cost."),credits=(credits+".$premium_cost.") WHERE user_id=".$oldowner.";";
	$temp[]=$sql;
	myqu($sql);
	//Add transaction log
	myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
			VALUES(".$oldowner.", 'Received ".$price." credits for buyout on ".$carName."', NOW(), ".$price.")");
			
	myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits,tcg_freemium,tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
			VALUES(".$oldowner.", NULL, ".$usercardID.", (SELECT card_id FROM mytcg_usercard WHERE usercard_id = ".$usercardID."), 
				now(), 'Received ".$price." credits for buyout on ".$carName."', ".$price.",".$freemium_cost.",".$premium_cost.", NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$oldowner."), 14)");

	
	//Close auction
	$sql = "UPDATE mytcg_market SET marketstatus_id=2 WHERE market_id=".$marketID.";";
	$temp[]=$sql;
	myqu($sql);
	
	//print_r($temp);
	
	//Return user credits
	$sql = "SELECT premium FROM mytcg_user WHERE user_id=".$userID.";";
	$u = myqu($sql);
	$iCreditsAfterPurchase = $u[0]['premium'];
	$xml = '';
	$xml.= '<response>'.$sCRLF;
	$xml.= $sTab.'<value>1</value>'.$sCRLF;
	$xml.= $sTab.'<credits>'.$iCreditsAfterPurchase.'</credits>'.$sCRLF;
	$xml.= $sTab.'<id>'.$marketID.'</id>'.$sCRLF;
	$xml.= '</response>'.$sCRLF;
	echo $xml;
	}
}
if (isset($_GET['load']))
{
	$cardID = $_GET['load'];
	$sql = "SELECT C.card_id,C.value,I.description AS 'imageserver',C.image,C.description AS cardname,CA.description AS category
			FROM mytcg_card C
	 		JOIN mytcg_category CA ON C.category_id = CA.category_id
			JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
			WHERE card_id=".$cardID;
	$a = myqu($sql);
	
	$xml = '';
	$xml.= '<response>'.$sCRLF;
	$xml.= $sTab.'<cardid>'.$a[0]['card_id'].'</cardid>'.$sCRLF;
	$xml.= $sTab.'<value>'.$a[0]['value'].'</value>'.$sCRLF;
	$xml.= $sTab.'<image>'.$a[0]['image'].'</image>'.$sCRLF;
	$xml.= $sTab.'<cardname>'.$a[0]['cardname'].'</cardname>'.$sCRLF;
	$xml.= $sTab.'<category>'.$a[0]['category'].'</category>'.$sCRLF;
	$xml.= $sTab.'<path>'.$a[0]['imageserver'].'</path>'.$sCRLF;
	$xml.= '</response>'.$sCRLF;
	echo $xml;
	exit;
}
if(isset($_GET['filter'])){
	
	$userID = $_SESSION['userDetails']['user_id'];
	switch ($_GET['filter']) {
	    case 'all':
	       $filter = "1";
	    break;
	    case 'notowned':
	        $filter = "owned = 0";
	    break;
	    case 'mine':
	        $filter = "uid = ".$userID;
	    break;
	    case 'other':
	        $filter = "uid != ".$userID;
	    break;
	    default:
			$trim_filter = $_GET['filter'];
			$trim_filter = ltrim($trim_filter);
	    	$filter = "cardname LIKE '%".$trim_filter."%'";
	    break;
	}
	
	$query= "SELECT * FROM
		(SELECT IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description AS cardname, C.image, C.category_id, UC.user_id AS uid, UC.usercard_id AS ucid, UC.card_id AS uccid, M.*,
        (SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$userID." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
        FROM mytcg_market M
        JOIN mytcg_usercard UC USING (usercard_id)
        JOIN mytcg_card C USING (card_id)
        JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
        JOIN mytcg_category CA ON C.category_id = CA.category_id
        JOIN mytcg_user U ON M.user_id = U.user_id
        WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
        ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC) tmpTable
        WHERE ".$filter;
	$aAuctions=myqu($query);
	$xml = "";
	$xml.= "<auctions>".$sCRLF;
	$xml.= $sTab."<iCount>".sizeof($aAuctions)."</iCount>".$sCRLF;
	$iCount = 0;
	while($iAuctionID=$aAuctions[$iCount]['market_id']){
  		$sql = "SELECT (ifnull(MC.price,0)+ifnull(MC.premium,0)) price , IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)) AS username, U.name
        FROM mytcg_marketcard MC
        JOIN mytcg_user U USING (user_id)
        WHERE MC.market_id = ".$iAuctionID."
        ORDER BY MC.price DESC;";
    	$aHistory = myqu($sql);
		$phpdate = strtotime($aAuctions[$iCount]['date_expired']);
		$price = (sizeof($aHistory) > 0) ? $aHistory[0]['price'] : $aAuctions[$iCount]['minimum_bid'] ;
		
		$xml.= $sTab."<auction_".$iCount.">".$sCRLF;
		$xml.= $sTab.$sTab."<auction_id>".$iAuctionID."</auction_id>".$sCRLF;
		$xml.= $sTab.$sTab."<card_id>".$aAuctions[$iCount]['uccid']."</card_id>".$sCRLF;
		$xml.= $sTab.$sTab."<imageserver>".$aAuctions[$iCount]['imageserver']."</imageserver>".$sCRLF;
		$xml.= $sTab.$sTab."<image>".$aAuctions[$iCount]['image']."</image>".$sCRLF;
		$xml.= $sTab.$sTab."<owned>".$aAuctions[$iCount]['owned']."</owned>".$sCRLF;
		$xml.= $sTab.$sTab."<description>".$aAuctions[$iCount]['cardname']."</description>".$sCRLF;
		$xml.= $sTab.$sTab."<owner>".$aAuctions[$iCount]['owner']."</owner>".$sCRLF;
		$xml.= $sTab.$sTab."<date_end>".date('D, j M Y H:i:s', $phpdate)."</date_end>".$sCRLF;
		$xml.= $sTab.$sTab."<bids>".sizeof($aHistory)."</bids>".$sCRLF;
		$xml.= $sTab.$sTab."<price>".$price."</price>".$sCRLF;
		$xml.= $sTab.$sTab."<buyout>".$aAuctions[$iCount]['price']."</buyout>".$sCRLF;
		$xml.= $sTab."</auction_".$iCount.">".$sCRLF;
		$iCount++;
	}
	$xml.= "</auctions>".$sCRLF;
	echo $xml;
}
?>
