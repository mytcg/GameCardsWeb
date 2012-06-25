<?php
require_once "../../func.php";

$sCRLF="\r\n";
$sTab=chr(9);

$pre = $Conf["database"]["table_prefix"];

$userID = $_SESSION["user"]["id"];


if (intval($_GET['init']==1))
{
	if(strlen($userID) > 0){
		$sql = "SELECT SUBSTRING_INDEX(U.username, '@', 1) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
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
		$userQuery = myqu("SELECT premium FROM ".$pre."_user WHERE user_id=".$userID);
		$userCredits = $userQuery[0]['premium'];
	}
	else{
		$sql = "SELECT SUBSTRING_INDEX(U.username, '@', 1) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*
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
			$sql = "SELECT MC.*, U.username, U.name
					FROM ".$pre."_marketcard MC
					JOIN ".$pre."_user U USING (user_id)
					WHERE MC.market_id = ".$card['market_id']."
					ORDER BY MC.price DESC;";
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
			SELECT M.market_id, M.usercard_id, U.username, CA.description, MC.price AS 'highest_bid', M.minimum_bid, M.price, M.date_expired, M.date_created
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
  $query = "SELECT SUBSTRING_INDEX(U.username, '@', 1) AS 'owner', ISC.description AS 'thumbnail_imageserver', ISB.description AS 'front_imageserver', ISA.description AS 'back_imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*,
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
  
  $sql = "SELECT premium FROM ".$pre."_user WHERE user_id=".$userID.";";
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
      $sql = "SELECT MC.*, U.username, U.name
          FROM ".$pre."_marketcard MC
          JOIN ".$pre."_user U USING (user_id)
          WHERE MC.market_id = ".$card['market_id']."
          ORDER BY MC.price DESC;";
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
	$price = $_GET['price'];
	$newowner = $userID;
	$now = date('Y-m-d H:i:s');
	
	//Retrieve details of auction
	$sql = "SELECT usercard_id, user_id FROM ".$pre."_market WHERE market_id=".$marketID.";";
	$uc = myqu($sql);
	$usercardID = $uc[0]['usercard_id'];
	$oldowner = $uc[0]['user_id'];
	
  $sql = "SELECT C.description FROM ".$pre."_usercard UC JOIN ".$pre."_card C USING(card_id) WHERE UC.usercard_id=".$usercardID;
  $carName = myqu($sql);
  $carName = $carName[0]['description'];
  
	$temp = array();
	
	//give credits back to current highest bidder
	$sql = "SELECT MC.user_id, MC.price, C.description
      FROM ".$pre."_market M
      JOIN ".$pre."_marketcard MC USING (market_id)
      LEFT JOIN ".$pre."_usercard UC ON (M.usercard_id = UC.usercard_id)
      LEFT JOIN ".$pre."_card C ON (UC.card_id = C.card_id)
      WHERE M.market_id = ".$marketID."
      ORDER BY MC.price DESC
      LIMIT 1;";
	$lastBidderQuery = myqu($sql);
  
	if(sizeof($lastBidderQuery) > 0){
		foreach($lastBidderQuery as $lastBidder){
			myqu("UPDATE mytcg_user SET premium = premium+".$lastBidder['price']." WHERE user_id = ".$lastBidder['user_id']);
			//add transaction log
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$lastBidder['user_id'].", 'Refunded with ".$lastBidder['price']." premium for bid on bought out ".$carName."', NOW(), ".$lastBidder['price'].")");
		}
	}
	
	//Change ownership of card
	$sql = "UPDATE ".$pre."_usercard SET user_id=".$newowner.", usercardstatus_id=1 WHERE usercard_id=".$usercardID.";";
	$temp[]=$sql;
	myqu($sql);
	
	//Record transaction
	$sql = "INSERT INTO ".$pre."_marketcard (market_id, usercard_id, price, date_of_transaction, user_id) ".
			"VALUES(".$marketID.",".$usercardID.",".$price.",'".$now."',".$newowner.");";
	$temp[]=$sql;
	myqu($sql);
	
	//Subtract premium from buyer
	$sql = "UPDATE ".$pre."_user SET premium=(premium-".$price.") WHERE user_id=".$newowner.";";
	$temp[]=$sql;
	myqu($sql);
	//Add transaction log
	myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
			VALUES(".$newowner.", 'Spent ".$price." premium on buyout of ".$carName."', NOW(), -".$price.")");
	
	//Add premium to seller
	$sql = "UPDATE ".$pre."_user SET premium=(premium+".$price.") WHERE user_id=".$oldowner.";";
	$temp[]=$sql;
	myqu($sql);
	//Add transaction log
	myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
			VALUES(".$oldowner.", 'Received ".$price." premium for buyout on ".$carName."', NOW(), ".$price.")");
	
	//Close auction
	$sql = "UPDATE ".$pre."_market SET marketstatus_id=2 WHERE market_id=".$marketID.";";
	$temp[]=$sql;
	myqu($sql);
	
	//print_r($temp);
	
	//Return user premium
	$sql = "SELECT premium FROM ".$pre."_user WHERE user_id=".$userID.";";
	$u = myqu($sql);
	$iCreditsAfterPurchase = $u[0]['premium'];
	$xml = '';
	$xml.= '<response>'.$sCRLF;
	$xml.= $sTab.'<value>1</value>'.$sCRLF;
	$xml.= $sTab.'<credits>'.$iCreditsAfterPurchase.'</credits>'.$sCRLF;
	$xml.= '</response>'.$sCRLF;
	echo $xml;
}



if (isset($_GET['auction']))
{
	$market_id = $_GET['market'];
  
	if (isset($_GET['placebid']))
	{
		$bid_amount = $_GET['placebid'];
		
		//get user's available premium
		$query = myqu("SELECT premium FROM ".$pre."_user WHERE user_id = ".$userID);
		$available_credits = $query[0]['premium'];
		
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
		  $sql = "SELECT C.description
      FROM ".$pre."_market M
      LEFT JOIN ".$pre."_usercard UC ON (M.usercard_id = UC.usercard_id)
      LEFT JOIN ".$pre."_card C ON (UC.card_id = C.card_id)
      WHERE M.market_id = ".$market_id."
      LIMIT 1";
      $carName = myqu($sql);
      $carName = $carName[0]['description'];
    
			//give premium back to current highest bidder
			$sql = "SELECT MC.user_id, MC.price
					FROM ".$pre."_market M
					JOIN ".$pre."_marketcard MC USING (market_id)
					WHERE M.market_id = ".$market_id."
					ORDER BY MC.price DESC
					LIMIT 1;";
			$lastBidderQuery = myqu($sql);
			if(sizeof($lastBidderQuery) > 0){
				foreach($lastBidderQuery as $lastBidder){
					myqu("UPDATE mytcg_user SET premium = premium+".$lastBidder['price']." WHERE user_id = ".$lastBidder['user_id']);
					//add transaction log
					myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
							VALUES(".$lastBidder['user_id'].", 'Refunded with ".$lastBidder['price']." premium for losing highest bid on ".$carName."', NOW(), ".$lastBidder['price'].")");
				}
			}
			
			//subtract premium from this user
			myqu("UPDATE mytcg_user SET premium = premium-".$bid_amount." WHERE user_id = ".$userID);
			//add transaction log
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Placed bid of ".$bid_amount." premium on ".$carName."', NOW(), -".$bid_amount.")");
			
			//place the bid
			$sql = "INSERT INTO ".$pre."_marketcard (market_id, user_id, price, date_of_transaction)
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
	$query = myqu("SELECT premium FROM ".$pre."_user WHERE user_id = ".$userID);
	$user_credits = $query[0]['premium'];
	
	// return auction data - xml
	$sql = "SELECT SUBSTRING_INDEX(U.username, '@', 1) AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.card_id, M.* 
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
		
		$sql = "SELECT MC.*, U.username, U.name
				FROM ".$pre."_marketcard MC
				JOIN ".$pre."_user U USING (user_id)
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
				SELECT M.market_id, M.usercard_id, M.minimum_bid, M.date_created, M.date_expired, MC.user_id, MC.price,C.category_id
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
	$query = myqu("SELECT premium FROM ".$pre."_user WHERE user_id = ".$userID);
	$user_credits = $query[0]['premium'];
	
	echo '<listen>'.$sCRLF;
	echo $sTab.'<string val="'.implode('^!%',$aAuctions).'" />'.$sCRLF;
	echo $sTab.'<credits val="'.$user_credits.'" />'.$sCRLF;
	echo '</listen>';
}

?>
