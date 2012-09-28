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
  $sEmail=str_replace(" ","",$_GET["email"]);
  $sUsername=$sEmail;
  $sPassword=$_GET["password"];
  $sName=$_GET["name"];
  $sSurname=$_GET["surname"];
  $sAge=$_GET["age"];
  $sGender=$_GET["gender"];
  
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
		$failMessage.= 'Email already registered.';
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
    $aUserInsert=myqu(
      "INSERT INTO mytcg_user (username, email_address,name,surname,age,gender,category_id, is_active, date_register, premium, xp, completion_process_stage, credits, web_date_last_visit, last_useragent) "
      ."VALUES ('".$sUsername."', '".$sEmail."', '".$sName."', '".$sSurname."', '".$sAge."', '".$sGender."','".$iCat."',1,now(),0,0,2,0,now(),'".$_SERVER["HTTP_USER_AGENT"]."')"
    );
    $aUserID=myqu(
      "SELECT user_id FROM "
      .$pre."_user "
      ."WHERE username='".$sUsername."' "
    );
    $iUserID=$aUserID[0]["user_id"];
	
	myqu("INSERT INTO tcg_user_log (user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id)
			SELECT user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id
			FROM mytcg_user WHERE user_id=".$iUserID);
	
  $sql = "SELECT * FROM mytcg_user_detail";
  $getUser = myqu($sql);
  
  //create empty achievements
		myqu("INSERT INTO mytcg_userachievementlevel 
			(user_id, achievementlevel_id)
			SELECT {$iUserID}, id
			FROM mytcg_achievementlevel");
  
  foreach($getUser as $u){
  	$sql = "INSERT INTO mytcg_user_answer (detail_id,answered,user_id) VALUES (".$u['detail_id'].",0,".$iUserID.")";
  	$res = myqu($sql);
  }
  
  $sql = "INSERT INTO mytcg_frienddetail (user_id, friend_id) values (".$user_id.",".$iUserID.")";
	myqu($sql);
  //TODO: Add friends code
  
    $iMod=(intval($iUserID) % 10)+1;
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