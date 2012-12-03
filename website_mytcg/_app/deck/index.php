<?php

//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
$sCRLF="\r\n";
$sTab=chr(9);

//SETUP PREFIX FOR TABLES
$pre = $Conf["database"]["table_prefix"];

$userID = $_SESSION["user"]["id"];


if(intval($_GET["cat"]) > 0)
{
  $catID = $_GET["cat"];
  $sCats = $catID;
  if(intval($_GET["l"])==1){
    $sCats="";
    $query='SELECT * FROM mytcg_category WHERE level = 2 AND parent_id = '.$catID;
    $aCats = myqu($query);
    foreach($aCats as $cat){
      $sCats .= $cat['category_id'].",";
    }
    if(strpos($sCats,",")){
      $sCats = substr($sCats, 0, -1);
    }
  }
  
  $sql = "SELECT D.deck_id, D.category_id, CAT.description AS 'category', D.description, D.image, CONCAT(I.description,'decks/',D.image,'.jpg') AS 'imageurl'
      FROM mytcg_deck D
      JOIN mytcg_category CAT USING(category_id)
      JOIN mytcg_imageserver I ON I.imageserver_id = D.imageserver_id
      WHERE D.user_id = ".$userID." AND CAT.category_id IN (".$sCats.")
      ORDER BY D.description ASC";
  $decks = myqu($sql);
  
  // Return XML
  echo '<init>'.$sCRLF;
  echo $sTab.'<deckcount val="'.count($decks).'" />'.$sCRLF;
  echo $sTab.'<decks>'.$sCRLF;
  if(count($decks) > 0)
  {
    $d = 0;
    foreach($decks as $deck)
    {
      // Get cards in deck from database
      $sql = "SELECT UC.card_id, UC.usercard_id, CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.image, C.description, C.ranking
          FROM mytcg_usercard UC
          JOIN mytcg_card C USING(card_id)
          JOIN mytcg_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
          WHERE UC.deck_id = '".$deck['deck_id']."'
          AND UC.user_id = '".$userID."'
          ORDER BY UC.card_id, UC.usercard_id ASC";
      $deckcards = myqu($sql);
      $deckranking = 0;
      
      echo $sTab.$sTab.'<deck_'.$d.'>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<index>'.$d.'</index>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<deckid>'.$deck['deck_id'].'</deckid>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<description>'.$deck['description'].'</description>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<categoryid>'.$deck['category_id'].'</categoryid>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<category>'.$deck['category'].'</category>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<image>'.$deck['imageurl'].'</image>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<imageid>'.$deck['image'].'</imageid>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cardcount val="'.count($deckcards).'" />'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<cards>'.$sCRLF;
      if(count($deckcards) > 0)
      {
        $c = 0;
        foreach($deckcards as $card)
        {
          $sql = "SELECT CS.description AS 'stattext', CS.statvalue, CCS.description AS 'category'
              FROM mytcg_cardstat CS
              JOIN mytcg_categorystat CCS USING (categorystat_id)
              WHERE CS.card_id = ".$card['card_id'].";";
          $cardstats = myqu($sql);
          //print_r($cardstats);
          
          echo $sTab.$sTab.$sTab.$sTab.'<card_'.$c.'>'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'<cardid val="'.$card['card_id'].'" />'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'<usercardid val="'.$card['usercard_id'].'" />'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'<thumbnail val="'.$card['thumbnail'].'" />'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'<stats>'.$sCRLF;
          if(count($cardstats) > 0)
          {
            $s = 0;
            foreach($cardstats as $stat)
            {
              echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<stat_'.$s.'>'.$sCRLF;
              echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<description val="'.$stat['stattext'].'" />'.$sCRLF;
              echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<statvalue val="'.$stat['statvalue'].'" />'.$sCRLF;
              echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<category val="'.$stat['category'].'" />'.$sCRLF;
              echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'</stat_'.$s.'>'.$sCRLF;
              $s++;
            }
          }
          echo $sTab.$sTab.$sTab.$sTab.$sTab.'</stats>'.$sCRLF;
          echo $sTab.$sTab.$sTab.$sTab.'</card_'.$c.'>'.$sCRLF;
          $deckranking+= intval($card['ranking']);
          $c++;
        }
      }
      echo $sTab.$sTab.$sTab.'</cards>'.$sCRLF;
      echo $sTab.$sTab.$sTab.'<ranking>'.$deckranking.'</ranking>'.$sCRLF;
      echo $sTab.$sTab.'</deck_'.$d.'>'.$sCRLF;
      $d++;
    }
  }
  echo $sTab.'</decks>'.$sCRLF;
  $sql = "SELECT SUM(ranking) AS 'deckranking' FROM
		(
			SELECT C.card_id, UC.usercard_id, C.description, C.ranking 
			FROM mytcg_usercard UC
			JOIN mytcg_card C USING (card_id)
			WHERE UC.user_id=".$userID."
			GROUP BY C.card_id
			ORDER BY C.ranking DESC
			LIMIT 10
		)
		tmp";
  $top10 = myqu($sql);
  echo $sTab.'<top10 val="'.$top10[0]['deckranking'].'" />'.$sCRLF;
  echo '</init>';
}


if(isset($_GET['init']))
{
	myqu('UPDATE mytcg_competitiondeck SET active=2 WHERE active = 1 AND end_date <= NOW()');
	// Get decks from database
	$sql = "SELECT competitiondeck_id, imageserver_id, description, image, end_date 
			FROM mytcg_competitiondeck 
			WHERE active IN (1,2)";
	$decks = myqu($sql);

	foreach ($decks as $aCData) {
		$iCompDeckID = $aCData['competitiondeck_id'];
		$query = "SELECT D.*, I.description AS path
				  FROM mytcg_deck D
				  INNER JOIN mytcg_imageserver I ON (D.imageserver_id = I.imageserver_id)
				  WHERE competitiondeck_id = ".$iCompDeckID." AND user_id = ".$userID;
		$aGetDeck = myqu($query);
		if(sizeof($aGetDeck)==0){
			$query = "INSERT INTO mytcg_deck (user_id, category_id, imageserver_id, description, image, type, competitiondeck_id)
					  VALUES ({$userID},{$aCData['category_id']},{$aCData['imageserver_id']},'{$aCData['description']}','{$aCData['image']}',{$aCData['type']},{$iCompDeckID})";
			$rInsert = myqu($query);
		}
	}
	
	// Get decks from database
	$query = "SELECT D.description, 
				D.image, 
				D.type, 
				CAT.description as catdesc, 
				D.category_id, 
				D.deck_id, 
				I.description AS path 
				FROM mytcg_deck D 
				INNER JOIN mytcg_imageserver I 
				ON (D.imageserver_id = I.imageserver_id) 
				INNER JOIN mytcg_category CAT 
				ON (D.category_id = CAT.category_id) 
				WHERE D.user_id= ".$userID;
	$decks = myqu($query);
	
	// Return XML
	echo '<init>'.$sCRLF;
	echo $sTab.'<deckcount val="'.count($decks).'" />'.$sCRLF;
	echo $sTab.'<decks>'.$sCRLF;
	if(count($decks) > 0)
	{
		$d = 0;
		foreach($decks as $deck)
		{
			// Get cards in deck from database
			$sql = "SELECT UC.card_id, UC.usercard_id, CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.image, C.description, C.ranking, C.value
					FROM mytcg_usercard UC
					JOIN mytcg_card C USING(card_id)
					JOIN mytcg_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
					WHERE UC.deck_id = '".$deck['deck_id']."'
					AND UC.user_id = '".$userID."'
					ORDER BY UC.card_id, UC.usercard_id ASC";
			$deckcards = myqu($sql);
			$deckranking = 0;
			$deckvalue = 0;
			
			echo $sTab.$sTab.'<deck_'.$d.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<index>'.$d.'</index>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<deckid>'.$deck['deck_id'].'</deckid>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description>'.$deck['description'].'</description>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<categoryid>'.$deck['category_id'].'</categoryid>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<type>'.$deck['type'].'</type>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<image>'.$deck['path'].'decks/'.$deck['image'].'.png'.'</image>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<imageid>'.$deck['image'].'</imageid>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<cardcount val="'.count($deckcards).'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<cards>'.$sCRLF;
			if(count($deckcards) > 0)
			{
				$c = 0;
				foreach($deckcards as $card)
				{
					$sql = "SELECT CS.description AS 'stattext', CS.statvalue, CCS.description AS 'category'
							FROM mytcg_cardstat CS
							JOIN mytcg_categorystat CCS USING (categorystat_id)
							WHERE CS.card_id = ".$card['card_id'].";";
					$cardstats = myqu($sql);
					//print_r($cardstats);
					
					echo $sTab.$sTab.$sTab.$sTab.'<card_'.$c.'>'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<cardid val="'.$card['card_id'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<usercardid val="'.$card['usercard_id'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<description val="'.$card['description'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<thumbnail val="'.$card['thumbnail'].'" />'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'<stats>'.$sCRLF;
					if(count($cardstats) > 0)
					{
						$s = 0;
						foreach($cardstats as $stat)
						{
							echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<stat_'.$s.'>'.$sCRLF;
							echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<description val="'.$stat['stattext'].'" />'.$sCRLF;
							echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<statvalue val="'.$stat['statvalue'].'" />'.$sCRLF;
							echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'<category val="'.$stat['category'].'" />'.$sCRLF;
							echo $sTab.$sTab.$sTab.$sTab.$sTab.$sTab.'</stat_'.$s.'>'.$sCRLF;
							$s++;
						}
					}
					echo $sTab.$sTab.$sTab.$sTab.$sTab.'</stats>'.$sCRLF;
					echo $sTab.$sTab.$sTab.$sTab.'</card_'.$c.'>'.$sCRLF;
					$deckranking+= intval($card['ranking']);
					$deckvalue+= intval($card['value']);
					$c++;
				}
			}
			echo $sTab.$sTab.$sTab.'</cards>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<ranking>'.$deckranking.'</ranking>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<value>'.$deckvalue.'</value>'.$sCRLF;
			echo $sTab.$sTab.'</deck_'.$d.'>'.$sCRLF;
			$d++;
		}
	}
	echo $sTab.'</decks>'.$sCRLF;
	$sql = "SELECT SUM(ranking) AS 'deckranking' FROM
			(
				SELECT C.card_id, UC.usercard_id, C.description, C.ranking 
				FROM mytcg_usercard UC
				JOIN mytcg_card C USING (card_id)
				WHERE UC.user_id=".$userID."
				GROUP BY C.card_id
				ORDER BY C.ranking DESC
				LIMIT 10
			) tmp";
	$top10 = myqu($sql);
	echo $sTab.'<top10 val="'.$top10[0]['deckranking'].'" />'.$sCRLF;
	echo '</init>';
}

if (isset($_GET['deck']) == 1){
	$iDeckID = $_GET['deck'];
	$query = "SELECT CD.active, D.category_id
			  FROM mytcg_deck D
			  INNER JOIN mytcg_competitiondeck CD ON (D.competitiondeck_id = CD.competitiondeck_id)
			  WHERE D.deck_id = ".$iDeckID;
	$response = myqu($query);
	$active = $response[0]['active'];
	$category_id = $response[0]['category_id'];
	
	$sql = "SELECT P.description, P.position_id
						FROM mytcg_deck D
						INNER JOIN mytcg_competitiondeck CD ON (D.competitiondeck_id = CD.competitiondeck_id) 
						INNER JOIN mytcg_position P ON (P.type = CD.type)
						WHERE deck_id = ".$iDeckID;
				$positions = myqu($sql);
				for($i=0;$i<sizeof($positions);$i++){
					$card = hasCard($positions[$i]['position_id'],$iDeckID);
					if(sizeof($card) > 0){
						echo("<div id='{$positions[$i]['position_id']}' alt='{$positions[$i]['description']}' class='deckcardholder'><img id='{$card['card_id']}' alt='deck' src='{$card['thumbnail']}' border=0 /></div>");
					}else{
						echo("<div id='{$positions[$i]['position_id']}' alt='{$positions[$i]['description']}' class='deckcardholder'>{$positions[$i]['description']}</div>");
					}
					if(sizeof($cards[$i]) > 0){
						$card = $cards[$i];
					}
				}

}
function hasCard($positionID,$deckID){
	$sql = "SELECT CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.description, C.image, C.card_id
			FROM mytcg_deckcard DC 
			INNER JOIN mytcg_card C ON (DC.card_id = C.card_id)
			JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
			WHERE DC.deck_id = ".$deckID."
			AND DC.position_id = ".$positionID;
	$card = myqu($sql);
	return $card[0];
}

/*
function getChildrenIds($id)
{
	$records = getChildren($id);
	$children = array();
	$children[] = $id;
	if(count($records) > 0)
	{
		foreach($records as $record)
		{
			$children[] = $record['category_id'];
		}
	}
	return $children;
}
*/
if(isset($_GET['cards']))
{
	$category_id = $_GET['category'];
	
	if($category_id==75){
		$category_id = "76,77";
	}
	
	$sql = "SELECT UC.card_id, COUNT(UC.card_id) AS 'avail', CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.description, C.image
					FROM mytcg_usercard UC
					JOIN mytcg_card C USING(card_id)
					JOIN mytcg_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
					WHERE UC.usercardstatus_id = 1
					AND UC.user_id = ".$userID."
					AND C.category_id IN ({$category_id}) 
					GROUP BY UC.card_id
					ORDER BY C.description ASC";
	$cards = myqu($sql);

	// Return XML
	echo '<init>'.$sCRLF;
	echo $sTab.'<cardcount val="'.count($cards).'" />'.$sCRLF;
	echo $sTab.'<cards>'.$sCRLF;
	if(count($cards) > 0)
	{
		$i = 0;
		foreach($cards as $card)
		{
			echo $sTab.$sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<cardid>'.$card['card_id'].'</cardid>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description>'.$card['description'].'</description>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<avail>'.$card['avail'].'</avail>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<ranking>'.$card['ranking'].'</ranking>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<value>'.$card['value'].'</value>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<image>'.$card['image'].'</image>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<thumbnail>'.$card['thumbnail'].'</thumbnail>'.$sCRLF;
			echo $sTab.$sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo $sTab.'</cards>'.$sCRLF;
	echo '</init>';

}

if(isset($_GET['add']))
{
	$iDeckID=$_GET['deckid'];
	$iCardID=$_GET['cardid'];
	$iPositionID=$_GET['position'];
	
	$cardQuery = myqu('SELECT usercard_id 
		FROM mytcg_usercard 
		WHERE user_id = '.$userID.' 
		AND card_id = '.$iCardID.' 
		AND deck_id IS NULL 
		AND usercardstatus_id = 1 
		LIMIT 1');
	// echo ($iDeckID.' dID+ '.$iCardID.' cID+ '.$iPositionID.' pID+ '.sizeof($cardQuery));
	// exit;
	if(count($cardQuery) > 0)
	{
		$usercard_id = $cardQuery[0]['usercard_id'];

		$query = "INSERT INTO mytcg_deckcard(
					card_id,
					position_id,
					usercard_id,
					deck_id
					)
					VALUES(
					{$iCardID},
					{$iPositionID},
					{$usercard_id},
					{$iDeckID}
				)";
		$res = myqu($query);
		
		// $sql = "UPDATE mytcg_usercard 
				// SET
					// deck_id = ".$deck_id.",
					// usercardstatus_id = 1 
				// WHERE usercard_id = ".$usercard_id."
				// AND user_id = ".$userID;
		// myqu($sql);
		echo $usercard_id;
	}
	else
	{
		echo '0';
	}

}

/*
if(isset($_GET['remove']))
{
	$usercard_id = $_GET['id'];
	
	$sql = "UPDATE ".$pre."_usercard UC 
			SET UC.deck_id = NULL
			WHERE UC.usercard_id = ".$usercard_id."
			AND UC.user_id = ".$userID.";";
	myqu($sql);
	
	$sql = "SELECT deck_id FROM ".$pre."_usercard UC WHERE UC.usercard_id = ".$usercard_id.";";
	$deck = myqu($sql);
	if(is_null($deck[0][0]))
	{
		echo '1';
	}
	else
	{
		echo '0';
	}
}


function getChildren($id)
{
	//get children of id
	$sql = "SELECT category_id, description
			FROM mytcg_category
			WHERE parent_id = '$id';";
	return myqu($sql);
}
*/

if(isset($_GET['load']))
{
	$deckId = $_GET['deck'];
	
	$sql = "SELECT * FROM ".$pre."_deck WHERE deck_id=".$deckId." AND user_id=".$userID;
	$deckData = myqu($sql);
	
	//return XML data
	echo '<deck>'.$sCRLF;
	if(sizeof($deckData) > 0){
		$deck = $deckData[0];
		$sql = "SELECT * FROM ".$pre."_usercard WHERE deck_id=".$deckId." AND user_id=".$userID;
		echo $sTab.'<deck_id val="'.$deck['deck_id'].'" />'.$sCRLF;
		echo $sTab.'<description val="'.$deck['description'].'" />'.$sCRLF;
		echo $sTab.'<category_id val="'.$deck['category_id'].'" />'.$sCRLF;
		echo $sTab.'<cards>'.$sCRLF;
		echo $sTab.'</cards>'.$sCRLF;
	}
	echo '</deck>';
}


if(isset($_GET['save']))
{
	
	$deckID = $_GET['deck'];
	$sString = substr($_GET['list'], 0, -1);
	
	$query = "DELETE FROM mytcg_deckcard WHERE deck_id = ".$deckID;
	$res = myqu($query);
	
	$aList = explode("@",$sString);
	for($i=0;$i < sizeof($aList);$i++){
		$aSplit = explode("||",$aList[$i]);
		$pos = $aSplit[0];
		$card_id =  $aSplit[1];
		
		$query = "INSERT INTO mytcg_deckcard (card_id,position_id,deck_id) VALUES ({$card_id},{$pos},{$deckID})";
		$res = myqu($query);
	}
	exit;
}


if(isset($_GET['update']))
{
	if($_GET['update']=='0')
	{
		//insert new deck
		
		//$deckCategory = $_GET['category'];
	}
	elseif($_GET['update']=='1')
	{
		//update existing deck
		$deckId = $_GET['deck'];
		$deckDescription = addslashes($_GET['description']);
		$deckImage = $_GET['image'];
		$deckCards = $_GET['cards'];
		$updateDeck = $_GET['updatedeck'];
		$updateDeckCards = $_GET['updatedeckcards'];
		
		if($updateDeck == '1'){
			//update deck details
			$sql = "UPDATE mytcg_deck SET 
						description='{$deckDescription}',
						image={$deckImage}
					WHERE user_id={$userID}
					AND deck_id={$deckId}
					";
			myqu($sql);//echo $sql.$sCRLF;
		}
		
		if($updateDeckCards == '1'){
			//remove all cards from deck
			$sql = "UPDATE mytcg_usercard SET deck_id=NULL WHERE deck_id={$deckId} AND user_id={$userID}";
			myqu($sql);//echo $sql.$sCRLF;
			
			//get usercard_id's of selected cards to deck
			$sql = "SELECT GROUP_CONCAT(usercard_id) usercards FROM (
						SELECT * FROM (
							SELECT *
							FROM mytcg_usercard
							WHERE user_id={$userID}
							AND card_id IN ({$deckCards})
							AND usercardstatus_id=1
							AND deck_id IS NULL
							ORDER BY usercard_id ASC
						) tmp1
						GROUP BY card_id
					) tmp2
					";
			//echo $sql.$sCRLF;
			$deckCards = myqu($sql);
			$deckCards = $deckCards[0]['usercards'];
			$deckCards = (!is_null($deckCards)) ? $deckCards : '0';
			
			//add selected cards to deck
			$sql = "UPDATE {$pre}_usercard SET deck_id={$deckId} WHERE user_id={$userID} AND usercard_id IN ($deckCards)";
			myqu($sql);//echo $sql.$sCRLF;
		}
		
		//return XML data
		echo '<update>'.$sCRLF;
			echo $sTab.'<result val="1" />'.$sCRLF;
			echo $sTab.'<description val="'.$deckDescription.'" />'.$sCRLF;
			echo $sTab.'<category_id val="'.$deckCategory.'" />'.$sCRLF;
			echo $sTab.'<cards val="'.$deckCards.'" />'.$sCRLF;
		echo '</update>';
		exit;
	}
/*	
	//check that deck name (description) does not exist
	$sql = "SELECT COUNT(deck_id) AS 'total' FROM ".$pre."_deck 
			WHERE description = \"".$_GET['deckname']."\"
			AND deck_id != '$deck_id'
			AND user_id = '$userID';";
	$found = myqu($sql);
	$found = $found[0]['total'];
	if($found == '0')
	{
		$sql = "UPDATE ".$pre."_deck
				SET
					category_id=$category_id,
					description='$description', 
					image='$image'
				WHERE deck_id = '$deck_id'
				AND user_id = '$userID';";
		myqu($sql);
		echo '1';
	}
	else
	{
		echo 'Deck name \''.$_GET['deckname'].'\' already exists. Please enter a different name.';
	}
 * 
 */
}


if(isset($_GET['delete']))
{
	$deck_id = $_GET['deck_id'];
	
	$sql = "UPDATE ".$pre."_usercard
			SET deck_id = NULL
			WHERE deck_id = '$deck_id'
			AND user_id = '$userID';";
	myqu($sql);
	$sql = "DELETE FROM ".$pre."_deck 
			WHERE deck_id = '$deck_id'
			AND user_id='$userID';";
	myqu($sql);
	
	echo '1';
}

?>
