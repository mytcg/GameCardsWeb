<?php
  
//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
$sCRLF="\r\n";
$sTab=chr(9);

//SETUP PREFIX FOR TABLES
$pre = $Conf["database"]["table_prefix"];

$userID = $_SESSION["user"]["id"];

if(intval($_GET["cat"]) > 0){
  $catID = $_GET["cat"];
  $sCats = getCardCategories($catID);
  $query='SELECT DISTINCT C.category_id, CA.description  '
      .'FROM mytcg_usercard UC '
      .'INNER JOIN mytcg_card C ON UC.card_id = C.card_id '
      .'INNER JOIN mytcg_category CA ON C.category_id = CA.category_id '
      .'INNER JOIN mytcg_usercardstatus UCS ON UC.usercardstatus_id = UCS.usercardstatus_id '
      .'WHERE UC.user_id = '.$userID.' AND UCS.description = "Album" '
      .'ORDER BY CA.description ASC';

  $aAlbums=myqu($query);
  $iSizeAlbums=sizeof($aAlbums);
  echo '<init>'.$sCRLF;
  echo $sTab.'<albumcount val="'.$iSizeAlbums.'" />'.$sCRLF;
  
  //ALL CARDS ALBUM LIST
  $query='SELECT C.card_id, C.image, C.description,I.description AS path '
        .'FROM mytcg_card C '
        .'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) ';
  $aAll=myqu($query);
  $iAll=sizeof($aAll);
  echo $sTab.'<album_all>'.$sCRLF;
    echo $sTab.$sTab.'<category_id>all</category_id>'.$sCRLF;
    echo $sTab.$sTab.'<description>All</description>'.$sCRLF;
    echo $sTab.$sTab.'<cards>'.$sCRLF;
    $iOwned = 0;
    for ($n=0;$n<$iAll;$n++){
      $iCC = getCardOwnedCount($aAll[$n]['card_id']);
      if($iCC > 0){
        $iOwned++;
      }
      echo $sTab.$sTab.'<card_'.$n.'>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cardid>'.$aAll[$n]['card_id'].'</cardid>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<description>'.$aAll[$n]['description'].'</description>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<qty>'.$iCC.'</qty>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<path>'.$aAll[$n]['path'].'</path>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<img>'.$aAll[$n]['image'].'</img>'.$sCRLF;
      echo $sTab.$sTab.'</card_'.$n.'>'.$sCRLF;
    }
    echo $sTab.$sTab.'</cards>'.$sCRLF;
    echo $sTab.'<totalcards>'.$iAll.'</totalcards>'.$sCRLF;
    echo $sTab.'<ownedcards>'.$iOwned.'</ownedcards>'.$sCRLF;
  echo $sTab.'</album_all>'.$sCRLF;
  
  
  for ($iCount=0;$iCount<$iSizeAlbums;$iCount++){
    $ownedCards = 0;
    echo $sTab.'<album_'.$iCount.'>'.$sCRLF;
    echo $sTab.$sTab.'<category_id>'.$aAlbums[$iCount]['category_id'].'</category_id>'.$sCRLF;
    echo $sTab.$sTab.'<description>'.$aAlbums[$iCount]['description'].'</description>'.$sCRLF;
    echo $sTab.$sTab.'<cards>'.$sCRLF;
  $sql = 'SELECT DISTINCT(C.card_id), C.image, C.description,I.description AS path '
      .'FROM '.$pre.'_card C '
      .'INNER JOIN '.$pre.'_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
      .'WHERE C.category_id = '.$aAlbums[$iCount]['category_id'].' ';
    $aCards=myqu($sql);
    //echo $sql;
    $iSizeCards = sizeof($aCards);
    for ($i=0;$i<$iSizeCards;$i++){
      $aCardsOwned=myqu(
        'SELECT COUNT(UC.card_id) AS qty '
        .'FROM '.$pre.'_usercard UC '
        .'WHERE UC.card_id = '.$aCards[$i]['card_id'].' AND UC.user_id = '.$userID.' AND UC.usercardstatus_id=1'
        .' GROUP BY UC.card_id'
      );
      if((int)$aCardsOwned[0]['qty'] > 0){
        $ownedCards++;
      }
      echo $sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cardid>'.$aCards[$i]['card_id'].'</cardid>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<description>'.$aCards[$i]['description'].'</description>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<qty>'.$aCardsOwned[0]['qty'].'</qty>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<path>'.$aCards[$i]['path'].'</path>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<img>'.$aCards[$i]['image'].'</img>'.$sCRLF;
      echo $sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
    }
    echo $sTab.$sTab.'</cards>'.$sCRLF;
    echo $sTab.'<totalcards>'.$iSizeCards.'</totalcards>'.$sCRLF;
    echo $sTab.'<ownedcards>'.$ownedCards.'</ownedcards>'.$sCRLF;
    echo $sTab.'</album_'.$iCount.'>'.$sCRLF;
  }
	//check for any cards received
	$sql = "SELECT UC.usercard_id, C.*, I.description AS path, CQ.description AS quality,
				(SELECT COUNT(*) AS 'possess' FROM ".$pre."_usercard WHERE card_id=C.card_id AND usercardstatus_id=1 AND user_id=".$userID.") AS possess
	        FROM ".$pre."_usercard UC 
	        INNER JOIN ".$pre."_card C ON UC.card_id = C.card_id
			INNER JOIN ".$pre."_cardquality CQ USING (cardquality_id)
	        INNER JOIN ".$pre."_usercardstatus UCS ON UC.usercardstatus_id = UCS.usercardstatus_id
	        INNER JOIN ".$pre."_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
	        WHERE UC.user_id = ".$userID." AND UC.usercardstatus_id = 4
	        ORDER BY C.value DESC";
	$receivedCards = myqu($sql);
	echo $sTab.'<received>'.$sCRLF;
	echo $sTab.$sTab.'<count val="'.sizeof($receivedCards).'" />'.$sCRLF;
	//echo $sTab.$sTab.'<count val="0" />'.$sCRLF;
	if(sizeof($receivedCards) > 0){
		$i = 0;
		echo $sTab.$sTab.'<cards>'.$sCRLF;
		foreach($receivedCards as $card){
			echo $sTab.$sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<usercard_id val="'.$card['usercard_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<card_id val="'.$card['card_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<path val="'.$card['path'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<ranking val="'.$card['ranking'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<avgranking val="'.$card['avgranking'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<quality val="'.$card['quality'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<value val="'.$card['value'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<possess val="'.$card['possess'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
		echo $sTab.$sTab.'</cards>'.$sCRLF;
	}
	echo $sTab.'</received>'.$sCRLF;
  echo '</init>'.$sCRLF;
}

if (intval($_GET['viewed']==1)){
  $query='UPDATE mytcg_usercard SET is_new = 0 WHERE user_id = '.$userID;
  myqu($query);
}

if (intval($_GET['init']==1)){
$query='SELECT DISTINCT C.category_id, CA.description  '
    		.'FROM mytcg_usercard UC '
    		.'INNER JOIN mytcg_card C ON UC.card_id = C.card_id '
    		.'INNER JOIN mytcg_category CA ON C.category_id = CA.category_id '
    		.'INNER JOIN mytcg_usercardstatus UCS ON UC.usercardstatus_id = UCS.usercardstatus_id '
    		.'WHERE UC.user_id = '.$userID.' AND UCS.description = "Album" '
    		.'ORDER BY CA.description ASC';
  $aAlbums=myqu($query);
	$iSizeAlbums=sizeof($aAlbums);
	echo '<init>'.$sCRLF;
	echo $sTab.'<albumcount val="'.$iSizeAlbums.'" />'.$sCRLF;
  
  //ALL CARDS ALBUM LIST
  $query='SELECT C.card_id, C.image, C.description,I.description AS path, C.value, CA.category_id  '
        .'FROM mytcg_card C '
        .'INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = imageserver_id) '
        .'INNER JOIN mytcg_category CA ON (CA.category_id = C.category_id)'
        .'ORDER BY C.description ASC ';
  $aAll=myqu($query);
  $iAll=sizeof($aAll);
  echo $sTab.'<album_all>'.$sCRLF;
    echo $sTab.$sTab.'<category_id>all</category_id>'.$sCRLF;
    echo $sTab.$sTab.'<description>All</description>'.$sCRLF;
    echo $sTab.$sTab.'<cards>'.$sCRLF;
    $iOwned = 0;
    for ($n=0;$n<$iAll;$n++){
      $iCC = getCardOwnedCount($aAll[$n]['card_id']);
      if($iCC > 0){
        $iOwned++;
      }
      echo $sTab.$sTab.'<card_'.$n.'>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cardid>'.$aAll[$n]['card_id'].'</cardid>'.$sCRLF;
	  echo $sTab.$sTab.$sTab.'<value>'.$aAll[$n]['value'].'</value>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<description>'.$aAll[$n]['description'].'</description>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<qty>'.$iCC.'</qty>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<path>'.$aAll[$n]['path'].'</path>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<img>'.$aAll[$n]['image'].'</img>'.$sCRLF;
      echo $sTab.$sTab.'</card_'.$n.'>'.$sCRLF;
    }
    echo $sTab.$sTab.'</cards>'.$sCRLF;
    echo $sTab.'<totalcards>'.$iAll.'</totalcards>'.$sCRLF;
    echo $sTab.'<ownedcards>'.$iOwned.'</ownedcards>'.$sCRLF;
  echo $sTab.'</album_all>'.$sCRLF;
  
  //NEW CARDS ALBUM LIST
  $query='SELECT C.*,COUNT(UC.card_id) AS iQty, I.description AS path
          FROM mytcg_usercard UC 
          INNER JOIN mytcg_card C ON UC.card_id = C.card_id 
          INNER JOIN mytcg_usercardstatus UCS ON UC.usercardstatus_id = UCS.usercardstatus_id
          INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
          WHERE UC.user_id = '.$userID.' AND UCS.description = "Album" AND UC.is_new = 1 
          GROUP BY UC.card_id 
          ORDER BY C.description ASC;';
  $aNew=myqu($query);
  $iNew=sizeof($aNew);
  echo $sTab.'<album_new>'.$sCRLF;
    echo $sTab.$sTab.'<category_id>new</category_id>'.$sCRLF;
    echo $sTab.$sTab.'<description>New</description>'.$sCRLF;
    echo $sTab.$sTab.'<cards>'.$sCRLF;
    $iOwned = 0;
    for ($n=0;$n<$iNew;$n++){
      $iCC = $aNew[$n]['iQty'];
      if($iCC > 0){
        $iOwned+=$iCC;
      }
      echo $sTab.$sTab.'<card_'.$n.'>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cardid>'.$aNew[$n]['card_id'].'</cardid>'.$sCRLF;
	  echo $sTab.$sTab.$sTab.'<value>'.$aNew[$n]['value'].'</value>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<description>'.$aNew[$n]['description'].'</description>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<qty>'.$aNew[$n]['iQty'].'</qty>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<path>'.$aNew[$n]['path'].'</path>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<img>'.$aNew[$n]['image'].'</img>'.$sCRLF;
      echo $sTab.$sTab.'</card_'.$n.'>'.$sCRLF;
    }
    echo $sTab.$sTab.'</cards>'.$sCRLF;
    echo $sTab.'<totalcards>'.$iNew.'</totalcards>'.$sCRLF;
    echo $sTab.'<ownedcards>'.$iOwned.'</ownedcards>'.$sCRLF;
  echo $sTab.'</album_new>'.$sCRLF;
  
	for ($iCount=0;$iCount<$iSizeAlbums;$iCount++){
	  $ownedCards = 0;
	  echo $sTab.'<album_'.$iCount.'>'.$sCRLF;
		echo $sTab.$sTab.'<category_id>'.$aAlbums[$iCount]['category_id'].'</category_id>'.$sCRLF;
		echo $sTab.$sTab.'<description>'.$aAlbums[$iCount]['description'].'</description>'.$sCRLF;
    echo $sTab.$sTab.'<cards>'.$sCRLF;
	$sql = 'SELECT DISTINCT(C.card_id), C.image, C.description, I.description AS path, C.value '
      .'FROM mytcg_card C '
      .'INNER JOIN mytcg_imageserver I ON (C.thumbnail_imageserver_id = imageserver_id) '
      .'WHERE C.category_id = '.$aAlbums[$iCount]['category_id'].' ';
    $aCards=myqu($sql);
    //echo $sql;
    $iSizeCards = sizeof($aCards);
    for ($i=0;$i<$iSizeCards;$i++){
      $aCardsOwned=myqu(
        'SELECT COUNT(UC.card_id) AS qty '
        .'FROM mytcg_usercard UC '
        .'WHERE UC.card_id = '.$aCards[$i]['card_id'].' AND UC.user_id = '.$userID.' AND UC.usercardstatus_id=1'
        .' GROUP BY UC.card_id'
      	);
      if((int)$aCardsOwned[0]['qty'] > 0){
        $ownedCards++;
      }
      echo $sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cardid>'.$aCards[$i]['card_id'].'</cardid>'.$sCRLF;
	  echo $sTab.$sTab.$sTab.'<value>'.$aCards[$i]['value'].'</value>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<description>'.$aCards[$i]['description'].'</description>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<qty>'.$aCardsOwned[0]['qty'].'</qty>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<path>'.$aCards[$i]['path'].'</path>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<img>'.$aCards[$i]['image'].'</img>'.$sCRLF;
      echo $sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
    }
    echo $sTab.$sTab.'</cards>'.$sCRLF;
    echo $sTab.'<totalcards>'.$iSizeCards.'</totalcards>'.$sCRLF;
    echo $sTab.'<ownedcards>'.$ownedCards.'</ownedcards>'.$sCRLF;
    echo $sTab.'</album_'.$iCount.'>'.$sCRLF;
	}
	//check for any cards received
	$sql = "SELECT UC.usercard_id, C.*, I.description AS path, CQ.description AS quality,
				(SELECT COUNT(*) AS 'possess' FROM mytcg_usercard WHERE card_id=C.card_id AND usercardstatus_id=1 AND user_id=".$userID.") AS possess
	        FROM mytcg_usercard UC 
	        INNER JOIN mytcg_card C ON UC.card_id = C.card_id
			INNER JOIN mytcg_cardquality CQ USING (cardquality_id)
	        INNER JOIN mytcg_usercardstatus UCS ON UC.usercardstatus_id = UCS.usercardstatus_id
	        INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
	        WHERE UC.user_id = ".$userID." AND UC.usercardstatus_id = 4
	        ORDER BY C.value DESC";
	$receivedCards = myqu($sql);
	echo $sTab.'<received>'.$sCRLF;
	echo $sTab.$sTab.'<count val="'.sizeof($receivedCards).'" />'.$sCRLF;
	//echo $sTab.$sTab.'<count val="0" />'.$sCRLF;
	if(sizeof($receivedCards) > 0){
		$i = 0;
		echo $sTab.$sTab.'<cards>'.$sCRLF;
		foreach($receivedCards as $card){
			echo $sTab.$sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<usercard_id val="'.$card['usercard_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<card_id val="'.$card['card_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<path val="'.$card['path'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<ranking val="'.$card['ranking'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<avgranking val="'.$card['avgranking'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<quality val="'.$card['quality'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<value val="'.$card['value'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.$sTab.'<possess val="'.$card['possess'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
		echo $sTab.$sTab.'</cards>'.$sCRLF;
	}
	echo $sTab.'</received>'.$sCRLF;
	echo '</init>'.$sCRLF;
}


if(isset($_GET['auction']) && $_GET['auction']=='1')
{
	$card_id = $_GET['card_id'];
	$minimum_bid = $_GET['minimum_bid'];
	$price = $_GET['price'];
	$date_expired = $_GET['date_expired'].' 23:59:59';
	
	//Get first available usercard
	$sql = "SELECT UC.`usercard_id`, UC.`deck_id`, C.`description`
			FROM `mytcg_usercard` UC 
			JOIN `mytcg_card` C USING (card_id)
			WHERE UC.`user_id`=".$userID." 
			AND UC.`card_id`=".$card_id."
			AND UC.`usercardstatus_id`=1
			ORDER BY UC.`deck_id` ASC, UC.`usercard_id` ASC
			LIMIT 1;";
	$uc = myqu($sql);
	$usercard_id = $uc[0]['usercard_id'];
	
	//Create new auction record
	$sql = "INSERT INTO `mytcg_market` (
				`markettype_id`,
				`marketstatus_id`,
				`usercard_id`,
				`date_created`,
				`date_expired`,
				`price`,
				`minimum_bid`,
				`user_id`
			) VALUES (
				1,
				1,
				".$usercard_id.",
				'".date('Y-m-d H:i:00')."',
				'".$date_expired."',
				".$price.",
				".$minimum_bid.",
				".$userID."
			);";
			
	echo '<auction>'.$sCRLF;
	
	$auctionCost = intval(intval($minimum_bid) * 0.1);
	if(intval($price) > 0){
		$auctionCost = intval(intval($price) * 0.1);
	}
	$auctionCost = ($auctionCost < 5) ? 5 : $auctionCost;
	$userQuery = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium FROM mytcg_user WHERE user_id=".$userID);
	$userCredits = $userQuery[0]['premium'];
	
	if($market_id = myqu($sql))
	{
		//Update usercard status
			$sql = "UPDATE mytcg_usercard SET usercardstatus_id=2, deck_id=NULL WHERE usercard_id=".$usercard_id." AND user_id=".$userID.";";
			myqu($sql);
			
			//Subtract auction creation cost from user
			$auctionCost = 0;//intval(intval($minimum_bid) * 0.1);
			//if(intval($price) > 0){
				//$auctionCost = intval(intval($price) * 0.1);
			//}
			//$auctionCost = ($auctionCost < 5) ? 5 : $auctionCost;
			//myqu("UPDATE mytcg_user SET premium = premium-".$auctionCost." WHERE user_id = ".$userID);
			
			//Add transactionlog entry
			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					VALUES(".$userID.", 'Created an auction for ".$uc[0]['description']."', NOW(), 0)");
			
			$sql = "INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type)
					VALUES(".$userID.", NULL, ".$usercard_id.", (SELECT card_id FROM mytcg_usercard WHERE usercard_id = ".$usercard_id."), 
							now(), 'Created an auction for ".$uc[0]['description']."', 0, NULL, 'web',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = ".$userID."), 6)";
			myqu($sql);
			
			//Get user credits
			$userQuery = myqu("SELECT (ifnull(premium,0)+ifnull(credits,0)) premium FROM mytcg_user WHERE user_id=".$userID);
			$userCredits = $userQuery[0]['premium'];
			
			$sql = "SELECT COUNT(usercard_id) AS counted
				FROM mytcg_usercard UC 
				WHERE user_id=".$userID." 
				AND card_id=".$card_id."
				AND usercardstatus_id=1
				GROUP BY card_id";
			$cardcount = myqu($sql);
			$cardcount = $cardcount[0]['counted'];
			
			//Success
			echo $sTab.'<result val="success" />'.$sCRLF;
			echo $sTab.'<cost val="'.$auctionCost.'" />'.$sCRLF;
			echo $sTab.'<count val="'.$cardcount.'" />'.$sCRLF;
			echo $sTab.'<credits val="'.$userCredits.'" />'.$sCRLF;
	}
	// else
	// {
		// //Failed to create auction
		// echo $sTab.'<result val="fail" />'.$sCRLF;
	// }
	echo '</auction>';
}


if(isset($_GET['search']))
{
	$searchstring = $_GET['string'];
	
	$sql = "SELECT user_id, username
			FROM {$pre}_user
			WHERE user_id != {$userID}
			AND (username LIKE '%{$searchstring}%'
			OR email_address LIKE '%{$searchstring}%'
			OR name LIKE '%{$searchstring}%')
			ORDER BY username ASC";
	$searchResults = myqu($sql);
	
	//return xml
	echo '<search>'.$sCRLF;
	echo $sTab.'<found val="'.sizeof($searchResults).'" />'.$sCRLF;
	if(sizeof($searchResults) > 0){
		echo $sTab.'<results>'.$sCRLF;
		$i = 0;
		foreach($searchResults as $result){
			echo $sTab.$sTab.'<result_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<user_id val="'.$result['user_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<username val="'.$result['username'].'" />'.$sCRLF;
			echo $sTab.$sTab.'</result_'.$i.'>'.$sCRLF;
			$i++;
		}
		echo $sTab.'</results>'.$sCRLF;
	}
	echo '</search>';
	exit;
}


if(isset($_GET['send']))
{
	$friend_id = $_GET['friend'];
	$friend_username = $_GET['name'];
	$card_id = $_GET['card'];
	
	//send the card
	$sql = "UPDATE {$pre}_usercard
			SET user_id = {$friend_id}, 
				deck_id = NULL,
				usercardstatus_id = 4
			WHERE card_id = {$card_id}
			AND user_id = {$userID}
			AND usercardstatus_id = 1
			LIMIT 1";
	myqu($sql);

	//record trade transaction
	$sql = "INSERT INTO ".$pre."_tradecard (user_id,trademethod,detail,date,card_id,status_id,note) 
			VALUES (".$userID.",'username','".$friend_username."',NOW(),".$card_id.",0,'');";
	myqu($sql);
	
	//return xml
	echo '<send>'.$sCRLF;
	if(true){
		echo $sTab.'<result val="1" />'.$sCRLF;
	}
	else{
		echo $sTab.'<result val="0" />'.$sCRLF;
		echo $sTab.'<message val="Unexpected error. Card was not sent to friend." />'.$sCRLF;
	}
	echo '</send>';
	exit;
}


if(isset($_GET['accept']))
{
	$usercard = $_GET['accept'];
	if($usercard == 'all'){
		echo $sql = "UPDATE ".$pre."_usercard SET usercardstatus_id=1, is_new=1 WHERE user_id=".$userID." AND usercardstatus_id=4";
	}
	else{
		echo $sql = "UPDATE ".$pre."_usercard SET usercardstatus_id=1, is_new=1 WHERE usercard_id=".$usercard;
	}
	myqu($sql);
	exit;
}


if(isset($_GET['reject']))
{
	$usercard = $_GET['reject'];
	if($usercard == 'all'){
		echo $sql = "UPDATE ".$pre."_usercard SET usercardstatus_id=3, is_new=0 WHERE user_id=".$userID." AND usercardstatus_id=4";
	}
	else{
		echo $sql = "UPDATE ".$pre."_usercard SET usercardstatus_id=3, is_new=0 WHERE usercard_id=".$usercard;
	}
	myqu($sql);
	exit;
}

?>