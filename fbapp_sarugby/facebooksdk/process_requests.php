<?php
	require_once("../configuration.php");
	require_once("../functions.php");
	require_once("facebook.php");
    $user = null; //facebook user ui
    
    //CREATE FACEBOOK SDK OBJECT
    $facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));
    $fbuser = $facebook->getUser(); //TRY TO GET USER DETAILS IF AUTHENTICATED

	if(($_REQUEST['request_ids'])&&($fbuser > 0)){
	   $request_ids = explode(',', $_REQUEST['request_ids']);
	   foreach ($request_ids as $request_id)
	   {
	      $full_request_id = $request_id . '_' . $fbuser;
	  	  $aSent = $facebook->api("/".$full_request_id."?access_token=".$facebook->getAccessToken() );
		  
		  $sql = "SELECT * FROM mytcg_userrequest WHERE user_fb_id = '".$aSent['to']['id']."'";
	      $res = myqu($sql);
		  if(sizeof($res) == 0){
		  	$sql = "INSERT INTO mytcg_userrequest (user_fb_id,request_user_fb_id) VALUES ('".$aSent['to']['id']."','".$aSent['from']['id']."')";
	      	$res = myqu($sql);
		  }		  
	   }
	}
?>