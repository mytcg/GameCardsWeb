<?php
require_once("config.php");
require_once("func.php");

if (isset($_COOKIE["rLogin"])){
  $details = explode("---",base64_decode($_COOKIE['rLogin']));
  $aValidUser=getUserData($Conf["database"]["table_prefix"],$details[0]);
  if(!$_SESSION['user']['id']){
    $_SESSION['user']['username']=$aValidUser[0]['username'];
    $_SESSION['user']['credits']=$aValidUser[0]['credits'];
	$_SESSION['user']['premium']=$aValidUser[0]['premium'];
	$_SESSION['user']['freemium']=$aValidUser[0]['freemium'];
    $_SESSION['user']['xp']=$aValidUser[0]['xp'];
    $_SESSION['user']['id']=$aValidUser[0]['user_id'];
  }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>SA Rugby Cards</title>
		<link rel="icon" href="favicon.ico" />
		<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
		
		<!-- ui timepicker addon -->
		<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>

		<!-- the transform plugin -->
		<script type="text/javascript" src="js/jquery-css-transform.js"></script>
		<script type="text/javascript" src="js/jquery-animate-css-rotate-scale.js"></script>

		<!-- the mousewheel plugin -->
		<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
		<!-- the mwheelIntent plugin -->
		<script type="text/javascript" src="js/mwheelIntent.js"></script>
		<!-- the jScrollPane script -->
		<script type="text/javascript" src="js/jquery.jscrollpane.min.js"></script>
		<script type="text/javascript" src="js/jquery.form.js"></script>
		
		<script type="text/javascript" src="work.js"></script>
		<link rel="stylesheet" type="text/css" href="ui/jquery-ui-1.8.13.custom.css" />
		<link rel="stylesheet" type="text/css" href="js/jquery.jscrollpane.css" media="all" />
		<link rel="stylesheet" type="text/css" href="ss.css" />
	</head>
	<body>
	</body>
</html>