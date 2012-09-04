<?php
    require_once("facebooksdk/facebook.php");
    $user = null; //facebook user ui
    
    //CREATE FACEBOOK SDK OBJECT
    $facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));
    $fbuser = $facebook->getUser(); //TRY TO GET USER DETAILS IF AUTHENTICATED
    
    if($_REQUEST['request_ids']){
    	$_SESSION['request_ids'] = $_REQUEST['request_ids'];
    }
    
	if(($_SESSION['request_ids']!="")&&($fbuser != "")){
		$request_id = $_SESSION['request_ids'];
		$full_request_id = $request_id . '_' . $fbuser;
		$sent_id = $facebook->api("/".$full_request_id."?access_token=".$facebook->getAccessToken() );
		
		$sql = "SELECT * FROM mytcg_userrequest WHERE user_fb_id = '".$sent_id['from']['id']."' AND request_user_fb_id = '".$sent_id['to']['id']."' AND request_status = 1";
		$aRequest = myqu($sql);
		$request_id = $aRequest[0]['request_id'];
		
		if($aRequest[0]['request_status'] == 1){
			$sql = "UPDATE mytcg_userrequest SET request_status = 3 WHERE request_id = ".$request_id;
			$res = myqu($sql);
			
			$sql = "SELECT * FROM mytcg_user WHERE facebook_user_id = '".$sent_id['from']['id']."'";
			$res = myqu($sql);
			if(sizeof($res) > 0){
				$sql = "SELECT * FROM mytcg_user WHERE facebook_user_id = '".$sent_id['to']['id']."'";
				$iGotIT = myqu($sql);
			}
			$sql = "UPDATE mytcg_userrequest SET request_status = 2 WHERE request_user_fb_id = '".$sent_id['to']['id']."' AND request_id != ".$request_id;
			$res = myqu($sql);
		}
		$_SESSION['request_ids'] = "";
	}
	
	if (isset($_GET['code'])){
      header("Location: " . $fbconfig['appBaseUrl']);
    }
	
    if (!$fbuser) {
		$params = array(
		  scope => 'user_birthday,email',
		  redirect_uri => $fbconfig['baseUrl']
		);

		$loginUrl = $facebook->getLoginUrl($params);
		echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
		exit;
    }else{
      $userProfile = $facebook->api('/me');
      $_SESSION['userProfile'] = $userProfile;
    }
    
    //Login user
    $user = myqu("SELECT user_id,username,credits,premium,gameswon,xp,facebook_process,date_last_visit,mobile_date_last_visit FROM mytcg_user WHERE facebook_user_id = '".$userProfile['id']."' LIMIT 1");
    $user = $user[0];
	
	if(!$user){
      header("Location: signup.php");
    }
	
	if(sizeof($_SESSION['userDetails'])==0){
		$sUA=$_SERVER["HTTP_USER_AGENT"];
		$sUA=myqu("UPDATE mytcg_user SET last_useragent='".$sUA."' WHERE user_id='".$user['user_id']."'");
		
		myqu("INSERT INTO tcg_user_log (user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id)
			SELECT user_id, name, surname, email_address, email_verified, date_register, date_last_visit, msisdn, imsi, imei, version, os, make, model, osver, touch, width, height, facebook_user_id, mobile_date_last_visit, web_date_last_visit, facebook_date_last_visit, last_useragent, ip, apps_id, age, gender, referer_id
			FROM mytcg_user WHERE user_id=".$user['user_id']);
	}
    
    $token = $facebook->getAccessToken();
    //$friends = $facebook->api('/me/friends?access_token='.$token.'&fields=id,name');
    
    $fql = 'SELECT uid,name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$userProfile['id'].') AND is_app_user = 1';
    $friends = $facebook->api(array('method' => 'fql.query','query' => $fql,));
?>
