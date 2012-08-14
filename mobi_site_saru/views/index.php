<?php if(!$_SESSION['userID']){ ?>
<ul id="item_list">
	<li><a href="index.php?page=login" id="login"><p>Login</p></a></li>
	<li><a href="index.php?page=register" id="register"><p>Register</p></a></li>
	<?php if($_SERVER["SERVER_NAME"] != "sarugbycards.com") { ?>
	<li><a href="mxit/index.php" id="buy"><p>Buy Credits</p></a></li>
	<?php } ?>
</ul>
<?php } else {
	echo("Hi,&nbsp;&nbsp;".$_SESSION['username']."<br>You are currently logged in<br><a href='index.php?page=home'>Main menu</a>");
 }?>
 


