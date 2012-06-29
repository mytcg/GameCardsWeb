<?php
require_once "../../func.php";

$sCRLF="\r\n";
$sTab=chr(9);

$pre = $Conf["database"]["table_prefix"];

$userID = $_SESSION["user"]["id"];


if (intval($_GET['init']==1))
{
	if(strlen($userID) > 0){
		$sql = "SELECT SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
					(SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$userID." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
				FROM mytcg_market M
				JOIN mytcg_usercard UC USING (usercard_id)
				JOIN mytcg_card C USING (card_id)
				JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
				JOIN mytcg_category CA ON C.category_id = CA.category_id
				JOIN mytcg_user U ON M.user_id = U.user_id
				WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
				ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC;";
		//get user credits
		$userQuery = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, ifnull(premium,0) prem, ifnull(credits,0) cred FROM mytcg_user WHERE user_id = ".$userID);
		$userCredits = $userQuery[0]['premium'];
	}
	else{
		$sql = "SELECT SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*
				FROM mytcg_market M
				JOIN mytcg_usercard UC USING (usercard_id)
				JOIN mytcg_card C USING (card_id)
				JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
				JOIN mytcg_category CA ON C.category_id = CA.category_id
				JOIN mytcg_user U ON M.user_id = U.user_id
				WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
				ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC;";
		$userCredits = '0';
	}
	$aAllCards = myqu($sql);
	$myAuctionsCount = 0;
	$iSizeCards=sizeof($aAllCards);
	echo '<init>'.$sCRLF;
	echo $sTab.'<credits val="'.$userCredits.'" />'.$sCRLF;
	echo $sTab.'<cards>'.$sCRLF;
	echo $sTab.$sTab.'<no_of_cards val="'.$iSizeCards.'" />'.$sCRLF;
	if($iSizeCards > 0)
	{
		$iCount = 0;
		foreach($aAllCards as $card)
		{
			$sql = "SELECT MC.date_of_transaction, (IFNULL(MC.price,0)+IFNULL(MC.premium,0)) price, SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) username, U.name
					FROM ".$pre."_marketcard MC
					JOIN ".$pre."_user U USING (user_id)
					WHERE MC.market_id = ".$card['market_id']."
					ORDER BY  MC.marketcard_id DESC;";
			$aHistory = myqu($sql);
			
			echo $sTab.$sTab.'<card_'.$iCount.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<usercard_id val="'.$card['usercard_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<card_id val="'.$card['card_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<owner val="'.$card['owner'].'" />'.$sCRLF;
			$mine = '0';
			if($card['user_id'] == $userID){
				$mine = '1';
				$myAuctionsCount++;
			}
			echo $sTab.$sTab.$sTab.'<mine val="'.$mine.'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<owned val="'.$card['owned'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<market_id val="'.$card['market_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<category_id val="'.$card['category_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<category val="'.$card['category'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<imageserver val="'.$card['imageserver'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<price val="'.$card['price'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<starting_bid val="'.$card['minimum_bid'].'" />'.$sCRLF;
			$phpdate = strtotime($card['date_expired']);
			echo $sTab.$sTab.$sTab.'<expire val="'.date('D, j M Y H:i:s', $phpdate).'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<date_expired val="'.$card['date_expired'].'" />'.$sCRLF;
			$phpdate = strtotime($card['date_created']);
			echo $sTab.$sTab.$sTab.'<started val="'.date('j M Y H:i', $phpdate).'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<date_created val="'.$card['date_created'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<history>'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<bid_count val="'.sizeof($aHistory).'" />'.$sCRLF;
			if(sizeof($aHistory) > 0)
			{
				$b = 0;
				foreach($aHistory as $bid)
				{
					echo $sTab.$sTab.$sTab.$sTab.'<bid_'.$b.'>'.$sCRLF;
					$bidder = ($bid['user_id'] == $userID) ? 'You' : $bid['username'];
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<user val="'.$bidder.'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<amount val="'.$bid['price'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<date val="'.$bid['date_of_transaction'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.'</bid_'.$b.'>'.$sCRLF;
					if(++$b > 9)
						break;
				}
			}
			echo $sTab.$sTab.$sTab.'</history>'.$sCRLF;
			echo $sTab.$sTab.'</card_'.$iCount.'>'.$sCRLF;
			$iCount++;
		}
	}
	echo $sTab.$sTab.'<no_of_cards_mine val="'.$myAuctionsCount.'" />'.$sCRLF;
	echo $sTab.'</cards>'.$sCRLF;
	echo '</init>'.$sCRLF;
	exit;
}


if(isset($_GET['search']))
{
	$searchstring = $_GET['string'];
	$sql = "SELECT usercard_id FROM
			(
			SELECT M.market_id, M.usercard_id, SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) Username, CA.description, IFNULL(MC.price,0)+IFNULL(MC.premium) AS 'highest_bid', M.minimum_bid, M.price, M.date_expired, M.date_created
			FROM ".$pre."_market M
			JOIN ".$pre."_usercard UC USING (usercard_id)
			JOIN ".$pre."_user U ON M.user_id = U.user_id
			LEFT JOIN ".$pre."_marketcard MC USING (market_id)
			LEFT JOIN ".$pre."_card CA ON UC.card_id = CA.card_id
			WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
			ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC, MC.price DESC
			) tmp
			WHERE
			(
				username LIKE '%".$searchstring."%' OR
				description LIKE '%".$searchstring."%' OR
				highest_bid LIKE '%".$searchstring."%' OR
				minimum_bid LIKE '%".$searchstring."%' OR
				price LIKE '%".$searchstring."%'
			)
			GROUP BY market_id
			ORDER BY date_expired ASC, date_created ASC, market_id ASC";
	$searchResults = myqu($sql);
	$auctions = array();
	if(sizeof($searchResults) > 0){
		foreach($searchResults as $result){
			$auctions[] = $result['usercard_id'];
		}
	}
	if(sizeof($auctions) > 0){
		$auctions = implode(',',$auctions);
	}
	else{
		$auctions = '';
	}
	echo '<search>'.$sCRLF;
	echo $sTab.'<results val="'.$auctions.'" />'.$sCRLF;
	echo '</search>'.$sCRLF;	
	exit;
}


//CATEGORY FILTER
if(intval($_GET["cat"]) > 0){
  $catID = $_GET["cat"];
  
  $sCats = getCardCategories($catID);
  
  $xml = "";
  $query = "SELECT SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) AS 'owner', ISC.description AS 'thumbnail_imageserver', ISB.description AS 'front_imageserver', ISA.description AS 'back_imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
  			(SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$userID." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned' 
      FROM mytcg_market M
      JOIN mytcg_usercard UC USING (usercard_id)
      JOIN mytcg_card C USING (card_id)
	  JOIN mytcg_imageserver ISA ON C.back_imageserver_id = ISA.imageserver_id
	  JOIN mytcg_imageserver ISB ON C.front_imageserver_id = ISB.imageserver_id
	  JOIN mytcg_imageserver ISC ON C.thumbnail_imageserver_id = ISC.imageserver_id
      JOIN mytcg_category CA ON C.category_id = CA.category_id
      JOIN mytcg_user U ON M.user_id = U.user_id
      WHERE M.markettype_id = 1 AND M.marketstatus_id = 1 AND C.category_id IN (".$sCats.")
      ORDER BY M.date_expired ASC, M.date_created ASC, M.market_id ASC;";
  $aAllCards=myqu($query);
  
  $sql = "SELECT ifnull(premium,0)+ifnull(credits,0) as premium FROM ".$pre."_user WHERE user_id=".$userID.";";
  $usr = myqu($sql);
  $usr = $usr[0];
  
  $iSizeCards=sizeof($aAllCards);
  $xml .= '<init>'.$sCRLF;
  $xml .= $sTab.'<credits val="'.$usr['credits'].'" />'.$sCRLF;
  $xml .= $sTab.'<cards>'.$sCRLF;
  $xml .= $sTab.$sTab.'<no_of_cards val="'.$iSizeCards.'" />'.$sCRLF;
  $myAuctionsCount = 0;
  if($iSizeCards > 0)
  {
    $iCount = 0;
    foreach($aAllCards as $card)
    {
      $sql = "SELECT MC.date_of_transaction, (IFNULL(MC.price,0)+IFNULL(MC.premium,0)) price, SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) username, U.name
          FROM ".$pre."_marketcard MC
          JOIN ".$pre."_user U USING (user_id)
          WHERE MC.market_id = ".$card['market_id']."
          ORDER BY MC.marketcard_id DESC;";
      $aHistory = myqu($sql);
      
      $xml .= $sTab.$sTab.'<card_'.$iCount.'>'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<usercard_id val="'.$card['usercard_id'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<card_id val="'.$card['card_id'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<owner val="'.$card['owner'].'" />'.$sCRLF;
	  $mine = '0';
	  if($card['user_id'] == $userID){
		$mine = '1';
		$myAuctionsCount++;
	  }
	  $xml .= $sTab.$sTab.$sTab.'<mine val="'.$mine.'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<owned val="'.$card['owned'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<market_id val="'.$card['market_id'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<category_id val="'.$card['category_id'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<category val="'.$card['category'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<back_imageserver val="'.$card['back_imageserver'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<front_imageserver val="'.$card['front_imageserver'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<thumbnail_imageserver val="'.$card['thumbnail_imageserver'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<price val="'.$card['price'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<starting_bid val="'.$card['minimum_bid'].'" />'.$sCRLF;
      $phpdate = strtotime($card['date_expired']);
      $xml .= $sTab.$sTab.$sTab.'<expire val="'.date('D, j M Y H:i:s', $phpdate).'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<date_expired val="'.$card['date_expired'].'" />'.$sCRLF;
      $phpdate = strtotime($card['date_created']);
      $xml .= $sTab.$sTab.$sTab.'<started val="'.date('D, j M Y H:i', $phpdate).'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<date_created val="'.$card['date_created'].'" />'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.'<history>'.$sCRLF;
      $xml .= $sTab.$sTab.$sTab.$sTab.'<bid_count val="'.sizeof($aHistory).'" />'.$sCRLF;
      if(sizeof($aHistory) > 0)
      {
        $b = 0;
        foreach($aHistory as $bid)
        {
          $xml .=  $sTab.$sTab.$sTab.$sTab.'<bid_'.$b.'>'.$sCRLF;
          $bidder = ($bid['user_id'] == $userID) ? 'You' : $bid['username'];
          $xml .= $sTab.$sTab.$sTab.$sTab.$sTab.'<user val="'.$bidder.'" />'.$sCRLF;
          $xml .= $sTab.$sTab.$sTab.$sTab.$sTab.'<amount val="'.$bid['price'].'" />'.$sCRLF;
          $xml .= $sTab.$sTab.$sTab.$sTab.$sTab.'<date val="'.$bid['date_of_transaction'].'" />'.$sCRLF;
          $xml .= $sTab.$sTab.$sTab.$sTab.'</bid_'.$b.'>'.$sCRLF;
          if(++$b > 9)
            break;
        }
      }
      $xml .= $sTab.$sTab.$sTab.'</history>'.$sCRLF;
      $xml .= $sTab.$sTab.'</card_'.$iCount.'>'.$sCRLF;
      $iCount++;
    }
  }
  $xml .= $sTab.$sTab.'<no_of_cards_mine val="'.$myAuctionsCount.'" />'.$sCRLF;
  $xml .= $sTab.'</cards>'.$sCRLF;
  $xml .= '</init>'.$sCRLF;
  echo $xml;
  exit;
}




if (isset($_GET['buyout']))
{
	$marketID = $_GET['market'];
	$newowner = $userID;
	$now = date('Y-m-d H:i:s');
	
	//Retrieve details of auction
	$sql = "SELECT usercard_id, user_id, price FROM ".$pre."_market WHERE market_id=".$marketID.";";
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
		 $sql = "SELECT C.description FROM ".$pre."_usercard UC JOIN ".$pre."_card C USING(card_id) WHERE UC.usercard_id=".$usercardID;
		  $carName = myqu($sql);
		  $carName = $carName[0]['description'];
		  
		$temp = array();
		
		//give credits back to current highest bidder
		$sql = "SELECT MC.user_id, MC.price, C.description, MC.premium
		  FROM ".$pre."_market M
		  JOIN ".$pre."_marketcard MC USING (market_id)
		  LEFT JOIN ".$pre."_usercard UC ON (M.usercard_id = UC.usercard_id)
		  LEFT JOIN ".$pre."_card C ON (UC.card_id = C.card_id)
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
		$sql = "UPDATE ".$pre."_usercard SET user_id=".$newowner.", usercardstatus_id=1 WHERE usercard_id=".$usercardID.";";
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
		$sql = "UPDATE ".$pre."_market SET marketstatus_id=2 WHERE market_id=".$marketID.";";
		$temp[]=$sql;
		myqu($sql);
		
		//print_r($temp);
		
		//Return user premium
		$sql = "SELECT ifnull(premium,0)+ifnull(credits,0) premium FROM ".$pre."_user WHERE user_id=".$userID.";";
		$u = myqu($sql);
		$iCreditsAfterPurchase = $u[0]['premium'];
		$xml = '';
		$xml.= '<response>'.$sCRLF;
		$xml.= $sTab.'<value>1</value>'.$sCRLF;
		$xml.= $sTab.'<credits>'.$iCreditsAfterPurchase.'</credits>'.$sCRLF;
		$xml.= '</response>'.$sCRLF;
		echo $xml;
		exit;
	}
}



if (isset($_GET['auction']))
{
	$market_id = $_GET['market'];
  
	if (isset($_GET['placebid']))
	{
		$bid_amount = $_GET['placebid'];
		
		//get user's available premium
		$query = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, ifnull(premium,0) prem, ifnull(credits,0) cred FROM mytcg_user WHERE user_id = ".$userID);
		$available_credits = $query[0]['premium'];
		$tcg_credits = $query[0]['cred'];
		$tcg_premium = $query[0]['prem'];

		if($available_credits < $bid_amount)
		{
			//user has insuffient credits to place the bid
			echo '<result>'.$sCRLF;
			echo $sTab.'<value val="0" />'.$sCRLF;
			echo $sTab.'<message val="Insuffient premium to place bid. Only '.$available_credits.' TCG premium available." />'.$sCRLF;
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
	$sql = "SELECT SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.card_id, M.* 
			FROM mytcg_market M
			JOIN mytcg_usercard UC USING (usercard_id)
			JOIN mytcg_card C USING (card_id)
			JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
			JOIN mytcg_category CA ON C.category_id = CA.category_id
			JOIN mytcg_user U ON M.user_id = U.user_id
			WHERE M.market_id = ".$market_id."
			ORDER BY M.date_expired ASC";
	$aDetails = myqu($sql);
	
	echo '<result>'.$sCRLF;
	echo $sTab.'<credits val="'.$user_credits.'" />'.$sCRLF;
	echo $sTab.'<value val="1" />'.$sCRLF;
	echo $sTab.'<details>'.$sCRLF;
	if(sizeof($aDetails) > 0)
	{
		$auction = $aDetails[0];
		
		$sql = "SELECT MC.date_of_transaction, (ifnull(MC.price,0) + ifnull(MC.premium,0)) price, SUBSTRING(IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)),1,8) as username, U.name
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


if (isset($_GET['listen']))
{
  $sCats = "";
  if(intval($_GET["c"]) > 0){
    $sCats = "AND C.category_id IN (".getCardCategories($_GET["c"]).")";
  }
	$sql = "SELECT *, COUNT(*) AS 'bid_count' FROM
			(
				SELECT M.market_id, M.usercard_id, M.minimum_bid, M.date_created, M.date_expired, MC.user_id, ifnull(MC.price,0)+ifnull(MC.premium,0) price,C.category_id
				FROM ".$pre."_market M
				LEFT JOIN ".$pre."_marketcard MC USING(market_id)
		        LEFT JOIN ".$pre."_usercard UC ON (M.usercard_id = UC.usercard_id)
		        LEFT JOIN ".$pre."_card C ON (UC.card_id = C.card_id)
				WHERE M.markettype_id=1 AND M.marketstatus_id=1 ".$sCats." 
				ORDER BY MC.marketcard_id DESC
			) AS openauctions
			GROUP BY (market_id)
			ORDER BY date_expired ASC, date_created ASC, market_id ASC;";
	$aRecords = myqu($sql);
	$aAuctions = array();
	if(count($aRecords) > 0)
	{
		foreach($aRecords as $rec)
		{
			$aDetails = array();
			$aDetails[] = $rec['usercard_id'];
			if(is_null($rec['user_id'])){
				//no bids
				$aDetails[] = '0';
				$aDetails[] = $rec['minimum_bid'];
				$aDetails[] = 'false';
			}
			else{
				//at least 1 bid
				$aDetails[] = $rec['bid_count'];
				$aDetails[] = $rec['price'];
				if($rec['user_id'] == $userID){
					$aDetails[] = 'true';
				}
				else{
					$aDetails[] = 'false';
				}
			}
			$aAuctions[] = implode('|',$aDetails);
		}
	}
	
	//get user's credits
	$query = myqu("SELECT ifnull(premium,0)+ifnull(credits,0) premium FROM ".$pre."_user WHERE user_id = ".$userID);
	$user_credits = $query[0]['premium'];
	
	echo '<listen>'.$sCRLF;
	echo $sTab.'<string val="'.implode('^!%',$aAuctions).'" />'.$sCRLF;
	echo $sTab.'<credits val="'.$user_credits.'" />'.$sCRLF;
	echo '</listen>';
}

?>
