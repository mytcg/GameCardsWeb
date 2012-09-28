<?php
if ($_GET['logout']){
	setcookie('username','',time()-3600);
	unset($_COOKIE['username']);
	session_destroy();
	echo ("You have been logged out succesfully");
}else { 
	echo("Click on top car logo, to return");
}?>