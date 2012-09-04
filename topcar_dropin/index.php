<?php
	if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'blackberry') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'iphone'))
	{
		$sUseragent = true;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Topcar Trading Cards</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
<meta http-equiv="keywords" content="mobile game, topcar cards, card games, topcar, mobile game cards, mobilegamecards, topcar cards, free games, free cards, topcar games, car games, topcar application, playing cards" />
<meta http-equiv="description" content="A mobile, web and facebook application for viewing, trading, purchasing and playing with TopCar trading cards." />
<link href="css/style.css"rel="stylesheet" type="text/css" />
</head>

<body>
<div class="logo"><img src="images/logo.jpg" alt="logo" /></div>
<p>A mobile, web and facebook application for viewing, trading, purchasing and playing with TopCar trading cards.</p>
<div class="menu">
<?php
if ($sUseragent){
?>
	<p style="text-align:center;font-size:10px;">We have detected you are using a mobile device, we recommend you use 1 of these</p>
	<a style="background-color:#666;" href="http://www.mytcg.net/uad/">Mobile Application</a>
	<a style="background-color:#666;" href="http://mobi.mytcg.net/">Mobile Site</a>
<?php
}else{
?>
	<p style="text-align:center;font-size:10px;">We have detected you are using a web device, we recommend you use 1 of these</p>
	<a style="background-color:#666;" href="http://www.topcarcards.com">Web Application</a>
	<a style="background-color:#666;" href="https://apps.facebook.com/topcarcards/">Facebook Application</a>
<?php
}
?>
	<p style="text-align:center;font-size:10px;">Alternatively try these</p>
	<a href="http://www.topcarcards.com">Web Application</a>
	<a href="https://apps.facebook.com/topcarcards/">Facebook Application</a>
    <a href="http://www.mytcg.net/uad/">Mobile Application</a>
    <a href="http://mobi.mytcg.net/">Mobile Site</a>
</div>
<div class="footer">
    <p>Design and Developed By <a href="http://www.mytcgtech.com">mytcgtech.com</a></p>
</div>
</body>
</html>
