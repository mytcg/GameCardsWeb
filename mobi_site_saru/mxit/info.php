<?php
require_once("functions.php");

   //Display message if form input invalid
	function displayInvalidMessage($formFieldType) {
		global $invalidInputType;
		global $formValid;
		if ($formValid[$formFieldType]=='invalid') {
			switch ($formFieldType) {
				case 'username': 
					switch($invalidInputType) {
						case 'noneGiven': echo 'username not given';
							break;
						case 'doesntExist': echo 'username does not exist';
							break;
					}
				break;
			}
		} else {
			return true;
		}
	}
	
   	if (isset($_POST['username']) && $_POST['username']!='') {
		
	$query = "SELECT user_id, username FROM mytcg_user WHERE username='" . $_POST['username'] . "'";
	$result = myqu($query);
		
		if ($result==true) {
			$formValid['username'] = 'valid';
			$validUserName = $result[0]['username'];
			$userID = $result[0]['user_id'];
			header("Location: purchase.php");
			
			exit;

		}  elseif ($result==false) {
			$formValid['username'] = 'invalid';
			$invalidInputType = 'doesntExist';
		}

	}else if (isset($_POST['username']) && $_POST['username']=='') {
		$formValid['username'] = 'invalid';
		$invalidInputType = 'noneGiven';
	}
?>
<html>
   <head>
   <style>p,a {font-size:12px;font-family:"Arial","Arial Black";font-weight:900;text-decoration:none;color:#777777}</style>
      <title>SA Rugby Cards App</title>
   </head>
   <body>
       <img src="images/header_left.png" border="0" /><br />
       <form action="info.php" method="POST"><br/>
       		<div>
	  			<p>Enter Username of Credits Recipient<br />(*case sensitive)</p>
	  			<input name="username" value="" type="text">
	  			<p><?php displayInvalidMessage('username'); ?></p>
	  		</div>
	  		<input type="submit" value="Continue" />
		</form>
   </body>
<html>