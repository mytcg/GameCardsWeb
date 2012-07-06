<?php
	//database connection to local host
	$db['host'] = "localhost";
	$db['database'] = "sarugmhnuf_db1";
	$db['username'] = "root";
	$db['password'] = "";
	$db['pre'] = "mytcg";

	
	//database connection to live site 
	// $db['host'] = "sarugbycards.com";
	// $db['database'] = "sarugmhnuf_db1";
	// $db['username'] = "sarugmhnuf_1";
	// $db['password'] = "j2gcuH88";
	// $db['pre'] = "mytcg"; 
	
	session_start();
	//Var short
	$sCRLF="\r\n";
	$sTab=chr(9);
	
	//Validating User
	if ($_REQUEST['username']){
		$sql = "SELECT user_id, username, password FROM mytcg_user WHERE username='{$_REQUEST['username']}' LIMIT 1";
		$aUser = myqu($sql);
		if($aUser){
			$userID = $aUser[0]["user_id"];
			$userPassword = $aUser[0]["password"];
			$iMod=(intval($userID) % 10)+1;
			$requestPassword=substr(md5($userID),$iMod,10).md5($_REQUEST['password']);
			if($userPassword == $requestPassword){
				$_SESSION['userID']=$userID;
				$_SESSION['username']=$aUser[0]['username'];
			}
		}
	}

	 // $_SESSION['userID'] = '92';
 	 // $_SESSION['username'] = 'Senjiro';
	 // $user['user_id'] = $_SESSION['userID'];
	 // $user['username'] = $_SESSION['username'];
	
	 // $_SESSION['userID'] = '2';
 	 // $_SESSION['username'] = 'cole@mytcg.net';
	 $user['user_id'] = $_SESSION['userID'];
	 $user['username'] = $_SESSION['username'];
?>