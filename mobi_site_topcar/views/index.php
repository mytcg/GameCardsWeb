<?php if(!$_SESSION['userID']){ ?>
<ul id="item_list">
	<li><a href="index.php?page=login" id="login"><p>Login</p></a></li>
	<!-- <li><a href="index.php?page=register" id="register"><p>Register</p></a></li> -->
</ul>
<?php } else {
	echo("Hi,&nbsp;&nbsp;".$_SESSION['username']."<br>You are currently logged in<br><a href='index.php?page=home'>Main menu</a>");
 }?>