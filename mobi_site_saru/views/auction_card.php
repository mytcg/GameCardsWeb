<?php
$iUserID =$user['user_id'];
if (isset($_GET['create_auction'])){
		
	$iCardId=$_GET['create_auction'];
	$iAuctionBid=$_POST['bid'];
	$iBuyNowPrice=$_POST['buynow'];
	$iDays=$_POST['days'];
	
	
	if (!($iAuctionType=$_GET['auctiontype'])) {
	
		$aCardType=myqu(
			'select pc.card_id, sum(p.price) as freemium, sum(p.premium) as premium 
			from mytcg_product p 
			inner join mytcg_productcard pc 
			on pc.product_id = p.product_id 
			where pc.card_id = "'.$iCardId.'" 
			group by pc.card_id'
		);
		
		if (sizeof($aCardType) > 0) {
			if ($aCardType[0]['freemium'] > 0 || $aCardType[0]['premium'] == 0) {
				$iAuctionType = '1';
			}
			else if ($aCardType[0]['premium'] > 0) {
				$iAuctionType = '2';
			}
		}
	
		//$iAuctionType = '1';
	}
	
	$aCheckCard=myqu('SELECT max(usercard_id) usercard_id '
					.'FROM mytcg_usercard '
					.'WHERE usercardstatus_id = (select usercardstatus_id from mytcg_usercardstatus where description = "Album")  '
					.'AND card_id = '.$iCardId.' '
					.'AND user_id = "'.$iUserID.'"');

	if (($aCheckCard[0]['usercard_id']) ==  null){
		echo 'Apologies, but you do not own this card.';  
		exit;
	} else {
		$iUserCardID = $aCheckCard[0]['usercard_id'];
	}
	
	$aCheckCredits=myqu('SELECT IFNULL(credits,0)+IFNULL(premium,0) credits, IFNULL(credits,0) free, IFNULL(premium,0) premium from mytcg_user
						WHERE user_id = '.$iUserID);
	
	$cost = $iAuctionBid;
	if ($iAuctionBid < $iBuyNowPrice) {
		$cost = $iBuyNowPrice;
	}

	myqu('UPDATE mytcg_usercard set loaded = 1 where usercard_id = '.$iUserCardID);
	$aUpdate=myqu('UPDATE mytcg_usercard SET usercardstatus_id=(select usercardstatus_id from mytcg_usercardstatus where description = "auction") '
					.'WHERE usercard_id="'.$iUserCardID.'"');
	$aInsert=myqu('INSERT INTO mytcg_market '
					.'(markettype_id, marketstatus_id, user_id, usercard_id, '
					.'date_created, date_expired, price, minimum_bid, auctiontype_id) '
					.'VALUES (1, 1, "'.$iUserID.'", "'.$iUserCardID.'", now(), "'.date('Y-m-d H:i:s',time()+$iDays*24*60*60).'", "'.$iBuyNowPrice.'", '
					.'"'.$iAuctionBid.'", "'.$iAuctionType.'")');

	echo "Your Auction has been created succesfully...";
	exit;
	}

if (isset($_GET['auction_card'])){
	
	$iCardId=$_GET['auction_card'];
	$query = 	'SELECT C.card_id, I.description AS path, C.category_id, CQ.description AS cardr, C.image, C.description, C.ranking  
				FROM mytcg_card AS C 
				INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) 
				INNER JOIN mytcg_cardquality CQ ON (C.cardquality_id = CQ.cardquality_id) 
				WHERE C.card_id =  '.$iCardId.' ';
	$aCards=myqu($query);
	$iCount = 0;
	?>
	<ul id="auction_card">
			<li><a>
			<div class="cardBlockBid">
				<div class="album_card_pic">
					<img src="<?php echo($aCards[$iCount]['path']); ?>/cards/jpeg/<?php echo($aCards[$iCount]['image']); ?>_web.jpg" title="" >
				</div>
				<div class="album-card-pic-container" style="background-image:url('<?php echo ($aCards[$iCount]['path']); ?>cards/jpeg/thumb.jpg')"></div>
				<div class="album_card_title">
						<?php echo $aCards[$iCount]['description']; ?>
						<br /><?php echo $aCards[$iCount]['cardr']; ?><br /><br />
	    		  	<form method="POST" id="submitForm" action="index.php?page=auction_card&create_auction=<?php echo($aCards[$iCount]['card_id']); ?>">
			            <div class="profile_form">
			            	<div>Opening bid:</div>
			            	<input type="text" name="bid" value="30" size="35" maxlength="50" class="textbox" />
			            	<div>Buy now price:</div>
			            	<input type="text" name="buynow" value="60" size="35" maxlength="50" class="textbox" />
			            	<div>Auction duration(days):</div>
			            	<input type="text" name="days" value="5" size="35" maxlength="50" class="textbox" />
			           	</div>
						<input type="submit" name="auction" value="Auction" class="button" />
			    	</form>
				</div>
			</div>
			</a></li>
    	</ul>
		<?php
	
}
	
	
?>   		
