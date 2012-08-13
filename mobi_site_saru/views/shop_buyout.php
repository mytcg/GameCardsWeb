<?php
$iUserID = $user['user_id'];

//GENERATES THE CONTENTS OF A BOOSTER PACK AND GIVES IT TO THE USER
function openBooster($userID,$packID){
	$iReleasedBuffer = 1;
	
	//CARD COUNT OF PACK
	$iPackCount = myqu("SELECT no_of_cards FROM mytcg_product WHERE product_id={$packID}");
	$iPackCount = $iPackCount[0]['no_of_cards'];
	
	$aQuality = myqu("SELECT distinct cq.cardquality_id,((cq.booster_probability)*{$iPackCount}) AS bp 
		FROM mytcg_cardquality cq 
		INNER JOIN mytcg_card c 
		ON c.cardquality_id = cq.cardquality_id 
		INNER JOIN mytcg_productcard pc 
		ON pc.card_id = c.card_id 
		WHERE pc.product_id = {$packID} 
		ORDER BY booster_probability ASC");
	$iQualityID = 0;
	$cards = array();
	
	//GET CARDS
	for ($i = 0; $i < $iPackCount; $i++){
		//GET A RANDOM QUALITY CARD
		$iQualityID = randomQualityID($aQuality,$iPackCount);
	
		//GET STACK OF SAME QUALITY CARDS
		$aGetCards = myqu(" SELECT c.card_id, c.cardquality_id, cq.booster_probability
			FROM mytcg_card c
			INNER JOIN  mytcg_productcard pc
			ON pc.card_id = c.card_id
			INNER JOIN mytcg_cardquality AS cq
			ON cq.cardquality_id = c.cardquality_id
			WHERE pc.product_id={$packID}
			AND c.cardquality_id={$iQualityID}");
		$iNumCards = sizeof($aGetCards);
		
		//PICK A RANDOM CARD FROM THE STACK
		$iRandom=rand(0,$iNumCards-1);
		$iCardID=$aGetCards[$iRandom]['card_id'];
					
		//GIVE THE CARD TO THE USER
		myqu('UPDATE mytcg_usercard set loaded = 1 where card_id = '.$iCardID.' and user_id = '.$userID);
		$aCards=myqu("INSERT INTO mytcg_usercard (user_id, card_id, usercardstatus_id)
			SELECT {$userID}, {$iCardID}, usercardstatus_id
			FROM mytcg_usercardstatus
			WHERE description = 'Album'");
		
		$card;
		if ($cards[$iCardID] == null) {
			$card = array();
			$card['cardId'] = $iCardID;
			$card['quantity'] = 1;
		}
		else {
			$card = $cards[$iCardID];
			$card['quantity'] = $card['quantity']+1;
		}
		$cards[$iCardID] = $card;
	}
	
	//we can remove one of the products from stock though
	myqu("UPDATE mytcg_product SET in_stock=in_stock-1 WHERE product_id={$packID}");
	
	return $cards;
}
//ROLL DICE AND CHECK WHAT QUALITY CARD THE USER RECEIVES 
function randomQualityID($aQuality,$iPackCount){
  $iRandom = rand(1, $aQuality[sizeof($aQuality) - 1]['bp']);//rand(1,$iPackCount);
  $interval=0;
  for($l=0; $l < sizeof($aQuality); $l++){
      $interval += $aQuality[$l]['bp'];
        if ($iRandom <= $interval){
          $iQualityID = $aQuality[$l]['cardquality_id'];
          break;
    }
  }
  return $iQualityID;
}


if($_GET['free'] == 2){
  $packID = $_SESSION['booster'];
  $userID = $_SESSION['userID'];
  
  
  if ($packID == 2) {
	$iFree=myqu("SELECT freebie FROM mytcg_user WHERE user_id=".$userID);
	$iFree=$iFree[0]['freebie'];
	if ($iFree == 1) {
		echo("Unsuccesful Freebie given <a href");
		unset ($_SESSION['booster']);
		unset ($_SESSION['userID']);
		session_destroy($_SESSION['booster']);
		session_destroy($_SESSION['userID']);
		exit;
	} else {
		myqu("UPDATE mytcg_user SET freebie = 1 WHERE user_id=".$userID);
	}
  }
  
  //GET PRODUCT DETAILS
  $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, premium as price, P.no_of_cards '
    .'FROM mytcg_product P '
    .'INNER JOIN mytcg_producttype PT '
    .'ON P.producttype_id = PT.producttype_id '
    .'WHERE P.product_id='.$packID);
  $iProductID = $aDetails[0]['product_id'];

  //VALIDATE USER CREDITS
  //User credits
  $iCredits=myqu("SELECT premium FROM mytcg_user WHERE user_id=".$userID);
  $iCredits=$iCredits[0]['premium'];
  
  //Total order cost
  $itemCost = $aDetails[0]['price'];
  $bValid = ($iCredits >= $itemCost);

	//RECEIVE ITEM
	$cards;
	if ($aDetails[0]['ptype'] == "Starter"){
	  $cards = openStarter($userID,$iProductID);
	}
	elseif($aDetails[0]['ptype'] == "Booster"){
	  $cards = openBooster($userID,$iProductID);
	}

	if(sizeof($cards) > 0){
	  //PAY FOR ORDER ITEM * QUANTITY ORDERED   
	  //$iCreditsAfterPurchase = $iCredits - $itemCost;
	  //$aCreditsLeft=myqu("UPDATE mytcg_user SET premium={$iCreditsAfterPurchase} WHERE user_id=".$userID);
	  //$_SESSION["user"]["premium"] = $iCreditsAfterPurchase;
	  
	  if ($packID == 2) {
		myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
			VALUES(".$userID.",'Received free 3 card booster for registering.', NOW(), 0)");
			
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
			VALUES(".$userID.", ".$packID.", NULL, NULL, 
				now(), 'Received free 3 card booster for registering.', 0, NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 12)");

	 } 
	  ?>
	  <div>Here something to get you started...</div>
        <?php
        foreach($cards as $card){
          $query='SELECT C.card_id, C.image, C.description,I.description AS path '
                .'FROM mytcg_card C '
                .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
                .'WHERE C.card_id = '.$card['cardId'];
          $aCard=myqu($query);
		  ?>
		  <div class="album_card_pic">
		  	<a href="index.php?page=card_display_front&card_id=<?php echo($aCard[0]['card_id']); ?>">
				<img src="<?php echo($aCard[0]['path']); ?>cards/jpeg/<?php echo($aCard[0]['image']); ?>_web.jpg" width="64" height="90" title="View potential cards">
	      	</a>
	      	<div style="width:64px"><?php echo($aCard[0]['description']); ?></div>
	      </div>
		  <?php
          $iCount++;
        }
	}else{
      echo("Your gift was not delivered...");
	}
  exit;
}
if (isset($_SESSION['booster'])){
	$boosterid = $_SESSION['booster'];

    //GET PRODUCT DETAIL
    $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, P.premium, P.price cred, P.no_of_cards 
      FROM mytcg_product P 
      INNER JOIN mytcg_producttype PT 
      ON P.producttype_id = PT.producttype_id 
      WHERE P.product_id='.$boosterid);
    $iProductID = $aDetails[0]['product_id'];

    //VALIDATE USER CREDITS
    //User credits
    $iCredits=myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, credits cred, premium prem FROM mytcg_user WHERE user_id=".$iUserID);
    $iCred=$iCredits[0]['premium'];
    
    //Total order cost
    $itemCost = $aDetails[0]['premium'];
    $bValid = ($iCred >= $itemCost);
    
    if ($bValid)
    {
      //RECEIVE ITEM
      
      $cards;
      if($aDetails[0]['ptype'] == "Booster"){
        $cards = openBooster($iUserID,$iProductID);
	  }
	  
      $iCreditsAfterPurchase=0;
      if(sizeof($cards) > 0){
        //PAY FOR ORDER ITEM * QUANTITY ORDERED   
		  $freemium = $iCredits[0]['cred'];
		  $premium = $iCredits[0]['prem'];
		  
		  if($freemium > $itemCost){
			$iCreditsAfterPurchase = $freemium - $itemCost;
			$aCreditsLeft=myqu("UPDATE mytcg_user SET credits={$iCreditsAfterPurchase} WHERE user_id=".$iUserID);
			$premiumCost = 0;
			$freemiumCost = $itemCost*-1;
			
			$_SESSION["user"]["credits"] = $iCreditsAfterPurchase;
		  }else{
			$iCreditsAfterPurchase = $premium - ($itemCost-$freemium);
			$aCreditsLeft=myqu("UPDATE mytcg_user SET credits=0,premium={$iCreditsAfterPurchase} WHERE user_id=".$iUserID);
			$premiumCost = ($itemCost-$freemium)*-1;
			$freemiumCost = $freemium*-1;
			
			$_SESSION["user"]["credits"] = 0;
			$_SESSION["user"]["premium"] = $iCreditsAfterPurchase;
		  }
        
        myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
        VALUES(".$iUserID.",'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', NOW(), ".(-1*$itemCost).")");
		
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
		VALUES(".$iUserID.", ".$iProductID.", NULL, NULL, 
				now(), 'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', -".$itemCost.", ".$freemiumCost.", ".$premiumCost.", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$iUserID."), 10)");
        
        $iCount = 0; ?>
        <div>These are your new cards</div>
        <?php
        foreach($cards as $card){
          $query='SELECT C.card_id, C.image, C.description,I.description AS path '
                .'FROM mytcg_card C '
                .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
                .'WHERE C.card_id = '.$card['cardId'];
          $aCard=myqu($query);
		  ?>
		  <div class="album_card_pic">
		  	<a href="index.php?page=card_display_front&card_id=<?php echo($aCard[0]['card_id']); ?>">
				<img src="<?php echo($aCard[0]['path']); ?>cards/jpeg/<?php echo($aCard[0]['image']); ?>_web.jpg" width="64" height="90" title="View potential cards">
	      	</a>
	      	<div style="width:64px"><?php echo($aCard[0]['description']); ?></div>
	      </div>
		  <?php
          $iCount++;
        }
      }
    }
    else{
      echo("Your purchase was unsuccesful...");
    }
	unset ($_SESSION['booster']);
    exit;
    }
    else
    {
      echo("Your purchase was unsuccesful...");
	}
?>