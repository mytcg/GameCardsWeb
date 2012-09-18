<?php
//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
$sCRLF="\r\n";
$sTab=chr(9);

//SETUP PREFIX FOR TABLES
$pre = $Conf["database"]["table_prefix"];

if (intval($_GET["register"])==1)
{
  $sUsername=str_replace(" ","",$_GET["username"]);
  $sEmail=str_replace(" ","",$_GET["email"]);
  $sPassword=$_GET["password"];
  
  $sql = "SELECT user_id, username, email_address
			 FROM ".$pre."_user
			 WHERE LOWER(email_address)=LOWER('".$sEmail."') OR LOWER(username)=LOWER('".$sUsername."')";
  $aUserExists=myqu($sql);
  
  echo '<register>'.$sCRLF;
  if ($aUserExists){
	 $usernameExists = false;
	 $emailExists = false;
	 $failMessage = '';
	 foreach($aUserExists as $user){
		if(strtolower($sUsername) == strtolower($user['username'])){
		  $usernameExists = true;
		}
		if(strtolower($sEmail) == strtolower($user['email_address'])){
		  $emailExists = true;
		}
	 }
	 if($usernameExists && $emailExists){
		$failMessage.= 'Username and Email already registered.';
	 }
	 elseif($usernameExists){
		$failMessage.= 'Username already registered.';
	 }
	 elseif($emailExists){
		$failMessage.= 'Email already registered.';
	 }
	 else{
		$failMessage.= 'Registration failed.';
	 }
    echo $sTab.'<action val="fail" />'.$sCRLF;
    echo $sTab.'<message val="'.$failMessage.'" />'.$sCRLF;
  }else{
    echo $sTab.'<action val="success" />'.$sCRLF;
    $sDate=date("Y-m-d H:i:s");
    $aUserInsert=myqu(
      "INSERT INTO ".$pre."_user (username, email_address, is_active, date_register, credits, xp, completion_process_stage) "
      ."VALUES ('".$sUsername."', '".$sEmail."',1,'".$sDate."',300,0,2)"
    );
    $aUserID=myqu(
      "SELECT user_id FROM "
      .$pre."_user "
      ."WHERE username='".$sUsername."' "
    );
    $iUserID=intval($aUserID[0]["user_id"]);
	
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (1, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (2, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (3, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (4, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (5, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (6, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (7, NULL,0,".$iUserID.")");
  myqu("INSERT INTO ".$pre."_user_answer (detail_id,answer,answered,user_id) VALUES (8, '".$sEmail."',1,".$iUserID.")");
  
	//add transaction log for register credits
	myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val)
			VALUES(".$iUserID.", 'Received 300 credits for registering', NOW(), 300)");
			
    $iMod=($iUserID % 10)+1;
    $sSalt=substr(md5($iUserID),$iMod,10);
    $aSaltPassword=myqu(
      "UPDATE "
      .$pre."_user "
      ."SET password='".$sSalt.md5($sPassword)."' "
      ."WHERE user_id='".$iUserID."'"
    );
    echo $sTab.'<message val="Registration Complete" />'.$sCRLF;
  }
  echo '</register>'.$sCRLF;
  
  $_COOKIE['registered'] = 1;
  
  exit;
}
?>