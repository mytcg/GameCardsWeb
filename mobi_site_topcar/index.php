<?php
require_once("functions.php");
require_once("conn.php");
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="cache-control" content="max-age=200" />
<link href="style.css" media="handheld, screen" rel="stylesheet" type="text/css" />
<link rel="icon" href="favicon.ico" />
<title>Top Car Mobile Game</title>
<?php 
    if (isset($_GET['page'])) {
    	$page_url = $_GET['page']; 
	 } else {
	 	$page_url = "index";
	 }
?>
</head>
<body>
<div class="mainwrapper">
	<div id="header">
			<div id="logo">
				<a href="index.php?page=home"><img height="44px" width="108px" alt="logo" src="images/crosslogo.png"/></a>
			</div>
	</div>
	<div id="content">
		<div class="min-width">
			<?php 
		    require_once("views/".$page_url.".php");
		    ?>
		</div>
	</div>
	<div id="footer">
		<p>Topcar Mobile App <a href="http://topcar.mytcg.net" target="_blank">Download</a></p>
	</div>
</div>
</body>
</html>