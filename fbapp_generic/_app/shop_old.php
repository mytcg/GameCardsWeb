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
  $aDetails=myqu('SELECT P.product_id, PT.description AS ptype, P.description, P.price, P.no_of_cards '
    .'FROM mytcg_product P '
    .'INNER JOIN mytcg_producttype PT '
    .'ON P.producttype_id = PT.producttype_id '
    .'WHERE P.product_id='.$packID);
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
      $cards = openStarter($userID,$iProductID);
    }
    elseif($aDetails[0]['ptype'] == "Booster"){
      $cards = openBooster($userID,$iProductID);
    }
    
    if(sizeof($cards) > 0){
      //PAY FOR ORDER ITEM * QUANTITY ORDERED   
      $iCreditsAfterPurchase = $iCredits - $itemCost;
      $aCreditsLeft=myqu("UPDATE mytcg_user SET credits={$iCreditsAfterPurchase} WHERE user_id=".$userID);
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
?>