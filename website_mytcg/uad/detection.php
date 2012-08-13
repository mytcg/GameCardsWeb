<?php
require_once("config.php");
include_once('functions.php');
if($testing){
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0(Linux; U; Android 2.1; en-gb; HTC Legend Build/ERD79) AppleWebKit/530.17(KHTML, like Gecko) Version/4.0 Mobile/530.17";
	$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5";
	//$_SERVER["HTTP_USER_AGENT"] = "BlackBerry8300/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/136";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (SymbianOS/9.1; U; [en]; Series60/3.0 NokiaE60/4.06.0) AppleWebKit/413 (KHTML, like Gecko) Safari/413";
	//$_SERVER["HTTP_USER_AGENT"] = "NokiaN73-2/3.0-630.0.2 Series60/3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (SymbianOS/9.2; U; Series60/3.1 NokiaE51-1/220.34.37; Profile/MIDP-2.0 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11)";
	//$_SERVER["HTTP_USER_AGENT"] = "SAMSUNG-SGH-i900/1.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11)";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (SymbianOS/9.1; U; [en]; Series60/3.0 NokiaE60/4.06.0) AppleWebKit/413 (KHTML, like Gecko) Safari/413";
	//$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Symbian/3; Series60/5.3 Nokia701/111.020.0307; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/533.4 (KHTML, like Gecko) NokiaBrowser/7.4.1.14 Mobile Safari/533.4 3gpp-gba";
}
$sUseragent = $_SERVER["HTTP_USER_AGENT"];

$bSupported = true;
$sPath = "";
$display = "Get detected build here<br />";
if(stripos($_SERVER["HTTP_USER_AGENT"], "iPhone")!==false){
	$bSupported = false;		
	$sPath = "";
    $display .= "<a href='".$sPath."'><img src='images/iphone_logo.jpg' border=0 /></a>";
}
elseif((stripos($sUseragent, "Symbian")!==false)||(stripos($sUseragent, "Nokia")!==false)){    
	$sPath = symbianVersionCheck($_SERVER["HTTP_USER_AGENT"]);
	$display .= "<a href='".$sPath."'><img src='images/symbian_logo.jpg' border=0 /></a>";
}
elseif(stripos($sUseragent, "Blackberry")!==false){
	$sPath = "Build/Blackberry/".$sFileName.".jad";
    $display .= "<a href='".$sPath."'><img src='images/bb_logo.jpg' border=0 /></a>";
}elseif(stripos($sUseragent, "Android")!==false){
	$sPath = "Build/Android/".$sFileName.".apk";
    $display .= "<a href='".$sPath."'><img src='images/android_logo.jpg' border=0 /></a>";
}elseif(stripos($sUseragent, "Java")!==false){
	$sPath = "Build/JavaME/".$sFileName.".jad";
    $display .= "<a href='".$sPath."'><img src='images/java_logo.jpg' border=0 /></a>";
}elseif(stripos($sUseragent, "Windows Phone OS 7")!==false){
	$sPath = "Build/Windows Phone/".$sFileName.".xap";
    $display .= "<a href='".$sPath."'><img src='images/windows7_logo.jpg' border=0 /></a>";
}elseif(stripos($sUseragent, "Windows")!==false){
	$sPath = "Build/Windows Mobile/".$sFileName.".cab";
    $display .= "<a href='".$sPath."'><img src='images/windowsmobile_logo.jpg' border=0 /></a>";
}

$do_it = false;
if(intval($_GET['u']) > 0){
  $iUAD = $_GET['u'];
  $redirect = false;
  $sFilePath = "";
  
  $do_it = true;
  
  $email = $_POST['email'];
  $cell = $_POST['cell'];
  
    $display = "You will be notified as soon as the application becomes available for your phone.";
    mysql_query("UPDATE mytcg_useragent_detection SET
                 useragent_email='".$_POST['email']."',
                 useragent_cell = '".$_POST['cell']."'
                 WHERE useragent_id = ".$_GET['u']);
}else{
  $sBrowser = checkHTTPHeaders();
	writeSQLLog($sUseragent,$sPath,$sBrowser);
}

if($_GET['option']){
	$_SESSION['os'] = $_GET['option']; 
}
$option = $_SESSION['os'];

$bOptionAvailable = true;
if($option=="iphone"){
	$option="iPhone";
    $oPath = "";
	$bOptionAvailable = false;
	$img = "iphone_logo.jpg";
}elseif($option=="symbian"){
	$option="Symbian";   
	$oPath = $sWebPath."Build/Symbian/S60E5/".$sFileName.".sisx";
	$img = "symbian_logo.jpg";
}elseif($option=="bb"){
	$option="Blackberry";
	$oPath = $sWebPath."Build/Blackberry/".$sFileName.".jad";
	$img = "bb_download_bt.png";
}elseif($option=="android"){
	$option="Android";
	$oPath = $sWebPath."Build/Android/".$sFileName.".apk";
	$img = "android_download.png";
}elseif($option=="java"){
	$option="Java Feature Phone";
	$oPath = $sWebPath."Build/JavaME/".$sFileName.".jad";
	$img = "java_logo.jpg";
}elseif($option=="windows7"){
	$option="Windows 7";
	$oPath = $sWebPath."Build/Windows Phone/".$sFileName.".xap";
	$img = "windows7_logo.jpg";
}elseif($option=="windowsmobile"){
	$option="Windows Mobile";
	$oPath = $sWebPath."Build/Windows Phone/".$sFileName.".cab";
	$bOptionAvailable = false;
	$img = "windowsmobile_logo.jpg";
}



?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>MyTCG Application Download</title>
<meta http-equiv="content-type" content="application/xhtml+xml" />
<meta http-equiv="cache-control" content="max-age=300" />
<style><?php require("style.css"); ?></style>
</head>
<body>
  <table border=0 cellspacing=0 cellpadding=0 width="331" align="center">
      <tr>
        <td class="menucell"><center><img src="images/<?php echo($sLogoFilename); ?>" /></center></td>
      </tr>
      <?php if(($bSupported)||($do_it)){ ?>
      <tr>
        <td class="menucell"><?php echo($display); ?></td>
      </tr>
      <?php } ?>
      
      <?php if((!$bSupported)&&(!$do_it)){ ?>
      <tr>
        <td class="menucell">
          While were getting your phone supported,<br>why not play on www.mytcg.net.<br>
        </td>
      </tr>
      <tr style="text-align:center;">
        <form action="?u=<?php echo($iUAD); ?>" method="POST">
        <td>
        <br />
        <b>Email</b><br />
        <input type="text" name="email" class="borders" value="<?php echo($_POST['email']); ?>" />
        <br /><br /><b>OR</b><br /><br />
        <b>Cell number</b><br />
        <input type="text" name="cell" class="borders" value="<?php echo($_POST['cell']); ?>" />
        <br />
        <input type="submit" name="Submit" value="Submit" class="submitz" />
        </td>
        </form>
      </tr>
      <?php } ?>
      <?php if($bOptionAvailable){ ?>
      <tr>
        <td class="menucell"><br />
			Get your selected build here<br />
			<a href="<?php echo($oPath); ?>"><img src="images/<?php echo($img); ?>" border="0" /></a>
		</td>
      </tr>
      <?php } ?>
  </table>
</body>
</html>