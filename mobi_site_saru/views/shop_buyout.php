<?php
$iUserID = $user['user_id'];

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
<div><a href="index.php?page=home"><div class="cmdButton" style="margin-top:5px;padding-top:8px;height:17px;">Back</div></a></div>
