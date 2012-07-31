<?php
$command_line = true;
require('func.php');

$pre = $Conf["database"]["table_prefix"];
$sCRLF = "\r\n";
$sTab = chr(9);
$sSpc = "* ";
$item = "card";
$Item = "Card";
$now = date('Y-m-d H:i:s');

//Start Processing...

echo "=========================================================".$sCRLF;
echo "START OF PROCESS: AUCTIONS".$sCRLF;
echo "=========================================================".$sCRLF;

echo "Processing Auctions...".$now.$sCRLF;

//Fetch all active auctions
$sql = "SELECT 
			M.*, 
			(SELECT COUNT(marketcard_id) FROM ".$pre."_marketcard WHERE market_id=M.market_id) AS 'nob',
			C.description AS 'card',
			U0.username AS 'seller'
		FROM ".$pre."_market M
		JOIN ".$pre."_usercard UC USING(usercard_id)
		JOIN ".$pre."_card C USING(card_id)
		JOIN ".$pre."_user U0 ON UC.user_id = U0.user_id
		WHERE M.markettype_id=1 
		AND M.marketstatus_id=1 
		ORDER BY M.date_expired ASC;";
$auctions = myqu($sql);
$activeAuctionsCount = sizeof($auctions);

//Display active auctions count
echo "Found ".$activeAuctionsCount." active auction(s)".$sCRLF.$sCRLF;

if($activeAuctionsCount > 0)
{
	//Process each active/open auction
	foreach($auctions as $auction)
	{
		echo "AUCTION ".$auction['market_id']." ends ".substr($auction['date_expired'],0,16)."...";
		if($auction['date_expired'] > $now){
			echo "open".$sCRLF;
		}
		else{
			//Auction must be closed
			echo "ended".$sCRLF;
			echo $sSpc.$Item." Auctioned: ".$auction['usercard_id']." (".$auction['card'].") by ".$auction['seller'].$sCRLF;
			echo $sSpc."Winning Bid: ";
			
			if($auction['nob'] > 0)
			{
				//Get winning bid info
				$sql = "SELECT MC.user_id, MC.price AS 'price', U.username AS 'winner'
						FROM ".$pre."_marketcard MC
						JOIN ".$pre."_user U USING(user_id)
						WHERE market_id=".$auction['market_id']."
						ORDER BY price DESC LIMIT 1;";
				$bids = myqu($sql);
				$winningbid = $bids[0];
				
				//Winning bid
				echo $winningbid['price']." by ".$winningbid['winner'];
				
				//Give credits to seller
				$sql = "UPDATE ".$pre."_user SET credits=(credits+".$winningbid['price'].") WHERE user_id=".$auction['user_id'].";";
				myqu($sql);
				//add transaction log
				myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$auction['user_id'].", 'Received ".$winningbid['price']." credits for auction ".$auction['market_id']."', NOW(), ".$winningbid['price'].")");
				
				//Change ownership of card
				$sql = "UPDATE ".$pre."_usercard SET user_id=".$winningbid['user_id'].", usercardstatus_id=1 WHERE usercard_id=".$auction['usercard_id'].";";
				myqu($sql);
				
				echo "...".$Item." Sold!".$sCRLF;
			}
			else
			{
				//No bids - remove card from aution
				$sql = "UPDATE ".$pre."_usercard SET usercardstatus_id=1 WHERE usercard_id=".$auction['usercard_id'];
				myqu($sql);
				
				echo "No Bids...Card Removed From Auction!".$sCRLF;
			}
			
			//Close auction
			$sql = "UPDATE ".$pre."_market SET marketstatus_id=2 WHERE market_id=".$auction['market_id'].";";
			myqu($sql);
			
			echo $sSpc."AUCTION ".$auction['market_id']." closed...OK".$sCRLF;
		}
		echo $sCRLF;
	}
}
else
{
	echo "* NO ACTIVE AUCTIONS".$sCRLF.$sCRLF;
}




	
echo "=========================================================";
echo $sCRLF."END OF PROCESS: AUCTIONS".$sCRLF;
echo "=========================================================";
?>