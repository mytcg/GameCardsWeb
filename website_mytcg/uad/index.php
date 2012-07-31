<?php
require_once("config.php");
include_once('functions.php');
if($testing){
	$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0(Linux; U; Android 2.1; en-gb; HTC Legend Build/ERD79) AppleWebKit/530.17(KHTML, like Gecko) Version/4.0 Mobile/530.17";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5";
	//$_SERVER["HTTP_USER_AGENT"] = "BlackBerry8300/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/136";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (SymbianOS/9.1; U; [en]; Series60/3.0 NokiaE60/4.06.0) AppleWebKit/413 (KHTML, like Gecko) Safari/413";
	//$_SERVER["HTTP_USER_AGENT"] = "NokiaN73-2/3.0-630.0.2 Series60/3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (SymbianOS/9.2; U; Series60/3.1 NokiaE51-1/220.34.37; Profile/MIDP-2.0 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11)";
	//$_SERVER["HTTP_USER_AGENT"] = "SAMSUNG-SGH-i900/1.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11)";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (SymbianOS/9.1; U; [en]; Series60/3.0 NokiaE60/4.06.0) AppleWebKit/413 (KHTML, like Gecko) Safari/413";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Symbian/3; Series60/5.3 Nokia701/111.020.0307; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/533.4 (KHTML, like Gecko) NokiaBrowser/7.4.1.14 Mobile Safari/533.4 3gpp-gba";
}
$sBrowser = "";
$aPhoneData = array();
$iUAD = 0;
$recommend = "";
$sAppstore = "";

if(stripos($_SERVER["HTTP_USER_AGENT"], "iPhone")!==false){
    $recommend = "iphone";
}
elseif((stripos($_SERVER["HTTP_USER_AGENT"], "Symbian")!==false)||(stripos($_SERVER["HTTP_USER_AGENT"], "Nokia")!==false)){    
	$recommend = "symbian";
}
elseif(stripos($_SERVER["HTTP_USER_AGENT"], "Blackberry")!==false){
	$recommend = "bb";
}elseif(stripos($_SERVER["HTTP_USER_AGENT"], "Android")!==false){
	$recommend = "android";
	$sAppstore = "<a href='https://market.android.com/details?id=com.mosync.app_Mobidex'><img src='images/android-market-download-button.png' border='0' /></a>";
}elseif(stripos($_SERVER["HTTP_USER_AGENT"], "Java")!==false){
	$recommend = "java";
}elseif(stripos($_SERVER["HTTP_USER_AGENT"], "Windows Phone OS 7")!==false){
	$recommend = "windows7";
}elseif(stripos($_SERVER["HTTP_USER_AGENT"], "Windows")!==false){
	$recommend = "windowsmobile";
}
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo($sApplicationName); ?> Application Download</title>
<meta http-equiv="content-type" content="application/xhtml+xml" />
<meta http-equiv="cache-control" content="max-age=300" />
<style><?php require("style.css"); ?></style>
</head>
<body>
  <table border=0 cellspacing=0 cellpadding=0 width="331" align="center">
      <tr>
        <td class="menucell"><img src="images/<?php echo($sLogoFilename); ?>" /></td>
      </tr>
      <tr>
        <td class="menucell">
        	Select your mobile operating system...<?php if($recommend!=""){ ?><br />
        	We recommend <br /><a href="detection.php?option=<?php echo($recommend); ?>"><img src="images/<?php echo($recommend); ?>_logo.jpg" border="0" /></a>
        	
        	<?php } ?>
        </td>
      </tr>
      <?php if($sAppstore){ ?>
      <tr>
        <td class="menucell">
        	Get it from the appstore...<br />
        	<?php echo($sAppstore); ?>
        </td>
      </tr>
      <?php } ?>
      <?php if(false){ ?>
      <tr>
        <td class="menucell">
        	Just want to view you cards?<br />
        	<a href="cardlist.php"><img src="images/view_cards.jpg" border="0" /></a>
        </td>
      </tr>
      <?php } ?>
      <tr>
        <td class="menucell">
        	Choose a custom build...<br />
        	<a href="detection.php?option=android"><img src="images/android_logo.jpg" border="0" /></a><br />
        	<a href="detection.php?option=bb"><img src="images/bb_logo.jpg" border="0" /></a><br />
        	<a href="detection.php?option=symbian"><img src="images/symbian_logo.jpg" border="0" /></a><br />
        	<a href="detection.php?option=java"><img src="images/java_logo.jpg" border="0" /></a><br />
        	<a href="detection.php?option=windowsmobile"><img src="images/windowsmobile_logo.jpg" border="0" /></a><br />
        	<a href="detection.php?option=iphone"><img src="images/iphone_logo.jpg" border="0" /></a><br />
        	<a href="detection.php?option=windows7"><img src="images/windows7_logo.jpg" border="0" /></a><br />
        </td>
      </tr>
  </table>
</body>
</html>