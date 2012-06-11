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
		  scope => 'publish_stream',
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
    $user = myqu("SELECT user_id,username,premium,gameswon,xp,facebook_process,date_last_visit,mobile_date_last_visit FROM mytcg_user WHERE facebook_user_id = '".$userProfile['id']."' LIMIT 1");
    $user = $user[0];
	
	if(!$user){
      header("Location: signup.php");
    }
	
	//update last visit
	$sDate=date("Y-m-d H:i:s");
	$aDateVisit=myqu("UPDATE mytcg_user SET date_last_visit='".$sDate."' WHERE user_id=".$user['user_id']);
	$today = date("Y-m-d");
	if((substr($user['date_last_visit'],0,10) != $today)&&(substr($user['mobile_date_last_visit'],0,10) != $today))
	{
		myqu("UPDATE mytcg_user SET gameswon=0 WHERE user_id=".$user['user_id']);
	}
    
    $token = $facebook->getAccessToken();
    //$friends = $facebook->api('/me/friends?access_token='.$token.'&fields=id,name');
    
    $fql = 'SELECT uid,name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$userProfile['id'].') AND is_app_user = 1';
    $friends = $facebook->api(array('method' => 'fql.query','query' => $fql,));
?>
