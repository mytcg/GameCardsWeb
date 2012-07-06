<?php if(!$_SESSION['userID']){?>
	<form method="POST" action="index.php?page=home" id="loginForm">
		Email:<br />
		<input type="text" name="username" value="" class="textbox" /><br />
		Password:<br />
		<input type="password" name="password" value="" class="textbox" /><br />
		<input type="submit" value="LOGIN" class="button" title="Login"/>
	</form>	
<?php }else{
	echo("Hi,&nbsp;&nbsp;".$_SESSION['username']."<br>You are currently logged in<br><a href='index.php?page=home'>Main menu</a>");
	}?>