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
<title>SA Rugby Mobile Trading Cards</title>
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
				<a href="index.php?page=home"><img alt="logo" src="images/header_left.png"/></a>
			</div>
			<?php if($_SESSION['userID']){ ?>
			<a href="index.php?page=home"><div class="cmdButton" style="position:absolute;top:-4px;right:10px;z-index:2;">home</div></a>
			<?php }; ?>
	</div>
	<div id="content">
		<div class="min-width">
			<?php 
		    require_once("views/".$page_url.".php");
		    ?>
		</div>
	</div>
	<!-- 
	<div id="footer">
		<p>SA Rugby Mobile App <a href="http://topcar.mytcg.net" target="_blank">Download here</a></p>
	</div>
	-->
</div>
</body>
</html>