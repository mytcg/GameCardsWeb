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
  
  $sql = "SELECT D.deck_id, D.category_id, CAT.description AS 'category', D.description, D.image, CONCAT(I.description,'decks/',D.image,'.png') AS 'imageurl'
      FROM ".$pre."_deck D
      JOIN ".$pre."_category CAT USING(category_id)
      JOIN ".$pre."_imageserver I ON I.imageserver_id = D.imageserver_id
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
      $sql = "SELECT UC.card_id, UC.usercard_id, CONCAT(I.description,'cards/',C.image,'_web.png') AS 'thumbnail', C.image, C.description, C.ranking
          FROM ".$pre."_usercard UC
          JOIN ".$pre."_card C USING(card_id)
          JOIN ".$pre."_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
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
	// Get decks from database
	$sql = "SELECT D.deck_id, D.category_id, CAT.description AS 'category', D.description, D.image, CONCAT(I.description,'decks/',D.image,'.png') AS 'imageurl'
			FROM ".$pre."_deck D
			JOIN ".$pre."_category CAT USING(category_id)
			JOIN ".$pre."_imageserver I ON I.imageserver_id = D.imageserver_id
			WHERE D.user_id = '".$userID."'
			ORDER BY D.description ASC;";
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
			$sql = "SELECT UC.card_id, UC.usercard_id, CONCAT(I.description,'cards/',C.image,'_web.png') AS 'thumbnail', C.image, C.description, C.ranking, C.value
					FROM ".$pre."_usercard UC
					JOIN ".$pre."_card C USING(card_id)
					JOIN ".$pre."_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
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

if(isset($_GET['cards']))
{
	$sql = "SELECT UC.card_id, COUNT(UC.card_id) AS 'avail', CONCAT(I.description,'cards/',C.image,'_web.png') AS 'thumbnail', C.description, C.image
			FROM ".$pre."_usercard UC 
			JOIN ".$pre."_card C USING(card_id)
			JOIN ".$pre."_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
			WHERE UC.deck_id IS NULL
			AND UC.usercardstatus_id = 1
			AND C.category_id IN(".implode(",",getChildrenIds($_GET['category_id'])).")
			AND UC.user_id = ".$userID."
			GROUP BY UC.card_id
			ORDER BY UC.card_id ASC";
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
			echo $sTab.'<card_'.$i.'>'.$sCRLF;
			echo $sTab.$sTab.'<cardid>'.$card['card_id'].'</cardid>'.$sCRLF;
			echo $sTab.$sTab.'<avail>'.$card['avail'].'</avail>'.$sCRLF;
			echo $sTab.$sTab.'<description>'.$card['description'].'</description>'.$sCRLF;
			echo $sTab.$sTab.'<image>'.$card['image'].'</image>'.$sCRLF;
			echo $sTab.$sTab.'<thumbnail>'.$card['thumbnail'].'</thumbnail>'.$sCRLF;
			echo $sTab.'</card_'.$i.'>'.$sCRLF;
			$i++;
		}
	}
	echo $sTab.'</cards>'.$sCRLF;
	echo '</init>';
}


if(isset($_GET['add']))
{
	$deck_id = $_GET['deckid'];
	$card_id = $_GET['cardid'];
	
	$sql = "SELECT usercard_id 
			FROM ".$pre."_usercard UC
			WHERE UC.card_id = '".$card_id."'
			AND UC.deck_id IS NULL AND UC.usercardstatus_id=1
			AND UC.user_id = ".$userID."
			ORDER BY UC.usercard_id ASC
			LIMIT 1;";
	$usercard_id = myqu($sql);
	
	if(count($usercard_id) > 0)
	{
		$usercard_id = $usercard_id[0][0];
		
		$sql = "UPDATE ".$pre."_usercard 
				SET
					deck_id = ".$deck_id.",
					usercardstatus_id = 1 
				WHERE usercard_id = ".$usercard_id."
				AND user_id = ".$userID;
		myqu($sql);
		echo $usercard_id;
	}
	else
	{
		echo '0';
	}
}


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

/*
if(isset($_GET['save']))
{
	$description = addslashes($_GET['deckname']);
	$category_id = $_GET['deckcategory'];
	$image = $_GET['deckimage'];
	
	//check that deck name (description) does not exist
	$sql = "SELECT COUNT(deck_id) AS 'total' FROM ".$pre."_deck 
			WHERE description = \"".$_GET['deckname']."\"
			AND user_id = '$userID';";
	$found = myqu($sql);
	$found = $found[0]['total'];
	if($found == '0')
	{
		$sql = "INSERT INTO ".$pre."_deck (
					user_id, 
					category_id, 
					imageserver_id, 
					description, 
					image
				) VALUES (
					$userID,
					$category_id,
					1,
					'$description',
					'$image'
				);";
		myqu($sql);
		echo '1';
	}
	else
	{
		echo 'Deck name \''.$_GET['deckname'].'\' already exists. Please enter a different name.';
	}
}
*/

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
			$sql = "UPDATE {$pre}_deck SET 
						description='{$deckDescription}',
						image={$deckImage}
					WHERE user_id={$userID}
					AND deck_id={$deckId}
					";
			myqu($sql);//echo $sql.$sCRLF;
		}
		
		if($updateDeckCards == '1'){
			//remove all cards from deck
			$sql = "UPDATE {$pre}_usercard SET deck_id=NULL WHERE deck_id={$deckId} AND user_id={$userID}";
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
