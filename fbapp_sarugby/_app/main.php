<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");
require_once("../facebooksdk/facebook.php");

//INIT FACEBOOK USER AUTH THING A MA BOB
$facebook = new Facebook(array(
  'appId'  => $fbconfig['appid'],
  'secret' => $fbconfig['secret'],
  'cookie' => true,
));
$fbuserID = $facebook->getUser();

if($_GET['login']){
  $userProfile = $_SESSION['userProfile'];
  $sUsername = $_GET["username"];
  $sPassword = $_GET["password"];
  
  $sql = "SELECT user_id,password FROM mytcg_user WHERE username='".$sUsername."'";
  $getUser = myqu($sql);
  if(!$getUser){
    echo('No valid username');
    exit;
  }
  $user_id = $getUser[0]['user_id'];
  $iMod=(intval($user_id) % 10)+1;
  $sPassword=substr(md5($user_id),$iMod,10).md5($sPassword);
  
  if($sPassword != $getUser[0]['password']){
    echo("Invalid password.");
    exit;
  }
  
  $sql = "UPDATE mytcg_user SET facebook_user_id = '".$userProfile['id']."',facebook_process = 1 WHERE user_id = ".$user_id;
  $response = myqu($sql);
  myqu("INSERT INTO tcg_user_log (user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id)
	SELECT user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id
	FROM mytcg_user WHERE user_id=".$user_id);
  echo("1");
  exit;
}

if($_GET['signup']){
  $userProfile = $_SESSION['userProfile'];
  $sEmail = $_GET["email_address"];
  $sPassword = $_GET["password"];
  $sAge = $_GET["age"];
  $sGender = $_GET["gender"];
  $sName = $_GET["name"];
  $sSurname = $_GET["surname"];
  
  
  $sql = "SELECT user_id FROM mytcg_user WHERE email_address='".$sEmail."'";
  $getUser = myqu($sql);
  if(sizeof($getUser) > 0){
    echo('Email address already in use.');
    exit;
  }
  
  $sql = "INSERT INTO mytcg_user (name,surname,date_register,username,email_address,age,gender,facebook_user_id) VALUES ('".$sName."','".$sSurname."',NOW(),'".$sEmail."','".$sEmail."',".$sAge.",".$sGender.",'".$userProfile['id']."')";
  $res = myqu($sql);
  
  $sql = "SELECT user_id FROM mytcg_user WHERE email_address='".$sEmail."'";
  $getUser = myqu($sql);
  
  $user_id = $getUser[0]['user_id'];
  
  myqu("INSERT INTO tcg_user_log (user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id)
	SELECT user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id
	FROM mytcg_user WHERE user_id=".$user_id);
  
  $iMod=(intval($user_id) % 10)+1;
  $sPassword=substr(md5($user_id),$iMod,10).md5($sPassword);
  
  $sql = "UPDATE mytcg_user SET password = '".$sPassword."' WHERE user_id = ".$user_id;
  $res = myqu($sql);
  
  $sql = "SELECT * FROM mytcg_user_detail";
  $getUser = myqu($sql);
  foreach($getUser as $u){
  	$sql = "INSERT INTO mytcg_user_answer (detail_id,answered,user_id) VALUES (".$u['detail_id'].",0,".$user_id.")";
  	$res = myqu($sql);
  }
  
  
  $sql = "INSERT INTO mytcg_frienddetail (user_id, friend_id) values (".$user_id.",".$user_id.")";
	myqu($sql);
	$sql = "SELECT user_fb_id FROM mytcg_userrequest where request_user_fb_id = '".$userProfile['id']."'";
  $res = myqu($sql);
  if (sizeof($res) > 0) {
	foreach($res as $friend){
		$sql = "INSERT INTO mytcg_frienddetail (user_id, friend_id) values ((SELECT user_id from mytcg_user where facebook_user_id = '".$friend['user_fb_id']."'),".$user_id.")";
		myqu($sql);
		$sql = "INSERT INTO mytcg_frienddetail (user_id, friend_id) values (".$user_id.",(SELECT user_id from mytcg_user where facebook_user_id = '".$friend['user_fb_id']."'))";
		myqu($sql);
	}
  }
  
  echo("1");
  exit;
}

if($_GET['init']){
	$sql = "SELECT request_user_fb_id AS fbid FROM mytcg_userrequest WHERE user_fb_id = '".$fbuserID."'";
	$aList = myqu($sql);
	echo '<init>'.$sCRLF;
	echo $sTab.'<requests>'.$sCRLF;
	echo $sTab.$sTab.'<iCount>'.sizeof($aList).'</iCount>'.$sCRLF;
	$i = 0;
	foreach($aList as $item)
	{
		echo $sTab.$sTab.'<request_'.$i.'>'.$sCRLF;
		echo $sTab.$sTab.$sTab.'<fbid>'.$item['fbid'].'</fbid>'.$sCRLF;
		echo $sTab.$sTab.'</request_'.$i.'>'.$sCRLF;
		$i++;
	}
	echo $sTab.'</requests>'.$sCRLF;
	echo '</init>'.$sCRLF;
  exit;
}

if($_REQUEST['request_ids']){
	$sql = "SELECT * FROM mytcg_userrequest WHERE user_fb_id = '".$fbuserID."' AND request_user_fb_id = '".$_REQUEST['request_ids']."'";
	$res = myqu($sql);
	
	if(sizeof($res) == 0){
		$sql = "INSERT INTO mytcg_userrequest (user_fb_id,request_user_fb_id,request_status) VALUES ('".$fbuserID."','".$_REQUEST['request_ids']."',1)";
		$res = myqu($sql);
	}
	$sql = "SELECT user_id FROM mytcg_user WHERE facebook_user_id = '".$_REQUEST['request_ids']."'";
	$res = myqu($sql);
	if (sizeof($res) > 0) {
		$sql = "INSERT INTO mytcg_frienddetail (user_id, friend_id) values ((SELECT user_id from mytcg_user where facebook_user_id = '".$fbuserID."'),".$res[0]['user_id'].")";
		myqu($sql);
		$sql = "INSERT INTO mytcg_frienddetail (user_id, friend_id) values (".$res[0]['user_id'].",(SELECT user_id from mytcg_user where facebook_user_id = '".$fbuserID."'))";
		myqu($sql);
	}
}


?>