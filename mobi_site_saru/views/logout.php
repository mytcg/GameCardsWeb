<?php
if ($_SESSION['userID']){
	setcookie('username','',time()-3600);
	$_SESSION['userID']==null;
	session_destroy();
	echo ("You have been logged out succesfully");
}else { 
	echo("Click on top car logo, to return");
}?>