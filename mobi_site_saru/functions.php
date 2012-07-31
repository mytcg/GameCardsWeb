<?php
function getUserData($prefix, $userId='')
{
	$userId = ($userId == '') ? $_SESSION['user']['id'] : $userId; 
	$sql = "SELECT user_id, username, password, date_last_visit, mobile_date_last_visit , (ifnull(credits,0)+ifnull(premium,0)) credits,credits freemium, premium, xp, freebie, completion_process_stage "
		."FROM ".$prefix."_user "
		."WHERE user_id='".$userId."' "
		."AND is_active='1'";
	return myqu($sql);
}
// function myqu($sQuery) {
	// $conn = new dbconnection();
	// return $conn->_myqu($sQuery);
// }
function myqui($sQuery) {
	$conn = new dbconnection();
	$conn->_myqui($sQuery);
}

function resizeCard($iHeight, $iWidth, $iImage, $root, $iBBHeight=0, $jpg=0) {

	//we need to check if the width after scaling would be too wide for the screen.
	$ext = '.png';
	$image_type=IMAGETYPE_PNG;
	
	if ($jpg) {
		$ext = '.jpg';
		$image_type=IMAGETYPE_JPEG;
	}
	
	$filename = $root.'img/cards/'.$iImage.'_front'.$ext;
	if (file_exists($filename)) {
		$image = new Upload($filename);
		$ratio = $iHeight / $image->image_src_y;
		if (($ratio * ($image->image_src_x)) > $iWidth) {
			$ratio = $iWidth / $image->image_src_x;
			$iHeight =  intval($ratio * $image->image_src_y);
		}
	}
	else {
		die('File does not exist -> '.$filename);
	}
	
	//we want a maximum image size, so larger devices dont have to download huge images
	if ($iHeight > 520) {
		//for now, max = 520
		$iHeight = 520;
	}
	
	if ($iWidth > 520) {
		//for now, max = 520
		$iWidth = 520;
	}
	
	//Check directory for resized version
	chmod($root.'img',0777);
	$dir = $root.'img/'.$iHeight;
	if (!is_dir($dir)){
		if (!mkdir($dir, 0777, true)) {
			die('Failed to create folders -> '.$dir);
		}
	}
	$dir .= "/cards";
	if ($iBBHeight) {
		$dir .= "bb";
	}
	if (!is_dir($dir)){
		if (!mkdir($dir, 0777, true)) {
			die('Failed to create folders -> '.$dir);
		}
	}
	$dir .= "/";
	
	$iRotateWidth = ($iWidth-40<=0)?$iWidth:$iWidth-40;
	$iRotateHeight = ($iHeight-40<=0)?$iHeight:$iHeight-40;
	$iBBRotateHeight =  ($iBBHeight-20<=0)?$iBBHeight:$iBBHeight-20;
	
	//Check and create new resized front image
	$filenameResized = $dir.$iImage.'_front'.$ext;
	if((!file_exists($filenameResized)) && (file_exists($filename))){
		$image = new Upload($filename);
		$image->image_resize = true;
		$image->image_ratio_x = true;
		$image->image_y = $iHeight;
		$image->Process($dir);
	}
	
	$filename = $root.'img/cards/'.$iImage.'_front'.$ext;
	$filenameResized = $dir.$iImage.'_front_flip'.$ext;
	if((!file_exists($filenameResized)) && (file_exists($filename))){
		$image = new Upload($filename);
		$image->image_resize = true;
		$image->file_new_name_body = $iImage.'_front_flip';
		if ($iBBHeight) {
			$ratio = $iRotateWidth / $image->image_src_y;
			$cardwidth = $image->image_src_x * $ratio;
			if ($iBBRotateHeight/2 < $cardwidth) {
				$cardwidth = $iBBRotateHeight/2;
				$ratio = $cardwidth / $image->image_src_x;
				$iRotateWidth = $image->image_src_y * $ratio;
			}
			$image->image_x = $cardwidth;
			$image->image_y = $iRotateWidth;
			
			$image->image_rotate = '90';
		} else {
			$ratio = $iRotateWidth / $image->image_src_y;
			$cardwidth = $image->image_src_x * $ratio;
			if ($iRotateHeight/2 < $cardwidth) {
				$cardwidth = $iRotateHeight/2;
				$ratio = $cardwidth / $image->image_src_x;
				$iRotateWidth = $image->image_src_y * $ratio;
			}
			$image->image_x = $cardwidth;
			$image->image_y = $iRotateWidth;
			
			$image->image_rotate = '90';
		}
		$image->Process($dir);
	}
	
	//Check and create new resized back image
	$filename = $root.'img/cards/'.$iImage.'_back'.$ext;
	$filenameResized = $dir.$iImage.'_back'.$ext;
	if((!file_exists($filenameResized)) && (file_exists($filename))){
		$image = new Upload($filename);
		$image->image_resize = true;
		$image->image_ratio_x = true;
		$image->image_y = $iHeight;
		$image->Process($dir);
	}
	
	$filename = $root.'img/cards/'.$iImage.'_back'.$ext;
	$filenameResized = $dir.$iImage.'_back_flip'.$ext;
	if((!file_exists($filenameResized)) && (file_exists($filename))){
		$image = new Upload($filename);
		$image->image_resize = true;
		$image->file_new_name_body = $iImage.'_back_flip';
		if ($iBBHeight) {
			$ratio = $iRotateWidth / $image->image_src_y;
			$cardwidth = $image->image_src_x * $ratio;
			if ($iBBRotateHeight/2 < $cardwidth) {
				$cardwidth = $iBBRotateHeight/2;
				$ratio = $cardwidth / $image->image_src_x;
				$iRotateWidth = $image->image_src_y * $ratio;
			}
			$image->image_x = $cardwidth;
			$image->image_y = $iRotateWidth;
			
			$image->image_rotate = '90';
		} else {
			$ratio = $iRotateWidth / $image->image_src_y;
			$cardwidth = $image->image_src_x * $ratio;
			if ($iRotateHeight/2 < $cardwidth) {
				$cardwidth = $iRotateHeight/2;
				$ratio = $cardwidth / $image->image_src_x;
				$iRotateWidth = $image->image_src_y * $ratio;
			}
			$image->image_x = $cardwidth;
			$image->image_y = $iRotateWidth;
			
			$image->image_rotate = '90';
		}
		$image->Process($dir);
	}
	
	//we need to resize the gc image for this size, if it hasnt been done yet.
	$filename = $root.'img/cards/gc'.$ext;
	$filenameResized = $dir.'gc'.$ext;
	if((!file_exists($filenameResized)) && (file_exists($filename))){
		$image = new Upload($filename);
		$image->image_resize = true;
		$image->image_ratio_x = true;
		$image->image_y = $iHeight - 60;
		$image->Process($dir);
	}
	
	$filename = $root.'img/cards/gc'.$ext;
	$filenameResized = $dir.'gcFlip'.$ext;
	if((!file_exists($filenameResized)) && (file_exists($filename))){
		$image = new Upload($filename);
		$image->image_resize = true;
		$image->file_new_name_body = 'gcFlip';
		if ($iBBHeight) {
			$ratio = $iRotateWidth / $image->image_src_y;
			$cardwidth = $image->image_src_x * $ratio;
			if ($iBBRotateHeight/2 < $cardwidth) {
				$cardwidth = $iBBRotateHeight/2;
				$ratio = $cardwidth / $image->image_src_x;
				$iRotateWidth = $image->image_src_y * $ratio;
			}
			$image->image_x = $cardwidth;
			$image->image_y = $iRotateWidth;
			
			$image->image_rotate = '90';
		} else {
			$ratio = $iRotateWidth / $image->image_src_y;
			$cardwidth = $image->image_src_x * $ratio;
			if ($iRotateHeight/2 < $cardwidth) {
				$cardwidth = $iRotateHeight/2;
				$ratio = $cardwidth / $image->image_src_x;
				$iRotateWidth = $image->image_src_y * $ratio;
			}
			$image->image_x = $cardwidth;
			$image->image_y = $iRotateWidth;
			
			$image->image_rotate = '90';
		}
		$image->Process($dir);
	}
	
	return $iHeight;
}
function saveProfileDetail($iAnswerID, $iAnswer, $iUserID) {

	$aAnswered=myqu('SELECT answered
					FROM mytcg_user_answer 
					WHERE answer_id='.$iAnswerID);
										
	$aCredits=myqu('SELECT credit_value, description
					FROM mytcg_user_detail 
					WHERE detail_id = (SELECT detail_id
										FROM mytcg_user_answer
										WHERE answer_id='.$iAnswerID.')');
	$aCredit=$aCredits[0];
	$aAnswer=$aAnswered[0];
	if ($aAnswer['answered'] == 0) {
		myqu('INSERT INTO mytcg_transactionlog (user_id, description, date, val)
				VALUES ('.$iUserID.', "Received '.$aCredit['credit_value'].' credits for answering '.$aCredit['description'].'", now(), '.$aCredit['credit_value'].')');
		
		myqu('UPDATE mytcg_user SET credits = credits + '.$aCredit['credit_value'].' WHERE user_id ='.$iUserID);
				
		$aCount=myqu('SELECT answer_id
					FROM mytcg_user_answer 
					WHERE answered=0
					AND user_id='.$iUserID);
		
		$iSize = sizeof($aCount);
		if ($iSize==1){
			myqu('INSERT INTO mytcg_transactionlog (user_id, description, date, val)
					SELECT '.$iUserID.', descript, now(), val
					FROM mytcg_transactiondescription
					WHERE transactionid = 5');	
					
			myqu('UPDATE mytcg_user SET credits = credits + IFNULL((SELECT val FROM mytcg_transactiondescription WHERE transactionid = 5),0) WHERE user_id ='.$iUserID);
		}
	}
	
	myqu('UPDATE mytcg_user_answer 
			SET answer = "'.$iAnswer.'", 
			answered = 1 
			WHERE answer_id = "'.$iAnswerID.'"');
		
	myqu('update mytcg_user
			set credits = credits + IFNULL((select credit_value 
											from mytcg_user_detail 
											where detail_id = (select detail_id
																from mytcg_user_answer
																where answer_id='.$iAnswerID.'
																AND answered = 0)), 0)
			where user_id = '.$iUserID);
	
	echo "1";

	exit;
}
function createDeck($iUserID,$iCategoryID,$iDescription) {
	myqu('INSERT INTO mytcg_deck (user_id, category_id, description) 
		VALUES('.$iUserID.','.$iCategoryID.',"'.$iDescription.'")');
		
	$deckIdQuery = myqu('SELECT max(deck_id) deck_id 
		FROM mytcg_deck 
		WHERE user_id = '.$iUserID.' 
		AND category_id = '.$iCategoryID.' 
		AND description = "'.$iDescription.'"');
	$deckId = $deckIdQuery[0]['deck_id'];
	$returnString = '<created><deck_id>'.$deckId.'</deck_id><result>Deck Created!</result></created>';
	
	return $returnString;
}
function getCardInAlbumCount($userID,$catID = 0)
{
  $add = ($catID == 0)? "" : " WHERE C.category_id = ".$catID;
  
  //Get all count
  $sql = "SELECT COUNT(card_id) AS iNr
         FROM mytcg_card AS C".$add;
  $r = myqu($sql);
  $totals[1] = $r[0]['iNr'];
  
  //Get owned count
  $sql = "SELECT DISTINCT UC.card_id
          FROM mytcg_usercard UC
          INNER JOIN mytcg_card C ON UC.card_id = C.card_id
          ".$add." AND UC.user_id = ".$userID;
  $r = myqu($sql);
  $totals[0] = sizeof($r);
  return $totals;
}

function getCardOwnedCount($cardID,$userID)
{
 $sql = "SELECT COUNT(card_id) AS iNr
          FROM mytcg_usercard UC
          INNER JOIN mytcg_usercardstatus UCS ON UCS.usercardstatus_id = UC.usercardstatus_id
          WHERE UC.user_id = ".$userID." AND UC.card_id = ".$cardID." AND UCS.description = 'Album'";
  $r = myqu($sql);
  return $r[0]['iNr'];
}

function getPath($param){
	$path = "";
	$sql = "SELECT value FROM mytcg_system WHERE parameter='{$param}' LIMIT 1";
	$res = mysql_query($sql) or die ("Error logging in.<br>".$sql);
	if (mysql_num_rows($res) > 0){
		$row = mysql_fetch_assoc($res);
		$path = $row['value'];
	}
	return $path;
}

function myqu($sQuery){
	global $db;
  	$aOutput=array();
  	$pattern = '/INSERT/i';
  
	$aLink=mysqli_connect($db["host"],$db["username"],$db["password"],$db["database"]);
	$sQuery=str_replace("&nbsp;","",$sQuery);
  
	if($aResult=@mysqli_query($aLink, $sQuery))
	{
		//If insert - return last insert id
		if(preg_match($pattern, $sQuery)){
			$mp = mysqli_insert_id($aLink);
			@mysqli_free_result($aResult);
      		mysqli_close($aLink);
			return $mp;
		}
    //Else build return array
		while ($aRow=@mysqli_fetch_array($aResult,MYSQL_BOTH)){
			$aOutput[]=$aRow;
		}
		return $aOutput;
	}
  else{
    die("[".date("Y-m-d H:i:s")."] Err:".mysqli_errno($aLink)." - ".mysqli_error($aLink)." - ".$_SERVER['PHP_SELF']." - ".$sQuery);
    @mysqli_free_result($aResult);
    mysqli_close($aLink);
  }
}
?>