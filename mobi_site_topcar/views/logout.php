<?php
if ($_SESSION['userID']){
	setcookie('username','',time()-3600);
	$_SESSION['userID']==null;
	session_destroy();
	echo ('You have been logged out succesfully<br /><a href="index.php?page=index">LOGIN AGAIN?</a>');
}else { 
	echo("Return to main menu, click on logo");
}?>