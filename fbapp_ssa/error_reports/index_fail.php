<?php
require_once("configuration.php");
require_once("functions.php");
require_once("facebooksdk/facebook.php");
  
$app_id = "148093075213465";
$app_secret = "50d96782a8f57e27f191260eef504fd9";
$canvas_page = "http://apps.facebook.com/mobilegamecards/";
$my_url = "http://apps.facebook.com/mobilegamecards/";

session_start();
$code = $_REQUEST["code"];

if(empty($code)) {
  $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
  $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=".$app_id."&redirect_uri=".urlencode($my_url)."&state=".$_SESSION['state'];
  echo("<script> top.location.href='" . $dialog_url . "'</script>");
}

if($_REQUEST['state'] == $_SESSION['state']) {
    
  $curl_data = "client_id=".$app_id."&redirect_uri=".urlencode($my_url)."&client_secret=".$app_secret."&code=".$code;
  $url = "https://graph.facebook.com/oauth/access_token";
  $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_POST           => 1,
        CURLOPT_POSTFIELDS     => $curl_data,
        CURLOPT_VERBOSE        => 1
    );
  $ch = curl_init($url);
  curl_setopt_array($ch,$options);
  $response = curl_exec($ch); 
  curl_close($ch); 

  echo($response);
  
  $params = null;
  parse_str($response, $params);
  
  $graph_url = "https://graph.facebook.com/me";
  $curl_data = "access_token=".$params['access_token'];
  
  $ch = curl_init($graph_url);
  curl_setopt_array($ch,$options);
  $user = curl_exec($ch); 
  curl_close($ch); 
  
  var_dump($user);
}
else {
  echo("The state does not match. You may be a victim of CSRF.");
}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>MyTCG Trading Card Games</title>
		<link rel="icon" href="favicon.ico" />
		<script type="text/javascript" src="jquery/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="jquery/jquery-ui-1.8.13.custom.min.js"></script>
		<!--
		<script type="text/javascript" src="_app/application.js"></script>
		-->
		<script type="text/javascript">
		  $(document).ready (function(){
  		  
		  });
		</script>
		
		<link href="css/jquery.jscrollpane.css" type="text/css" rel="stylesheet" media="all" />
		<link href="css/stylesheet.css" type="text/css" rel="stylesheet" media="all"  />
	</head>
	<body>
  	<div id="divTopMenu">
      <?php //BUILD TOP MENU STRUCTURE ?>
  	</div>
    <div id="divPage">
    
    </div>
  	</div>
  	<div id="divFooterMenu">
      <?php //BUILD FOOTER MENU STRUCTURE ?>
  	</div>
	</body>
</html>