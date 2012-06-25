<?php
$command_line = true;

$sCRLF = "\r\n";
$sTab = chr(9);
$sSpc = "* ";
$item = "card";
$Item = "Card";
$now = date('Y-m-d H:i:s');

$textResponse = "";

//Start Processing...

$textResponse .= "=========================================================".$sCRLF;
$textResponse .= "START OF PROCESS: AUCTIONS".$sCRLF;
$textResponse .= "=========================================================".$sCRLF;

$textResponse .= "Processing Auctions...".$now.$sCRLF;

//Fetch all active auctions
$sql = "SELECT 
			M.*, 
			(SELECT COUNT(marketcard_id) FROM mytcg_marketcard WHERE market_id=M.market_id) AS 'nob',
			C.description AS 'card',
			U0.username AS 'seller'
		FROM mytcg_market M
		JOIN mytcg_usercard UC USING(usercard_id)
		JOIN mytcg_card C USING(card_id)
		JOIN mytcg_user U0 ON UC.user_id = U0.user_id
		WHERE M.markettype_id=1 
		AND M.marketstatus_id=1 
		ORDER BY M.date_expired ASC;";
$auctions = myqu($sql);
$activeAuctionsCount = sizeof($auctions);

//Display active auctions count
$textResponse .= "Found ".$activeAuctionsCount." active auction(s)".$sCRLF.$sCRLF;

if($activeAuctionsCount > 0)
{
	//Process each active/open auction
	foreach($auctions as $auction)
	{
		$textResponse .= "AUCTION ".$auction['market_id']." ends ".substr($auction['date_expired'],0,16)."...";
		if($auction['date_expired'] > $now){
			$textResponse .= "open".$sCRLF;
		}
		else{
			//Auction must be closed
			$textResponse .= "ended".$sCRLF;
			$textResponse .= $sSpc.$Item." Auctioned: ".$auction['usercard_id']." (".$auction['card'].") by ".$auction['seller'].$sCRLF;
			$textResponse .= $sSpc."Winning Bid: ";
			
			if($auction['nob'] > 0)
			{
				//Get winning bid info
				$sql = "SELECT MC.user_id, (ifnull(MC.price,0)+ifnull(MC.premium,0)) AS 'price', ifnull(MC.price,0) credits, ifnull(MC.premium,0) premium, IFNULL(U.name, SUBSTRING_INDEX(U.username, '@', 1)) AS 'winner'
						FROM mytcg_marketcard MC
						JOIN mytcg_user U USING(user_id)
						WHERE market_id=".$auction['market_id']."
						ORDER BY price DESC LIMIT 1;";
				$bids = myqu($sql);
				$winningbid = $bids[0];
				
				//Winning bid
				$textResponse .= $winningbid['price']." by ".$winningbid['winner'];
				
				$freemium_cost = $winningbid['credits'];
				$premium_cost = $winningbid['premium'];
				
				$freemium_fee = $premium_cost * 0.1;
				$premium_fee = $freemium_cost * 0.1;
				
				$freemium_cost = $freemium_cost - $freemium_fee;
				$premium_cost = $premium_cost - $premium_fee;
				
				$total = $freemium_cost + $premium_cost;
				
				//Give credits to seller
				$sql = "UPDATE mytcg_user SET premium=(premium+".$premium_cost."), credits=(credits+".$freemium_cost.") WHERE user_id=".$auction['user_id'].";";
				myqu($sql);
				//add transaction log
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$auction['user_id'].", 'Received ".$total." credits for auction ".$auction['market_id']."', NOW(), ".$winningbid['price'].")");
				
				//Change ownership of card
				$sql = "UPDATE mytcg_usercard SET user_id=".$winningbid['user_id'].", usercardstatus_id=1 WHERE usercard_id=".$auction['usercard_id'].";";
				myqu($sql);
				
				$textResponse .= "...".$Item." Sold!".$sCRLF;
			}
			else
			{
				//No bids - remove card from aution
				$sql = "UPDATE mytcg_usercard SET usercardstatus_id=1 WHERE usercard_id=".$auction['usercard_id'];
				myqu($sql);
				
				$textResponse .= "No Bids...Card Removed From Auction!".$sCRLF;
			}
			
			//Close auction
			$sql = "UPDATE mytcg_market SET marketstatus_id=2 WHERE market_id=".$auction['market_id'].";";
			myqu($sql);
			
			$textResponse .= $sSpc."AUCTION ".$auction['market_id']." closed...OK".$sCRLF;
		}
		echo $sCRLF;
	}
}
else
{
	$textResponse .= "* NO ACTIVE AUCTIONS".$sCRLF.$sCRLF;
}
	
$textResponse .= "=========================================================";
$textResponse .= $sCRLF."END OF PROCESS: AUCTIONS".$sCRLF;
$textResponse .= "=========================================================";

if($localhost){
	echo $textResponse;
}

$_SESSION['auctions'] = "$textResponse";
?>