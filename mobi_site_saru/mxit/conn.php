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
	echo ($_REQUEST['username']);
	//Validating User
	if ($_REQUEST['username']){
		$sql = "SELECT user_id, username, `password` FROM mytcg_user WHERE username='{$_REQUEST['username']}' LIMIT 1";
		$aUser = myqu($sql);
		if($aUser[0]["user_id"] != null){
			$userName = $aUser[0]["username"];
			if($userName == $_REQUEST['username']){
				$_SESSION['userID']=$aUser[0]["user_id"];;
				$_SESSION['username']=$aUser[0]['username'];
			}
		}
	}

	 $user['user_id'] = $_SESSION['userID'];
	 $user['username'] = $_SESSION['username'];
?>