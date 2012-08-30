<?php
  //GET REQUIRED FILES
  require_once("../../config.php");
  require_once("../../func.php");
  $sCRLF="\r\n";
  $sTab=chr(9);
  
  //SETUP PREFIX FOR TABLES
  $pre = $Conf["database"]["table_prefix"];

  $userID = $_SESSION["user"]["id"];
  
  
if(isset($_GET['getcards']))
{
	$packID = $_GET['pack'];
	openStarter($pre,'-1',$packID);
}
  
  
//GETS THE CONTENTS OF A STARTER PACK AND GIVES IT TO THE USER
//also returns the card_id of the highest quality card
function openStarter($pre,$userID,$packID){
  $aGetCards = myqu("SELECT c.*, cq.booster_probability, I.description AS path
    FROM mytcg_card c
    INNER JOIN mytcg_imageserver I ON (c.thumbnail_imageserver_id = I.imageserver_id)
    INNER JOIN mytcg_productcard AS pc
    ON pc.card_id = c.card_id
    INNER JOIN mytcg_cardquality AS cq
    ON cq.cardquality_id = c.cardquality_id
    WHERE pc.product_id={$packID}");
  $iNumCards = sizeof($aGetCards);
  
  if($userID=='-1'){
  	//return xml of potential cards in pack
	$sCRLF="\r\n";
	$sTab=chr(9);
	//get product details
	$sql = "SELECT P.*, I.description AS imageserver FROM ".$pre."_product P INNER JOIN ".$pre."_imageserver I ON P.full_imageserver_id=I.imageserver_id WHERE P.product_id=".$packID." LIMIT 1";
	$pack = myqu($sql);
	$pack = $pack[0];
  	echo '<pack>'.$sCRLF;
	echo $sTab.'<desc val="'.$pack['description'].'" />'.$sCRLF;
	echo $sTab.'<size val="'.$pack['no_of_cards'].'" />'.$sCRLF;
	echo $sTab.'<path val="'.$pack['imageserver'].'" />'.$sCRLF;
	echo $sTab.'<image val="'.$pack['image'].'" />'.$sCRLF;
	echo $sTab.'<price val="'.$pack['premium'].'" />'.$sCRLF;
	echo $sTab.'<count val="'.$iNumCards.'" />'.$sCRLF;
	echo $sTab.'<cards>'.$sCRLF;
	if($iNumCards > 0){
		$i = 0;
		foreach($aGetCards as $card){
			$sql = "SELECT COUNT(*) AS 'possess' FROM ".$pre."_usercard WHERE card_id=".$card['card_id']." AND user_id=".$_SESSION["user"]["id"];
			$usercard = myqu($sql);
			echo $sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<card_id val="'.$card['card_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<possess val="'.$usercard[0]['possess'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<path val="'.$card['path'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo $sTab.'</cards>'.$sCRLF;
	echo '</pack>';
	exit;
  }
  
  $cards = array();
  for ($i = 0; $i < $iNumCards; $i++){
    //GET CARD FROM STACK
    $iCardID = $aGetCards[$i]['card_id'];
  
    //REMOVE THE CARD FROM THE STACK
    //this bit is removed for now, as the database doesnt support individual cards amounts
    //$iReleasedLeft=$aGetCards[$i]['released_left']-1;
    //$aReleasedLeft=myqu("UPDATE mytcg_card SET released_left={$iReleasedLeft} WHERE card_id={$iCardID}");
            
    //GIVE THE CARD TO THE USER
    $aCards=myqu("INSERT mytcg_usercard (user_id, card_id, usercardstatus_id)
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
  
  //GENERATES THE CONTENTS OF A BOOSTER PACK AND GIVES IT TO THE USER
function openBooster($pre,$userID,$packID){
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
    $aCards=myqu("INSERT mytcg_usercard (user_id, card_id, usercardstatus_id, is_new)
      SELECT {$userID}, {$iCardID}, usercardstatus_id, 1 
      FROM mytcg_usercardstatus
      WHERE description = 'Album'");
	  
	  $sql = "INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
		VALUES({$userID}, {$packID}, (SELECT max(usercard_id) from mytcg_usercard where card_id = {$iCardID} and user_id = {$userID}), {$iCardID}, 
				now(), 'Card received in booster', 0, NULL, 'web',  NULL, 10)";
	myqu($sql);
	
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
  
  
  //BUY ITEMS IN CART
  if ($_GET['buyItem']){
    $xml = "";
    //CHECK LOGIN STATUS
    if(!$userID){
     $xml .=  '<response>'.$sCRLF;
     $xml .=  $sTab.'<value>0</value>'.$sCRLF;
     $xml .=  '</response>'.$sCRLF;
     echo($xml);
     exit; 
    }
  
    //GET PRODUCT DETAIL
    $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, P.premium, P.price cred, P.no_of_cards 
      FROM mytcg_product P 
      INNER JOIN mytcg_producttype PT 
      ON P.producttype_id = PT.producttype_id 
      WHERE P.product_id='.$_GET['buyItem']);
    $iProductID = $aDetails[0]['product_id'];

    //VALIDATE USER CREDITS
    //User credits
    $iCredits=myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, credits cred, premium prem FROM mytcg_user WHERE user_id=".$userID);
    $iCred=$iCredits[0]['premium'];
    
    //Total order cost
    $itemCost = $aDetails[0]['premium'];
    $bValid = ($iCred >= $itemCost);
    
    if ($bValid)
    {
      //RECEIVE ITEM
      $cards;
      if ($aDetails[0]['ptype'] == "Starter"){
        $cards = openStarter($pre,$userID,$iProductID);
      }
      elseif($aDetails[0]['ptype'] == "Booster"){
        $cards = openBooster($pre,$userID,$iProductID);
      }
      $iCreditsAfterPurchase=0;
      if(sizeof($cards) > 0){
        //PAY FOR ORDER ITEM * QUANTITY ORDERED   
		  $freemium = $iCredits[0]['cred'];
		  $premium = $iCredits[0]['prem'];
		  
		  if($freemium > $itemCost){
			$iCreditsAfterPurchase = $freemium - $itemCost;
			$aCreditsLeft=myqu("UPDATE mytcg_user SET credits={$iCreditsAfterPurchase} WHERE user_id=".$userID);
			$premiumCost = 0;
			$freemiumCost = $itemCost*-1;
			
			$_SESSION["user"]["credits"] = $iCreditsAfterPurchase;
		  }else{
			$iCreditsAfterPurchase = $premium - ($itemCost-$freemium);
			$aCreditsLeft=myqu("UPDATE mytcg_user SET credits=0,premium={$iCreditsAfterPurchase} WHERE user_id=".$userID);
			$premiumCost = ($itemCost-$freemium)*-1;
			$freemiumCost = $freemium*-1;
			
			$_SESSION["user"]["credits"] = 0;
			$_SESSION["user"]["premium"] = $iCreditsAfterPurchase;
		  }
        
        myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
        VALUES(".$userID.",'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', NOW(), ".(-1*$itemCost).")");
		
		myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
		VALUES(".$userID.", ".$iProductID.", NULL, NULL, 
				now(), 'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', -".$itemCost.", ".$freemiumCost.", ".$premiumCost.", NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 10)");
        
       
        
        $xml .=  '<response>'.$sCRLF;
        $xml .=  $sTab.'<value>1</value>'.$sCRLF;
        $xml .=  $sTab.'<credits>'.$iCreditsAfterPurchase.'</credits>'.$sCRLF;
		$xml .=  $sTab.'<price>'.$itemCost.'</price>'.$sCRLF;
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
    }
    else{
      $xml .=  '<response>'.$sCRLF;
      $xml .=  $sTab.'<value>-1</value>'.$sCRLF;
      $xml .=  '</response>'.$sCRLF;
      echo($xml);
    }
	checkAchis($userID, 1);
    exit;
  }
  
	//INIT RETURN
  if($_GET["init"]){
    $xml = "";
    $query='SELECT A.product_id, A.description, A.premium, A.no_of_cards, A.image, '
    		.'A.background_position, B.description AS imageserver '
            .'FROM mytcg_product A '
            .'INNER JOIN mytcg_imageserver B '
            .'ON A.full_imageserver_id=B.imageserver_id ';
            
    $aProducts=myqu($query);
    $iCount = 0;
    $xml .=  '<packs>'.$sCRLF;
    while ($iPackID=$aProducts[$iCount]['product_id']){
      $xml .=  $sTab.'<pack_'.$iCount.'>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<id>'.$iPackID.'</id>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<desc>'.$aProducts[$iCount]['description'].'</desc>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<img>'.$aProducts[$iCount]['image'].'</img>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<price>'.$aProducts[$iCount]['premium'].'</price>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<cards>'.$aProducts[$iCount]['no_of_cards'].'</cards>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<backgroundposition>'.$aProducts[$iCount]['background_position'].'</backgroundposition>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<fullimageserver>'.$aProducts[$iCount]['imageserver'].'</fullimageserver>'.$sCRLF;
      $xml .=  $sTab.'</pack_'.$iCount.'>'.$sCRLF;
      $iCount++;
    }
    $xml .=  '<iCount>'.$iCount.'</iCount>'.$sCRLF;
    $xml .=  '</packs>'.$sCRLF;
    echo($xml);
    exit;
  }

  //CATEGORY FILTER
  if(intval($_GET["cat"]) > 0){
    $catID = $_GET["cat"];
    $xml = "";
    $query='SELECT A.product_id, A.description, A.premium, A.no_of_cards, A.image, A.background_position, B.description AS imageserver,C.category_id
            FROM mytcg_product A
            INNER JOIN mytcg_imageserver B ON A.full_imageserver_id=B.imageserver_id
            INNER JOIN mytcg_productcategory_x C ON A.product_id = C.product_id
            WHERE C.category_id = '.$catID.' AND C.category_id <> 51 ';
    $aProducts=myqu($query);
    $iCount = 0;
    $xml .=  '<packs>'.$sCRLF;
    while ($iPackID=$aProducts[$iCount]['product_id']){
      $xml .=  $sTab.'<pack_'.$iCount.'>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<id>'.$iPackID.'</id>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<desc>'.$aProducts[$iCount]['description'].'</desc>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<img>'.$aProducts[$iCount]['image'].'</img>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<price>'.$aProducts[$iCount]['premium'].'</price>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<cards>'.$aProducts[$iCount]['no_of_cards'].'</cards>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<backgroundposition>'.$aProducts[$iCount]['background_position'].'</backgroundposition>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<fullimageserver>'.$aProducts[$iCount]['imageserver'].'</fullimageserver>'.$sCRLF;
      $xml .=  $sTab.'</pack_'.$iCount.'>'.$sCRLF;
      $iCount++;
    }

    if(intval($_GET["l"])==1){ //GET ALL LEVEL 2 STUFF
      $query='SELECT * FROM mytcg_category WHERE level = 2 AND parent_id = '.$catID;
      $aSub=myqu($query);
      while ($iCatID=$aSub[$iCount]['category_id']){
        $query='SELECT A.product_id, A.description, A.premium, A.no_of_cards, A.image, A.background_position, B.description AS imageserver,C.category_id
                FROM mytcg_product A
                INNER JOIN mytcg_imageserver B ON A.full_imageserver_id=B.imageserver_id
                INNER JOIN mytcg_productcategory_x C ON A.product_id = C.product_id
                WHERE C.category_id = '.$iCatID.' AND C.category_id <> 51 ';
        $aProducts=myqu($query);
        while ($iPackID=$aProducts[$iCount]['product_id']){
          $xml .=  $sTab.'<pack_'.$iCount.'>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<id>'.$iPackID.'</id>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<desc>'.$aProducts[$iCount]['description'].'</desc>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<img>'.$aProducts[$iCount]['image'].'</img>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<price>'.$aProducts[$iCount]['premium'].'</price>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<cards>'.$aProducts[$iCount]['no_of_cards'].'</cards>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<backgroundposition>'.$aProducts[$iCount]['background_position'].'</backgroundposition>'.$sCRLF;
          $xml .=  $sTab.$sTab.'<fullimageserver>'.$aProducts[$iCount]['imageserver'].'</fullimageserver>'.$sCRLF;
          $xml .=  $sTab.'</pack_'.$iCount.'>'.$sCRLF;
          $iCount++;
        }
        
      }
    }
    $xml .=  '<iCount>'.$iCount.'</iCount>'.$sCRLF;
    $xml .=  '</packs>'.$sCRLF;
    echo($xml);
    exit;
  }
?>