<?php if(!$_SESSION['userID']){?>
	<form method="POST" action="index.php?page=home&login=1" id="loginForm">
		Email:<br />
		<input type="text" name="username" value="" class="textbox" /><br />
		Password:<br />
		<input type="password" name="password" value="" class="textbox" /><br />
		<input type="submit" name="login" value="LOGIN" style="float:left;" class="button" title="Login"/>
	<div><a href="index.php?page=index"><div class="cmdButton" style="margin-top:5px;padding-top:8px;height:17px;">BACK</div></a></div>
	</form>	
	
<?php }else{
	echo("Hi,&nbsp;&nbsp;".$_SESSION['username']."<br>You are currently logged in<br><a href='index.php?page=home'>Main menu</a>");
	}?>