<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

if($_GET['boosterpack']){
  $packID = $_GET['boosterpack'];
  $sCards = boosterCards($_SESSION['userDetails']['user_id'],$packID);
  echo($sCards);
  exit;
}

if($_GET['buy']){
  $xml = "";
  $packID = $_GET['buy'];
  $userID = $_SESSION['userDetails']['user_id'];
  //GET PRODUCT DETAILS
  $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, premium as price, P.no_of_cards '
    .'FROM mytcg_product P '
    .'INNER JOIN mytcg_producttype PT '
    .'ON P.producttype_id = PT.producttype_id '
    .'WHERE P.product_id='.$packID);
  $iProductID = $aDetails[0]['product_id'];

  //VALIDATE USER CREDITS
  //User credits
  $iCredits=myqu("SELECT credits,premium FROM mytcg_user WHERE user_id=".$userID);
  $iTotalCredits=$iCredits[0]['premium']+$iCredits[0]['credits'];
  
  //Total order cost
  $itemCost = $aDetails[0]['price'];
  
  //Check total credits
  $bValid = ($iTotalCredits >= $itemCost);
  if ($bValid)
  {
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
      $freemium = $iCredits[0]['credits'];
	  $premium = $iCredits[0]['premium'];
	  
	  if($freemium > $itemCost){
	  	$iCreditsAfterPurchase = $freemium - $itemCost;
      	$aCreditsLeft=myqu("UPDATE mytcg_user SET credits={$iCreditsAfterPurchase} WHERE user_id=".$userID);
		$premiumCost = 0;
		$freemiumCost = $itemCost*-1;
	  }else{
	  	$iCreditsAfterPurchase = $premium - ($itemCost-$freemium);
      	$aCreditsLeft=myqu("UPDATE mytcg_user SET credits=0,premium={$iCreditsAfterPurchase} WHERE user_id=".$userID);
		$premiumCost = ($premium - ($itemCost-$freemium))*-1;
		$freemiumCost = $freemium*-1;
	  }
	  
      myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
        VALUES(".$userID.",'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', NOW(), ".(-1*$itemCost).")");
		
	  myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type,tcg_freemium,tcg_premium)
		VALUES(".$userID.", ".$iProductID.", NULL, NULL, 
				now(), 'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', -".$itemCost.", NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 10,{$freemiumCost},{$premiumCost})");

      $xml .=  '<response>'.$sCRLF;
      $xml .=  $sTab.'<value>1</value>'.$sCRLF;
      $xml .=  $sTab.'<credits>'.$iCreditsAfterPurchase.'</credits>'.$sCRLF;
      $xml .=  $sTab.'<count>'.sizeof($cards).'</count>'.$sCRLF;
	  $xml .=  $sTab.'<cost>'.$itemCost.'</cost>'.$sCRLF;
      $xml .=  $sTab.'<cards>'.$sCRLF;
      $iCount = 0;
      foreach($cards as $card){
        $query='SELECT C.card_id, C.image, C.description,I.description AS path '
              .'FROM mytcg_card C '
              .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
              .'WHERE C.card_id = '.$card['cardId'];
        $aCard=myqu($query);
        $xml .= $sTab.$sTab.'<card_'.$iCount.'>'.$sCRLF;
        $xml .= $sTab.$sTab.$sTab.'<cardid>'.$card['cardId'].'</cardid>'.$sCRLF;
        $xml .= $sTab.$sTab.$sTab.'<description>'.$aCard[0]['description'].'</description>'.$sCRLF;
        $xml .= $sTab.$sTab.$sTab.'<qty>'.$card['quantity'].'</qty>'.$sCRLF;
        $xml .= $sTab.$sTab.$sTab.'<path>'.$aCard[0]['path'].'</path>'.$sCRLF;
        $xml .= $sTab.$sTab.$sTab.'<img>'.$aCard[0]['image'].'</img>'.$sCRLF;
        $xml .= $sTab.$sTab.'</card_'.$iCount.'>'.$sCRLF;
        $iCount++;
      }
      $xml .=  $sTab.'</cards>'.$sCRLF;
      $xml .=  '</response>'.$sCRLF;
    }else{
      $xml .=  '<response>'.$sCRLF;
      $xml .=  $sTab.'<value>0</value>'.$sCRLF;
      $xml .=  '</response>'.$sCRLF;
    }
    echo($xml);
  }
  else{
    $xml .=  '<response>'.$sCRLF;
    $xml .=  $sTab.'<value>-1</value>'.$sCRLF;
    $xml .=  '</response>'.$sCRLF;
    echo($xml);
  }
  exit;
}
if($_GET['free']){
  $xml = "";
  $packID = $_GET['free'];
  $userID = $_SESSION['userDetails']['user_id'];
  
  
  if ($packID == 2) {
	$iFree=myqu("SELECT freebie FROM mytcg_user WHERE user_id=".$userID);
	$iFree=$iFree[0]['freebie'];
	if ($iFree == 1) {
		$xml .=  '<response>'.$sCRLF;
		$xml .=  $sTab.'<value>-1</value>'.$sCRLF;
		$xml .=  '</response>'.$sCRLF;
		echo($xml);
		exit;
	} else {
		myqu("UPDATE mytcg_user SET freebie = 1 WHERE user_id=".$userID);
	}
  }else if ($packID == 1) {
	$iFree=myqu("SELECT profile FROM mytcg_user WHERE user_id=".$userID);
	$iFree=$iFree[0]['profile'];
	if ($iFree == 1) {
		$xml .=  '<response>'.$sCRLF;
		$xml .=  $sTab.'<value>-1</value>'.$sCRLF;
		$xml .=  '</response>'.$sCRLF;
		echo($xml);
		exit;
	} else {
		myqu("UPDATE mytcg_user SET profile = 1 WHERE user_id=".$userID);
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

	} else if ($packID == 1) {
		myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
			VALUES(".$userID.",'Received free 1 card booster for completing profile details.', NOW(), 0)");
			
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
			VALUES(".$userID.", ".$packID.", NULL, NULL, 
				now(), 'Received free 1 card booster for completing profile details.', 0, NULL, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 11)");

	}
	  
	  $xml .=  '<response>'.$sCRLF;
	  $xml .=  $sTab.'<value>1</value>'.$sCRLF;
	  $xml .=  $sTab.'<count>'.sizeof($cards).'</count>'.$sCRLF;
	  $xml .=  $sTab.'<cards>'.$sCRLF;
	  $iCount = 0;
	  foreach($cards as $card){
		$query='SELECT C.card_id, C.image, C.description,I.description AS path '
			  .'FROM mytcg_card C '
			  .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
			  .'WHERE C.card_id = '.$card['cardId'];
		$aCard=myqu($query);
		$xml .= $sTab.$sTab.'<card_'.$iCount.'>'.$sCRLF;
		$xml .= $sTab.$sTab.$sTab.'<cardid>'.$card['cardId'].'</cardid>'.$sCRLF;
		$xml .= $sTab.$sTab.$sTab.'<description>'.$aCard[0]['description'].'</description>'.$sCRLF;
		$xml .= $sTab.$sTab.$sTab.'<qty>'.$card['quantity'].'</qty>'.$sCRLF;
		$xml .= $sTab.$sTab.$sTab.'<path>'.$aCard[0]['path'].'</path>'.$sCRLF;
		$xml .= $sTab.$sTab.$sTab.'<img>'.$aCard[0]['image'].'</img>'.$sCRLF;
		$xml .= $sTab.$sTab.'</card_'.$iCount.'>'.$sCRLF;
		$iCount++;
	  }
	  $xml .=  $sTab.'</cards>'.$sCRLF;
	  $xml .=  '</response>'.$sCRLF;
	}else{
	  $xml .=  '<response>'.$sCRLF;
	  $xml .=  $sTab.'<value>0</value>'.$sCRLF;
	  $xml .=  '</response>'.$sCRLF;
	}
	echo($xml);
  exit;
}
?>