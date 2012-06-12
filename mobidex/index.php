<?php
/**
 * Index File, Mobidex
 * 
 * @author Jaco Horn <jaco@mytcg.net>
 * @version 1.0
 * @package index
 */
 
session_start();

//set the cookie for remember me if selected
if(isset($_GET['r']) && $_GET['r']=='1')
{
	setcookie('mobidex_username',$_SESSION['username'],time()+(3600*24*7)); //expire in 1 week
	setcookie('mobidex_password',$_SESSION['password'],time()+(3600*24*7)); //expire in 1 week
}
else if(isset($_GET['r']) && $_GET['r']=='0')
{
	//delete the cookies
	setcookie('mobidex_username','',time()-3600);
	setcookie('mobidex_password','',time()-3600);
}

include('include/system.inc.php');

//controller
$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';

if(isUserLoggedIn())
{
	if($page=='home') $page = 'browse';
}
else
{
	if($page=='browse') $page = 'home';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Mobidex BETA</title>
		
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="styles/style.css" />	
		<link rel="stylesheet" href="common/ui/jquery-ui-1.8.16.custom.css" />
		<link rel="stylesheet" href="common/ui/jquery.ui.slider.css" />
		<link rel="stylesheet" href="common/ui/jquery.ui.theme.css" />

		<!-- jquery libraries -->
		<script src="common/jquery-1.6.4.min.js"></script>
		<script src="common/jquery.livequery.js" type="text/javascript" charset="utf-8"></script>
		<script src="common/jquery-ui-1.8.16.custom.min.js"></script>
		<script src="common/jquery.cookie.js"></script>
		
		<!-- the transform plugin -->
		<script type="text/javascript" src="common/jquery-css-transform.js"></script>
		<script type="text/javascript" src="common/jquery-animate-css-rotate-scale.js"></script>
		
		<script type="text/javascript" src="common/ui/jquery.ui.mouse.js"></script>
		<script type="text/javascript" src="common/ui/jquery.ui.widget.js"></script>

		<!-- file uploader -->
		<script src="common/uploader/ajaxfileupload.js"></script>
	    
		<!-- project -->
		<script src="scripts/mobidex.ui.js"></script>
		
		<!--
		<script src="common/colorpicker/colorpicker.js"></script>
		<link rel="stylesheet" href="common/colorpicker/themes.css" />
		-->
	</head>
	<body>
		<?php
		
//page info
$loggedIn = (isUserLoggedIn()) ? '1' : '0';
echo '<input type="hidden" id="loggedIn" value="'.$loggedIn.'" />';
echo '<input type="hidden" id="currentPage" value="'.$page.'" />';

//system debugging
if(isset($_GET['debug']))
{
	echo '<div style="position:relative;"><pre>[ |< -- DEBUG MODE -- ]'.CRLF;
	$debug = $_GET['debug'];
	switch($debug)
	{
		case 'server':
			print_r($_SERVER);
			break;
		
		case 'session':
			print_r($_SESSION);
			break;
		
		case 'cookie':
			print_r($_COOKIE);
			break;
		
		default:
			break;
	}
	echo '</pre></div>';
}

		//facebook message
		echo '<div style="display:none;">Check out Mobidex, digital business cards that work on all types of phones!<br />http://Mobidex.biz<br />Mobidex keeps your business card(s) up to date, no matter when you give it to someone.</div>';

		//terms and conditions
		echo '<div id="frmTerms" style="display:none;"><div style="padding-right:10px; width:770px; height:535px; overflow-y:scroll; position:relative;">';
		include('terms-and-conditions.txt');
		echo '</div></div>';
		
		//activate pro with payment
		printActivationForm();
		
		//help notes
		include('include/help.inc.php');
		
		?>
		<!-- sign up form popup contents -->
		<div id="frmSignUp" style="display:none;">
			<div class="close"></div>
			<div class="form" id="signup">
				
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<input type="text" class="textbox" name="txtName" alt="Name" value="Name" />
				</div>
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<input type="text" class="textbox" name="txtSurname" alt="Surname" value="Surname" />
				</div>
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<input type="password" class="textbox" name="txtPassword" alt="Password" value="" />
					<div class="label" id="password">Password</div>
				</div>
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<input type="password" class="textbox" name="txtConfirm" alt="Confirm Password" value="" />
					<div class="label" id="confirmpassword">Confirm Password</div>
				</div>
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<input type="text" class="textbox" name="txtEmail" alt="Email" value="Email" />
				</div>
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<input type="text" class="textbox" name="txtCode" alt="Mobile Number" value="+27" style="width:36px;padding:10px 10px 10px 14px;" readonly="readonly" />
					<span style="font-size:14px;color:#666;">(0)</span>
					<input type="text" class="textbox" name="txtMobile" alt="Mobile Number" value="Mobile Number" style="width:126px;" maxlength="9" />
					<p style="padding-left:139px;">e.g. 821234567</p>
				</div>
				<div class="field-holder">
					<div class="check-icon float-left hidden"></div>
					<div class="x-icon float-left"></div>
					<select class="textbox" name="selCountry" alt="Country" style="width:239px">
					<?php 
					$sql = "SELECT * FROM mytcg_country ORDER BY country_name";
					$aCountry = myqu($sql);
					for($i=0; $i < sizeof($aCountry); $i++){
						if($aCountry[$i]['country_name']=="South Africa"){
							echo("<option value='{$aCountry[$i]['country_name']}' alt='{$aCountry[$i]['country_code']}' selected>{$aCountry[$i]['country_name']} [{$aCountry[$i]['country_code']}]</option>");
						}else{
							echo("<option value='{$aCountry[$i]['country_name']}' alt='{$aCountry[$i]['country_code']}'>{$aCountry[$i]['country_name']} [{$aCountry[$i]['country_code']}]</option>");
						}
					}
					?>
					</select>
				</div>
					
				<p style="text-align:center; margin-top:5px;"><input type="checkbox" name="chkAgree" /> <span id="viewTerms" title="View the terms and conditions">I agree to the terms and conditions</span></p>
				<div class="clear"></div>
				
				<div class="error-message" style="padding:5px 90px 10px 10px;"></div>
				<div class="button button-disabled center" id="signup" style="width:75px;">SIGN UP</div>
				<div class="clear"></div>
				
			</div>
		</div>
		
		
		<div id="layout">
			<div id="header">
				<div id="logo"></div>
				<div id="user" style="display:none;"><?php include('include/user.inc.php'); ?></div>
				<div id="menu"><?php include('include/menu.inc.php'); ?></div>
			</div>
			<div id="body">
				<div id="page-top">
					<div id="corner-top-left"></div>
					<div id="corner-top-right"></div>
					<div id="page-help" style="display: none;"><div class="help-icon"><div><a href="" class="help">Help</a></div></div></div>
				</div>
				<div id="page-center">
					<div id="page-contents">
						<?php
						
include('include/'.$page.'.inc.php');
						
						?>
					</div>
					<div id="page-left"></div>
					<div id="page-right"></div>
				</div>
				<div id="page-bottom">
					<div id="corner-bottom-left"></div>
					<div id="corner-bottom-right"></div>
				</div>
			</div>
		</div>
		<div id="footer">
			<?php
			
include('include/footer.inc.php');

			?>
		</div>
		
	</body>
</html>