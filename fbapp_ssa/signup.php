<?php
require_once("configuration.php");
require_once("functions.php");

if(!$localhost){
	require_once("facebooksdk/facebook.php");
	$user = null; //facebook user ui
	$facebook = new Facebook(array(
	  'appId'  => $fbconfig['appid'],
	  'secret' => $fbconfig['secret'],
	  'cookie' => true,
	));
	$fbuser = $facebook->getUser(); //TRY TO GET USER DETAILS IF AUTHENTICATED
	
	if (!$fbuser) {
	  $loginUrl   = $facebook->getLoginUrl( array('scope' => 'user_birthday,email') );
	  echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
	  exit;
	}else{
	  $userProfile = $facebook->api('/me');
	  $_SESSION['userProfile'] = $userProfile;
	}
	if (isset($_GET['code'])){
	  header("Location: " . $fbconfig['appBaseUrl']);
	}
	if (isset($_GET['request_ids'])){
	  
	}
	$user = myqu("SELECT username FROM mytcg_user WHERE facebook_user_id = '".$userProfile['id']."' LIMIT 1");
	if($user){
	  header("Location: home.php");
	}
	
	$male = ($userProfile['gender']=="male")? "checked='checked'" : "";
	$female = ($userProfile['gender']=="female")? "checked='checked'" : "";
	$age = floor((time() - strtotime($userProfile['birthday'])) / 31556926);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Mytcg Trading Card Games</title>
    <link rel="icon" href="favicon.ico" />
    <script type="text/javascript" src="jquery/jquery-1.6.1.min.js"></script>
    <link href="css/stylesheet.css" type="text/css" rel="stylesheet" media="all"  />
    <script language="Javascript">
    $(document).ready (function(){
    	
	$("#login").click(function(){
      	$("#txtResponse").fadeIn();
        var email_address = $("#email_address").val();
        var password = $("#password").val();
		if(email_address==""){
			$("#email_address").focus();
			$("#txtResponse").fadeOut();
		}else if(password==""){
			$("#password").focus();
			$("#txtResponse").fadeOut();
		}else{
			$.post("_app/main.php?login=1&username="+email_address+"&password="+password,function(data){
	        	if(data == "1"){
	        		location.href = "<?php echo($fbconfig['baseUrl']); ?>";
	        	}else{
	        		$("#txtResponse").fadeOut();
	        		$(".divSigninText").html(data);
	        	}
	        });
		}
	});
    
    var validateEmail = function(email){
     	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
   		return re.test(email);
	}
    
    $("#register").click(function(){
      	var name = $("#name").val();
        var surname = $("#surname").val();
        var email_address = $("#email_address").val();
        var password = $("#password").val();
        var age = $("#age").val();
        var gender = $("input[name='gender']:checked").val();
        
        var validEmail = validateEmail(email_address);
        var validAge = !isNaN(age);
        
        if(name==""){
			$("#name").focus();
			$("#txtError").html("Name is required.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
		}else if(surname==""){
			$("#surname").focus();
			$("#txtError").html("Surname is required.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
		}else if(email_address==""){
        	$("#email_address").focus();
			$("#txtError").html("Email address is required.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
        }else if(!validEmail){
        	$("#email_address").focus();
        	$("#txtError").html("Invalid email address.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
        }else if(age==""){
			$("#age").focus();
			$("#txtError").html("Age is required.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
		}else if(!validAge){
        	$("#age").focus();
        	$("#txtError").html("Age is a number only value.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
        }else if(password==""){
			$("#password").focus();
			$("#txtError").html("Password is required.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
		}else if(gender==undefined){
			$("#gender").focus();
			$("#txtError").html("Select a gender.");
        	$(".errorNotice").fadeIn().delay(5000).fadeOut();
		}else{
			$("#txtResponse").fadeIn();
	        $.post("_app/main.php?signup=1&email_address="+email_address+"&password="+password+"&age="+age+"&gender="+gender+"&name="+name+"&surname="+surname,function(data){
	        	if(data == "1"){
	        		location.href = "<?php echo($fbconfig['baseUrl']); ?>";	
	        	}else{
	        		$("#txtResponse").fadeOut();
	        		$("#txtError").html(data);
        			$(".errorNotice").fadeIn().delay(5000).fadeOut();
	        	}
	        });
        }
      });
      
    });
    </script>
    <style>
    	div{
    		position: absolute;
    	}
    	body{
		  font-size:10px;
		  font-family:Arial,Sans;
		  line-height:12px;
		  margin:0;
		  background-color:#C6C6C6;
		  color:#FFF;
		}
		.errorNotice{
			display:none;
			position:absolute;
			top:135px;
			left:360px;
			width:300px;
			padding:3px;
			-moz-border-radius: 9px;
  			border-radius: 9px;
  			background-color:#747474;
			
		}
		.errorNotice div{
			font-weight:bold;
			font-size:13px;
			position:absolute;
			top:17px;
			left:50px;
			float: left;
		}
    </style>
  </head>
  <body>
    <div id="fb-root"></div>
    <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
     <script type="text/javascript">
       FB.init({
         appId  : '<?php echo($fbconfig['appid']); ?>',
         status : true, // check login status
         cookie : true, // enable cookies to allow the server to access the session
         xfbml  : true  // parse XFBML
       });
     </script>
      <div class="divPageLogin">
      	<div class="errorNotice">
      		<img src="_site/important.png" width="40" height="40" />
      		<div id='txtError'>Not a valid email</div>
      	</div>
        <div class="divSignin"></div>
        <div class="divSign"><span>Sign</span> In</div>
        <div class="divSigninBox" style="top:202px;left:115px;">
          <div syle="">
	          <span>Name</span><br />
	          <input type="text" class="signin" id="name" value="<?php echo($userProfile['first_name']); ?>" />
          </div>
          <div style="position: absolute;left:250px;">
          	<span>Sur</span>name<br />
          	<input type="text" class="signin" id="surname" value="<?php echo($userProfile['last_name']); ?>" />
          </div>
        </div>
        <div class="divSigninBox" style="top:262px;left:115px;">
          <span>Email</span> address<br />
          <input type="text" class="signin" id="email_address" value="<?php echo($userProfile['email']); ?>" /><br /><br />
          <span>Pass</span>word<br />
          <input type="text" class="signin" id="password" />
        </div>
        <div class="divSigninBox" style="top:262px;left:365px;">
          <span>Age</span><br />
          <input type="text" class="signin" id="age" style="width:30px;" maxlength="2" value="<?php echo($age); ?>" /><br /><br />
          <span>Gender</span><br />
          <div style="margin-top: 5px;"><input type="radio" name="gender" style="margin-top:5px;width:20px;" value="0" <?php echo($male); ?> /> Male &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="gender" style="width:20px;" value="1" <?php echo($female); ?> /> Female</div>
        </div>
        <div class="divSigninText">
          <span>Welcome to SA rugby cards</span>.
          <br>Just enter your details above, and we will create a brand new shiny account for you.<br /><br />
          <span>Already have an account?</span><br />
          Just enter the email address and password field and we will link your facebook account to your SA Rugby cards account.
          </div>
        <div id="txtResponse" class="profileResponse" style="display:none;height:16px;top:488px;left:670px;"><img src="_site/loading51.gif" width="15" height="15" /></div>
        <div id="login" class="divSigninEnter" style="top:490px;left:485px;">Login</div> <div id="register" class="divSigninEnter" style="top:490px;left:585px;">Register</div>
      	<div class="signupTerms"><a href="terms.php">*Terms and conditions apply.</a></div>
      </div>

  </body>
</html>