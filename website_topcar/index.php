<?php
require_once("config.php");
require_once("func.php");

if (isset($_COOKIE["rLogin"])){
  $details = explode("---",base64_decode($_COOKIE['rLogin']));
  $aValidUser=getUserData($Conf["database"]["table_prefix"],$details[0]);
  if(!$_SESSION['user']['id']){
    $_SESSION['user']['username']=$aValidUser[0]['username'];
    $_SESSION['user']['credits']=$aValidUser[0]['credits'];
    $_SESSION['user']['xp']=$aValidUser[0]['xp'];
    $_SESSION['user']['id']=$aValidUser[0]['user_id'];
  }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>TopCar - Trading Card Games</title>
		<link rel="icon" href="favicon.ico" />
		<script type="text/javascript" src="jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="jquery-ui-1.8.13.custom.min.js"></script>
		
		<!-- GOOGLE ANALYTIC TRACKER  -->
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-25598182-2']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>

		<!-- ui timepicker addon -->
		<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>

		<!-- the transform plugin -->
		<script type="text/javascript" src="jquery-css-transform.js"></script>
		<script type="text/javascript" src="jquery-animate-css-rotate-scale.js"></script>

		<!-- the mousewheel plugin -->
		<script type="text/javascript" src="jquery.mousewheel.js"></script>
		<!-- the mwheelIntent plugin -->
		<script type="text/javascript" src="mwheelIntent.js"></script>
		<!-- the jScrollPane script -->
		<script type="text/javascript" src="jquery.jscrollpane.min.js"></script>
		<script type="text/javascript" src="jquery.form.js"></script>
		
		<script type="text/javascript" src="work.js"></script>
		<link rel="stylesheet" type="text/css" href="ui/jquery-ui-1.8.13.custom.css" />
		<link rel="stylesheet" type="text/css" href="jquery.jscrollpane.css" media="all" />
		<link rel="stylesheet" type="text/css" href="ss.css" />
	</head>
	<body>
	</body>
</html>