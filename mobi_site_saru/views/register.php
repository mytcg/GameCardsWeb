<?php if(!$_SESSION['userID']){ 
?>	
	<form method="POST" action="index.php?page=home&register=1" id="loginForm">
		Email:<br />
		<input type="text" name="email_address" value="" class="textbox" /><br />
		Password:<br />
		<input type="password" name="password" value="" class="textbox" /><br />
		First Name:<br />
		<input type="text" name="name" value="" class="textbox" /><br />
		Surname:<br />
		<input type="text" name="surname" value="" class="textbox" /><br />
		<input type="submit" name="register" value="REGISTER" style="width:80px;float:left" class="button" title="Login"/>
	<div><a href="index.php?page=index"><div class="cmdButton" style="margin-top:5px;padding-top:8px;height:17px;">BACK</div></a></div>
	</form>	


<?php } else { 
	echo("Hi,&nbsp;&nbsp;".$_SESSION['username']."<br>You are currently logged in<br><a href='index.php?page=home'>Main menu</a>");
 }?>	