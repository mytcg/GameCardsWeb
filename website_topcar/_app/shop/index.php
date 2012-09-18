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
	echo $sTab.'<price val="'.$pack['price'].'" />'.$sCRLF;
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
  
    //GET PRODUCT DETAILS
    $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, P.price, P.no_of_cards '
      .'FROM mytcg_product P '
      .'INNER JOIN mytcg_producttype PT '
      .'ON P.producttype_id = PT.producttype_id '
      .'WHERE P.product_id="'.$_GET['buyItem'].'"');
    $iProductID = $aDetails[0]['product_id'];

    //VALIDATE USER CREDITS
    //User credits
    $iCredits=myqu("SELECT credits FROM mytcg_user WHERE user_id=".$userID);
    $iCredits=$iCredits[0]['credits'];
    
    //Total order cost
    $itemCost = $aDetails[0]['price'];
    $bValid = ($iCredits >= $itemCost);
    
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
      
      if(sizeof($cards) > 0){
        //PAY FOR ORDER ITEM * QUANTITY ORDERED   
        $iCreditsAfterPurchase = $iCredits - $itemCost;
        $aCreditsLeft=myqu("UPDATE {$pre}_user SET credits={$iCreditsAfterPurchase} WHERE user_id=".$userID);
        $_SESSION["user"]["credits"] = $iCreditsAfterPurchase;
        
        myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
          VALUES(".$userID.",'Spent ".$itemCost." credits on ".$aDetails[0]['description'].".', NOW(), ".(-1*$itemCost).")");
        
        $xml .=  '<response>'.$sCRLF;
        $xml .=  $sTab.'<value>1</value>'.$sCRLF;
        $xml .=  $sTab.'<credits>'.$iCreditsAfterPurchase.'</credits>'.$sCRLF;
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
    exit;
  }
  
	//INIT RETURN
  if($_GET["init"]){
    $xml = "";
    $query='SELECT A.product_id, A.description, A.price, A.no_of_cards, A.image, '
    		.'A.background_position, B.description AS imageserver '
            .'FROM mytcg_product A '
            .'INNER JOIN mytcg_imageserver B '
            .'ON A.full_imageserver_id=B.imageserver_id '
            .'WHERE A.product_id < 12 ';
            
    $aProducts=myqu($query);
    $iCount = 0;
    $xml .=  '<packs>'.$sCRLF;
    while ($iPackID=$aProducts[$iCount]['product_id']){
      $xml .=  $sTab.'<pack_'.$iCount.'>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<id>'.$iPackID.'</id>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<desc>'.$aProducts[$iCount]['description'].'</desc>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<img>'.$aProducts[$iCount]['image'].'</img>'.$sCRLF;
      $xml .=  $sTab.$sTab.'<price>'.$aProducts[$iCount]['price'].'</price>'.$sCRLF;
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
    $query='SELECT A.product_id, A.description, A.price, A.no_of_cards, A.image, A.background_position, B.description AS imageserver,C.category_id
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
      $xml .=  $sTab.$sTab.'<price>'.$aProducts[$iCount]['price'].'</price>'.$sCRLF;
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
        $query='SELECT A.product_id, A.description, A.price, A.no_of_cards, A.image, A.background_position, B.description AS imageserver,C.category_id
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
          $xml .=  $sTab.$sTab.'<price>'.$aProducts[$iCount]['price'].'</price>'.$sCRLF;
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