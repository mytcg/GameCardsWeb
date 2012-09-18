<?php

session_start();
//require_once("SimpleImage.php");

//classes

class dbconnect
{
	 //Piero
	 var $host = 'dedi94.flk1.host-h.net';
	 var $user = 'mobidvzmrz_1';
	 var $pass = 'C9KKLhi8';
	 var $dbase = 'mobidvzmrz_db1';

	// var $host = 'localhost';
	// var $user = 'root';
	// var $pass = '';
	// var $dbase = 'mobidex';

	var $dbconnection;
	
	function __construct()
	{
		$this->dbconnection = mysql_connect($this->host, $this->user, $this->pass)
			or die (CRLF."* Unable to connect to Database Server");
		
		mysql_select_db($this->dbase, $this->dbconnection)
			or die (CRLF."* Unable to connect to Database -> ".$this->dbase);
	}
	
	function _myqu($sql)
	{
		if($result = mysql_query($sql, $this->dbconnection))
		{
			$data = array();
			while($row = mysql_fetch_assoc($result))
			{ 
				$data[] = $row;
			}
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	function _myqui($sql)
	{
		if(mysql_query($sql, $this->dbconnection))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}


//constants

define(CRLF,"\r\n");


//variables

$pre = 'mytcg';
$gridSize = 12;


//connections

function myqu($sQuery) {
	$conn = new dbconnect();
	return $conn->_myqu($sQuery);
}

function myqui($sQuery) {
	$conn = new dbconnect();
	$conn->_myqui($sQuery);
}


//functions

function editCard($card_id){
	$sCRLF = "\r\n";
	$sTab = chr(9);
	
	$sql = "SELECT C.card_id, I.description AS path, C.image AS img, C.cardorientation_id AS portrait, C.description, C.template
			FROM mytcg_card C
			INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
			WHERE card_id = ".$card_id;
	$aCard = myqu($sql);
	
	$sql = "SELECT CS.categorystat_id AS id,CS.description AS statValue, CST.description AS statName,F.name,F.file,CS.font_size,CS.text_colour,CS.top,CS.left,CS.width,CS.height,CS.frontorback,CS.colour_r,CS.colour_g,CS.colour_b
			FROM mytcg_cardstat CS
			JOIN mytcg_categorystat CST ON (CS.categorystat_id = CST.categorystat_id)
			JOIN mytcg_font F ON (CS.font_id = F.font_id)
			WHERE card_id = ".$card_id;
	$aStats = myqu($sql);
	$iCount = sizeof($aStats); 
	
	echo "<card>".$sCRLF;
	echo $sTab."<card_id>".$aCard[0]['card_id']."</card_id>".$sCRLF;
	echo $sTab."<description>".$aCard[0]['description']."</description>".$sCRLF;
	echo $sTab."<path>".$aCard[0]['path']."</path>".$sCRLF;
	echo $sTab."<img>".$aCard[0]['img']."</img>".$sCRLF;
	echo $sTab."<template>".$aCard[0]['template']."</template>".$sCRLF;
	echo $sTab."<orientation>".$aCard[0]['portrait']."</orientation>".$sCRLF;
	echo $sTab."<searchdata>".$aStats[0]['statValue']."</searchdata>".$sCRLF;
	echo $sTab."<statCount>".$iCount."</statCount>".$sCRLF;
	echo $sTab."<stats>".$sCRLF;
	for($i=1;$i<$iCount;$i++){
		echo $sTab.$sTab."<stat_{$i}>".$sCRLF;
		echo $sTab.$sTab.$sTab."<stat_id>".$aStats[$i]["id"]."</stat_id>".$sCRLF;
		echo $sTab.$sTab.$sTab."<type>".$aStats[$i]["statName"]."</type>".$sCRLF;
		echo $sTab.$sTab.$sTab."<description>".$aStats[$i]["statValue"]."</description>".$sCRLF;
		echo $sTab.$sTab.$sTab."<frontorback>".$aStats[$i]["frontorback"]."</frontorback>".$sCRLF;
		echo $sTab.$sTab.$sTab."<top>".$aStats[$i]["top"]."</top>".$sCRLF;
		echo $sTab.$sTab.$sTab."<left>".$aStats[$i]["left"]."</left>".$sCRLF;
		echo $sTab.$sTab.$sTab."<width>".$aStats[$i]["width"]."</width>".$sCRLF;
		echo $sTab.$sTab.$sTab."<height>".$aStats[$i]["height"]."</height>".$sCRLF;
		echo $sTab.$sTab.$sTab."<color>".$aStats[$i]["text_colour"]."</color>".$sCRLF;
		echo $sTab.$sTab.$sTab."<size>".$aStats[$i]["font_size"]."</size>".$sCRLF;
		echo $sTab.$sTab.$sTab."<font_name>".$aStats[$i]["name"]."</font_name>".$sCRLF;
		echo $sTab.$sTab.$sTab."<font_file>".$aStats[$i]["file"]."</font_file>".$sCRLF;
		echo $sTab.$sTab.$sTab."<bcolor>".$aStats[$i]["colour_r"].",".$aStats[$i]["colour_g"].",".$aStats[$i]["colour_b"]."</bcolor>".$sCRLF;
		echo $sTab.$sTab."</stat_{$i}>".$sCRLF;
	}
	echo $sTab."</stats>".$sCRLF;
	echo "</card>".$sCRLF;
}

function isUserLoggedIn()
{
	if(isset($_SESSION['user']))
	{
		return true;
	}
	else
	{
		return false;
	}
}


function clear($return=false)
{
	$html = '<div class="clear"></div>';
	if($return)
	{
		return $html;
	}
	else
	{
		echo $html;
	}
}


function getExtension($str)
{
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}


function buildBlock($width, $height, $classes, $title, $contents, $customCSS='', $id='', $alt='', $return=false)
{
	if($title == '')
	{
		$notitle = ' no-title';
	}
	else
	{
		$notitle = '';
	}
	
	if($height == '')
	{
		$height = 'auto';
	}
	else
	{
		$height = $height.'px';
	}
	
	$html =
<<<STR
<div id="{$id}" class="block {$classes} {$notitle}" alt="{$alt}" style="width:{$width}px; height:{$height}; {$customCSS}">
	<div class="title">{$title}</div>
	<div class="contents">
		{$contents}
	</div>
</div>
STR;
	
	if($return)
	{
		return $html;
	}
	else
	{
		echo $html;
	}
}

function isUsernameAvailable($username)
{
	$sql = "SELECT user_id, username FROM mytcg_user WHERE username = '{$username}'";
	$aUserDetails = myqu($sql);
	if(sizeof($aUserDetails) > 0)
	{
		return false;
	}
	else
	{
		return true;
	}
	/*
		$userId = $aUserDetails[0]['user_id'];
		$aValidUser=myqu(
			"SELECT user_id, username, password, date_last_visit, credits "
			."FROM mytcg_user "
			."WHERE username='".$aDetails['username']."' "
			."AND is_active='1'"
		);
		$iUserID=$aValidUser[0]["user_id"];
		$iMod=(intval($iUserID) % 10)+1;
		$sPassword=substr(md5($iUserID),$iMod,10).md5($aDetails['password']);
		if ($sPassword==$aValidUser[0]['password']){
			$_SESSION['user'] = $aValidUser[0]['user_id'];
			$_SESSION['username'] = $aValidUser[0]['username'];
			return true;
			break;
		}else{
			return false;
			break;
		}
	}*/
}

// register user
function registerUser($aDetails)
{
	//check for available username (name+surname+i)
	$username = strtolower($aDetails['username']);
	if(!isUsernameAvailable($username))
	{
		$i = 1;
		$username = strtolower($aDetails['username'].$i);
		while(!isUsernameAvailable($username))
		{
			$username = strtolower($aDetails['username'].$i);
			$i++;
		}
	}
	
	//check if the email address is already in the database
	$aUserDetails=myqu('SELECT email_address FROM mytcg_user WHERE email_address="'.$aDetails['email'].'"');
	if (sizeof($aUserDetails) > 0) {
		echo 'Email address, '.$aDetails['email'].', is already registered.';
		return false;
	}
	
	$mobile = $aDetails["ccode"].$aDetails['mobile'];
	
	//check if the cell address is already in the database
	$aUserDetails=myqu('SELECT cell FROM mytcg_user WHERE cell="'.$mobile.'"');
	if (sizeof($aUserDetails) > 0) {
		echo 'Mobile number, '.$mobile.', is already registered.';
		return false;
	}
	
	$sql = "INSERT INTO mytcg_user (username, name, email_address, is_active, date_register, credits, cell,country , pro, paid) 
	VALUES ('{$username}', '{$aDetails['name']}', '{$aDetails['email']}', 1, now(), 0, '{$mobile}','{$aDetails['country']}',{$aDetails['pro']}, 0)";
	myqui($sql);
	
	/* Include PanaceaApi class */
	require_once("../panacea/panacea_api.php");
	
	$api = new PanaceaApi();
	$api->setUsername("LemonFresh");
	$api->setPassword("callie1228");
	 
	$result = $api->message_send($aDetails['mobile'], "Thank you for registering with Mobidex. Please go to www.mobidex.biz/download to download the Mobidex Mobile App");
	 /*
	 if($api->ok($result)) {
	    echo "Message sent! ID was {$result['details']}\n";
	 } else {
	    echo $api->getError();
	 } */
	
	$aUserDetails=myqu("SELECT user_id, username FROM mytcg_user WHERE username = '{$username}'");
	$iUserID = $aUserDetails[0]['user_id'];
	$iMod=(intval($iUserID) % 10)+1;
	$crypPass = substr(md5($iUserID),$iMod,10).md5($aDetails['password']);
	myqui("UPDATE mytcg_user SET password = '{$crypPass}' WHERE user_id = {$iUserID}");
	
	$_SESSION['user'] = $aUserDetails[0]['user_id'];
    $_SESSION['username'] = $aUserDetails[0]['username'];
	
	//send the user a welcome email
	$subject = 'Mobidex Registration';
	$email = $aDetails['email'];
	$name = $aDetails['name'];
	$mobile = $aDetails['mobile'];
	$password = $aDetails['password'];
	$message = <<<STR

<p>Hi {$name},</p>

<p>Welcome to Mobidex! You have successfully registered on <a href="http://www.mobidex.biz/">www.mobidex.biz</a></p>

<p>Your login details:</p>
<pre>
<ul>
	<li>Username: <strong>{$username}</strong></li>
	<li>Password: <strong>{$password}</strong></li>
	<li>Mobile number : <strong>{$mobile}</strong></li>
</ul>
</pre>

<p>Please contact us if you experience any difficulties with the system.</p>

<p>Kind regards,</p>

<p>Mobidex Support Team</p>

STR;

	sendMail($email, $subject, $message);
	
	return $username;
}


function sendMail($to, $subject, $message)
{
	$subject = 'Mobidex Registration';
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers.= 'From: support@mobidex.biz' . "\r\n";
	//send actual email
	try
	{
		error_reporting(E_ERROR);
		if(mail($to, $subject, $message, $headers)){
			return true;
		}
		else{
			return false;
		}	
	}
	catch(Exception $e)
	{
		return false;
	}
}


function printActivationForm()
{
	//settings
	
	$p3 = '1 year subscription';
	$p4 = '19.99';
	$m_1 = '1 year subscription';
	
	$contents = <<<STR
		
	<div id="frmPaymentGateway" style="position:relative; background:black; padding:10px; display:none;">
	
		<p style="padding-left:5px; padding-bottom:5px;">You need to upgrade your account to use the Pro features of Mobidex.</p>
		
		<hr />
		
		<div id="paypal" style="position:relative; width:48%; float:left; text-align:center; margin-left:10px;">
		
			<div class="paypal-icon"></div>
			<br />
			
			<form id="frmPaypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_self">
				<input type="hidden" name="cmd" value="_s-xclick">
				
				<input type="hidden" name="hosted_button_id" value="ZT4SREPVEM5J8">
				<table>
				<tr><td><input type="hidden" name="on0" value="Subscription">Subscription</td></tr><tr><td><select name="os0">
					<option value="1 Year Subscription" alt="1" title="$19.99">1 Year Subscription $19.99</option>
				</select> </td></tr>
				<input type="hidden" name="on1" value="reference"><input type="hidden" name="os1" id="referenceNumber">
				</table>
				<input type="hidden" name="currency_code" value="USD">
				<input type="image" id="cmdPayByPaypal" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" onclick="alert('Payment Gateway disabled until test mode activated.');return false;">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		
		</div>
		
		<div style="position:relative; background:#333333; width:1px; height:240px; float:left; margin-left:10px;"></div>
		
		<div id="creditcard" style="position:relative; width:42%; float:left; text-align:center; margin-left:10px; padding-top:20px; padding-bottom:20px;">
			
			<div class="visa-icon"></div>
			<br />
			
			<p>
				1 year subscription
				<br />
				$19.99
			</p>
			
			<form id="frmCreditCard" method="POST" action="https://www.vcs.co.za/vvonline/ccform.asp">
				<input type="hidden" name="p1" value="8043">
				<input type="hidden" name="p2" id="referenceNumber" value="">
				<input type="hidden" name="p3" value="{$p3}">
				<input type="hidden" name="p4" value="{$p4}">
				<input type="hidden" name="p5" value="e">
				<input type="hidden" name="p6" value="f">
				<input type="hidden" name="p7" value="g">
				<input type="hidden" name="p8" value="h">
				<input type="hidden" name="p9" value="i">
				<input type="hidden" name="p10" value="http://mytcg.net/vcs/cancel/">
				<input type="hidden" name="p11" value="k">
				<input type="hidden" name="p12" value="l">
				<input type="hidden" name="Budget" value="N">
				<input type="hidden" name="NextOccurDate" value="n">
				<input type="hidden" name="m_1" value="{$m_1}">
				<input type="hidden" name="m_2" value="z">
				<input type="hidden" name="m_2" value="z">
				<input type="hidden" name="m_3" value="z">
				<input type="hidden" name="m_4" value="z">
				<input type="hidden" name="m_6" value="z">
				<input type="hidden" name="m_7" value="z">
				<input type="hidden" name="m_8" value="z">
				<input type="hidden" name="m_9" value="z">
				<input type="hidden" name="m_10" value="z">
				<div id="cmdPayByCreditCard" class="button-small center" style="width:120px;" onclick="alert('Payment Gateway disabled until test mode activated.');return false;">Pay by Credit Card</div>
				<div class="credits-image"></div>
			</form>
			
		</div>
		
		<div class="clear"></div>
		
		<hr />
		
		<div id="cmdCancel" class="button center" style="width:50px;">Cancel</div>
		
	</div>

STR;
	/*
		<form id="frmPaypal" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_self">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="CYRRUVYYRMENC">
		<table>
		<tr><td><input type="hidden" name="on0" value="Subscription">Subscription</td></tr><tr><td><select name="os0">
			<option value="1 Year Subscription" alt="39" title="$39.00">1 Year Subscription $39.00</option>
		</select> </td></tr>
		<input type="hidden" name="on1" value="reference"><input type="hidden" name="os1" id="referenceNumber">
		</table>
		<input type="hidden" name="currency_code" value="USD">
		<input type="image" id="cmdPayByPaypal" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
		
		.
		.
		<input type="hidden" name="notify_url" value="http://mytcg.net/paypal/">
		.
	*/
	echo $contents;

}


function authenticateUser($username, $password)
{
   $sql = "SELECT * FROM mytcg_user WHERE username='{$username}'";
   $users = myqu($sql);
   if(sizeof($users) > 0)
   {
      $user = $users[0];
      
      $iUserID=intval($user['user_id']);
      $iMod=($iUserID % 10)+1;
      $sSalt=substr(md5($iUserID),$iMod,10);
      $sPassword = $sSalt.md5($password);
      //is_active
      
      if($sPassword == $user['password'])
      {
         //set user session online
         $_SESSION['user'] = $user['user_id'];
         $_SESSION['username'] = $user['username'];
			$_SESSION['password'] = $password;
			$_SESSION['pro'] = $user['pro'];
			$_SESSION['paid'] = $user['paid'];
         return true;
      }else{
      	return false;
      }
   }
   else
   {
      return false;
   }
}


class JUserHelper
{
	function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
	{
		// Get the salt to use.
		$salt = JUserHelper::getSalt($encryption, $salt, $plaintext);

		// Encrypt the password.
		switch ($encryption)
		{
			case 'plain' :
				return $plaintext;

			case 'sha' :
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));
				return ($show_encrypt) ? '{SHA}'.$encrypted : $encrypted;

			case 'crypt' :
			case 'crypt-des' :
			case 'crypt-md5' :
			case 'crypt-blowfish' :
				return ($show_encrypt ? '{crypt}' : '').crypt($plaintext, $salt);

			case 'md5-base64' :
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));
				return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;

			case 'ssha' :
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext.$salt).$salt);
				return ($show_encrypt) ? '{SSHA}'.$encrypted : $encrypted;

			case 'smd5' :
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext.$salt).$salt);
				return ($show_encrypt) ? '{SMD5}'.$encrypted : $encrypted;

			case 'aprmd5' :
				$length = strlen($plaintext);
				$context = $plaintext.'$apr1$'.$salt;
				$binary = JUserHelper::_bin(md5($plaintext.$salt.$plaintext));

				for ($i = $length; $i > 0; $i -= 16) {
					$context .= substr($binary, 0, ($i > 16 ? 16 : $i));
				}
				for ($i = $length; $i > 0; $i >>= 1) {
					$context .= ($i & 1) ? chr(0) : $plaintext[0];
				}

				$binary = JUserHelper::_bin(md5($context));

				for ($i = 0; $i < 1000; $i ++) {
					$new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
					if ($i % 3) {
						$new .= $salt;
					}
					if ($i % 7) {
						$new .= $plaintext;
					}
					$new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
					$binary = JUserHelper::_bin(md5($new));
				}

				$p = array ();
				for ($i = 0; $i < 5; $i ++) {
					$k = $i +6;
					$j = $i +12;
					if ($j == 16) {
						$j = 5;
					}
					$p[] = JUserHelper::_toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
				}

				return '$apr1$'.$salt.'$'.implode('', $p).JUserHelper::_toAPRMD5(ord($binary[11]), 3);

			case 'md5-hex' :
			default :
				$encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
				return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;
		}
	}


	function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
	{
		// Encrypt the password.
		switch ($encryption)
		{
			case 'crypt' :
			case 'crypt-des' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
				} else {
					return substr(md5(mt_rand()), 0, 2);
				}
				break;

			case 'crypt-md5' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
				} else {
					return '$1$'.substr(md5(mt_rand()), 0, 8).'$';
				}
				break;

			case 'crypt-blowfish' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
				} else {
					return '$2$'.substr(md5(mt_rand()), 0, 12).'$';
				}
				break;

			case 'ssha' :
				if ($seed) {
					return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
				} else {
					return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'smd5' :
				if ($seed) {
					return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
				} else {
					return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'aprmd5' :
				/* 64 characters that are valid for APRMD5 passwords. */
				$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

				if ($seed) {
					return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
				} else {
					$salt = '';
					for ($i = 0; $i < 8; $i ++) {
						$salt .= $APRMD5 {
							rand(0, 63)
							};
					}
					return $salt;
				}
				break;

			default :
				$salt = '';
				if ($seed) {
					$salt = $seed;
				}
				return $salt;
				break;
		}
	}


	function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		$stat = @stat(__FILE__);
		if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i ++) {
			$makepass .= $salt[mt_rand(0, $len -1)];
		}

		return $makepass;
	}

}/** end JUserHelper Class */



function getAllCategories()
{
	$sql =
	"
		SELECT * FROM mytcg_category
		ORDER BY description ASC
	";
	$cats = myqu($sql);
	return $cats;
}


function getTemplates($filter='')
{
	$dir = 'templates/';
	$dir_l = $dir.'landscape/';
	$dir_p = $dir.'portrait/';
	$aTemplates = array();
	$groups = array
	(
		'landscape'=>$dir_l,
		'portrait'=>$dir_p
	);
	
	//get template images (front and back) for portrait and landscape
	$i = 0;
	$limit = 10;
	foreach($groups as $group=>$dir)
	{
		if(is_dir($dir))
		{
			$files = scandir($dir);
			if(sizeof($files) > 0)
			{
				$counter = 1;
				$index = '';
				foreach($files as $file)
				{
					if($file != '.' && $file != '..')
					{
						if($counter == 1)
						{
							$id = $i;
							$description = trim(str_replace('_',' ',substr($file,0,-6)));
							$orientation = $group;
							$industry = 'x';
							$style = 'y';
							$colour = 'z';
							$index = strtolower($description);
							$aTemplates[$index]['id'] = $id;
							$aTemplates[$index]['description'] = $description;
							$aTemplates[$index]['orientation'] = $orientation;
							$aTemplates[$index]['industry'] = $industry;
							$aTemplates[$index]['style'] = $style;
							$aTemplates[$index]['colour'] = $colour;
							$aTemplates[$index]['front'] = $file;
							$counter = 2;
						}
						else
						{
							$aTemplates[$index]['back'] = $file;
							$counter = 1;
							$i++;
						}
					}
				}
			}
		}
		else
		{
			echo 'Error -> Directory "'.$dir.'" not found!';
		}
	}
	
	//echo '<pre>'.print_r($aTemplates,true).'</pre>';
	return $aTemplates;
}


function addAlbum($description)
{
	$description = addslashes($description);
	$sql = "
		INSERT INTO mytcg_deck (user_id, description, imageserver_id, image)
		VALUES
		(
			{$_SESSION['user']},
			'{$description}',
			NULL,
			NULL
		)
	";
	myqui($sql);
}



function getAlbums($arrayonly=false)
{
	$sql = "SELECT * FROM mytcg_deck WHERE user_id='{$_SESSION['user']}' ORDER BY description ASC";
	$aAlbums = myqu($sql);
	if($arrayonly)
	{
		return $aAlbums;
	}
	//print_r($aAlbums);
	$albums = '<p class="album-item" alt="-1"><span class="description">All</span></p>';
	if(sizeof($aAlbums) > 0)
	{
		foreach($aAlbums as $album)
		{
			$albums.= '<p class="album-item" alt="'.$album['deck_id'].'"><span class="description">'.$album['description'].'</span><span class="del-button" id="'.$album['deck_id'].'" alt="album"></span></p>';
		}
	}
	return $albums;
}


function getCardTracking($id='')
{
	$in = ($id=='') ? "SELECT GROUP_CONCAT(card_id) FROM mytcg_card WHERE user_id={$_SESSION['user']}" : $id;
	$restrict = (false) ? ' AND T.status_id=1 ' : '';
	$sql =
	"
		SELECT T.*,U.username,U.user_id as 'receiver'
		FROM mytcg_tradecard AS T
		LEFT OUTER JOIN mytcg_user AS U ON (T.detail = U.cell)
		WHERE card_id IN ($in)
		AND T.user_id={$_SESSION['user']}
		ORDER BY U.username
	";
	//echo '<p>'.$sql.'</p>';
	$aCards = myqu($sql);
	return $aCards;
}


function getCardSubTracking($card_id, $user_id)
{
	$sql =
	"
		SELECT T.*,U.username,U.user_id as 'receiver'
		FROM mytcg_tradecard AS T
		LEFT OUTER JOIN mytcg_user AS U ON (T.detail = U.cell)
		WHERE card_id IN ($card_id)
		AND T.user_id={$user_id}
		ORDER BY U.username
	";
	$aTracks = myqu($sql);
	//echo '<p>'.$sql.'</p>';
	//echo '<pre>'.print_r($aTracks,true).'</pre>';
	return $aTracks;
}


function getTradeCards($tradecard_id)
{
	//GET TRADECARD ENTRY DETAILS
	$sql="SELECT *
		  FROM mytcg_tradecard
		  WHERE tradecard_id = ".$tradecard_id;
	$aDetails = myqu($sql);
	$aDetails = $aDetails[0];
	
	$sql="SELECT T.*,U.username
		FROM mytcg_tradecard AS T
		LEFT OUTER JOIN mytcg_user AS U ON (T.detail = U.cell)
		WHERE T.user_id = (SELECT user_id FROM mytcg_user WHERE cell = {$aDetails['detail']})
		AND T.card_id = {$aDetails['card_id']}";
	$aReturn = myqu($sql);
	$sReturn = '{"bindings":[
        {"ircEvent": "PRIVMSG", "method": "newURI", "regex": ""},
        {"ircEvent": "PRIVMSG", "method": "deleteURI", "regex": ""},
        {"ircEvent": "PRIVMSG", "method": "randomURI", "regex": ""}
    ]}';
	echo $sReturn;
}


function getCardsThumbnails()
{
	$aCards = getCards();
	
	$cardThumbs = '';
	if(sizeof($aCards) > 0)
	{
		foreach($aCards as $card)
		{
			$cardThumbs.= '<img src="'.$card['imageserver'].'cards/'.$card['image'].'_thumb.jpg" class="thumbnail" id="'.$card['card_id'].'" title="'.$card['description'].'" />';
		}
	}
   
	return $cardThumbs;
}


function getUsercardNotes($usercard_id)
{
	//get card id of usercard
	$sql = "SELECT card_id FROM mytcg_usercard WHERE usercard_id=".$usercard_id;
	$rs = myqu($sql);
	$card_id = $rs[0]['card_id'];
	
	$sql = "SELECT * FROM mytcg_usercardnote WHERE user_id=".$_SESSION['user']." AND card_id={$card_id} ORDER BY date_updated DESC";
	$aNotes = myqu($sql);
	//print_r($aNotes);
	$notes = array();
	if(sizeof($aNotes) > 0)
	{
		foreach($aNotes as $note)
		{
			$notes[] = '<li>'.$note['note'].'<span class="del-button" alt="'.$note['usercardnote_id'].'"></span></li>';
		}
	}
	if(sizeof($notes) > 0)
	{
		$notes = implode("",$notes);
	}
	else
	{
		$notes = '';
	}
	return $notes;
}


function deleteUsercardNote($usercardnote_id)
{
	$sql = "DELETE FROM mytcg_usercardnote WHERE usercardnote_id = ".$usercardnote_id;
	myqu($sql);
}


function deleteAlbum($deck_id)
{
	//unset all cards in this album
	$sql = "UPDATE mytcg_usercard SET deck_id=NULL WHERE user_id=".$_SESSION['user'];
	//echo $sql;
	myqui($sql);
	
	$sql = "DELETE FROM mytcg_deck WHERE deck_id = ".$deck_id;
	//echo $sql;
	myqui($sql);
}



function addNoteToCard($usercard_id, $note)
{
	//get card id of usercard
	$sql = "SELECT card_id FROM mytcg_usercard WHERE usercard_id=".$usercard_id;
	$rs = myqu($sql);
	$card_id = $rs[0]['card_id'];
	
	$sql = "INSERT INTO mytcg_usercardnote (user_id, card_id, note, usercardnotestatus_id, date_updated)
		VALUES
		(
			{$_SESSION['user']},
			{$card_id},
			'".addslashes($note)."',
			1,
			NOW()
		)";
	myqu($sql);
}


function getCategories()
{
	$sql = "SELECT * FROM mytcg_category WHERE user_id IS NULL OR user_id IN (".$_SESSION['user'].")";
	$aCategories = myqu($sql);
	//print_r($aCategories);
	$categories = array();
	if(sizeof($aCategories) > 0)
	{
		foreach($aCategories as $cat)
		{
			$categories[] = '<p alt="'.$cat['category_id'].'">'.$cat['description'].'</p>';
		}
	}
	if(sizeof($categories) > 0)
	{
		$categories = implode("",$categories);
	}
	return $categories;
}


function getCards()
{
	$sql =
	"
		SELECT U.username AS 'owner', CA.*,MAX(UC.usercard_id) AS usercard, IM.description AS 'imageserver', LCASE(CO.description) AS 'orientation', CAT.description AS 'album',(SELECT description from mytcg_cardstat WHERE card_id=UC.card_id AND categorystat_id=1) as 'searchtags' 
		FROM mytcg_card CA
		JOIN mytcg_user U ON CA.user_id = U.user_id 
		JOIN mytcg_usercard UC ON CA.card_id = UC.card_id
		JOIN mytcg_category CAT USING(category_id)
		JOIN mytcg_imageserver IM ON CA.thumbnail_imageserver_id = IM.imageserver_id
		JOIN mytcg_cardorientation CO ON CA.cardorientation_id = CO.cardorientation_id
		WHERE CA.user_id={$_SESSION['user']}
		AND CA.cardstatus_id IN (1)
		GROUP BY CA.card_id
	";
	//echo '<p>'.$sql.'</p>';
	$aCards = myqu($sql);
	return $aCards;
}


function deleteCard($id)
{
	//find all usercards of this card and delete them
	echo $sql = "UPDATE mytcg_usercard SET usercardstatus_id = 3 WHERE card_id=".$id;
	myqui($sql);
	
	//delete the card
	echo $sql = "UPDATE mytcg_card SET cardstatus_id = 3 WHERE card_id = ".$id." AND user_id = ".$_SESSION['user'];
	myqui($sql);
	
	//delete the card images
	if($_SERVER['HTTP_HOST']=='localhost')
	{
		$dir = 'C:/wamp/www/mobidex/img/cards/'.$id.'_';
	}
	else
	{
		$dir = $_SERVER['DOCUMENT_ROOT'].'/img/cards/'.$id.'_';
	}
	unlink($dir.'front.jpg');
	unlink($dir.'back.jpg');
	unlink($dir.'thumb.jpg');
}


function deleteUserCard($id)
{
	$sql = "UPDATE mytcg_usercard SET usercardstatus_id = 3 WHERE usercard_id = ".$id." AND user_id = ".$_SESSION['user'];
	return myqui($sql);
}


function moveUsercard($usercard_id, $deck_id)
{
	if($deck_id=='-1')
	{
		$deck_id = "NULL";
	}
	$sql = "UPDATE mytcg_usercard SET deck_id={$deck_id} WHERE usercard_id={$usercard_id} AND user_id=".$_SESSION['user'];
	myqui($sql);
}


function getUserCards($filter='')
{
	$sql =
	"
		SELECT U.username AS 'owner',IF(CA.user_id = {$_SESSION['user']},'1','0') AS yours, UC.*, CA.*, IM.description AS 'imageserver', LCASE(CO.description) AS 'orientation', D.description AS 'album',
			(SELECT description from mytcg_cardstat WHERE card_id=UC.card_id AND categorystat_id=1) as 'searchtags'
		FROM mytcg_usercard UC
		JOIN mytcg_card CA USING(card_id)
		JOIN mytcg_user U ON CA.user_id = U.user_id
		LEFT JOIN mytcg_deck D ON UC.deck_id = D.deck_id
		JOIN mytcg_category CAT USING(category_id)
		JOIN mytcg_imageserver IM ON CA.thumbnail_imageserver_id = IM.imageserver_id
		JOIN mytcg_cardorientation CO ON CA.cardorientation_id = CO.cardorientation_id
		WHERE UC.user_id={$_SESSION['user']}
		AND UC.usercardstatus_id IN (1,4)
	";
	//echo '<p>'.$sql.'</p>';
	$aCards = myqu($sql);
	return $aCards;
}

function truncateString($str, $max=20)
{
	$stringval = $str;
   if(strlen($stringval) > $max)
	{
		$stringval = trim(substr($str,0,$max)).'..';
   }
   return $stringval;
}


function printUserCards($inneronly=false)
{
	$gridSize = 9;
	$pageHeight = 650;
	$aCards = getUserCards();
	$pages = ceil(sizeof($aCards)/$gridSize);
	
	if(!$inneronly) echo '<div id="blocks-holder" class="userCards float-right" style="width:675px;">';
	
		//all user cards
		echo '<div id="cards-holder" style="position:relative; min-height:460px;">';
		if(sizeof($aCards)>0)
		foreach($aCards as $id=>$card)
		{
			//check for web thumbnail
			//if not found create it
			if(!file_exists($card['imageserver'].'cards/'.$card['image'].'_web.jpg'))
			{
				//web thumb
				if($card['cardorientation_id']=='1')
				{
					$width = 90;
					$height = 126;
				}
				else
				{
					$width = 126;
					$height = 90;
				}
				$thumb_src = 'img/cards/'.$card['image'].'_front.jpg';
				$thumb = 'img/cards/'.$card['image'].'_web.jpg';
				make_thumb($thumb_src, $thumb, $width, $height);
			}
			
			$id = $card['usercard_id'];
			//echo '<pre>'.print_r($card,true).'</pre>';
			$description = truncateString($card['description'],16);
			
			$contents =
				'<div class="orientation-holder" style="margin-top:-47px; right:0px;">'.
					'<div id="'.strtolower($card['orientation']).'" class="orientation"></div>'.
				'</div>'.
				'<div class="cardImage" style="cursor:pointer; background:url('.$card['imageserver'].'cards/'.$card['image'].'_web.jpg) center center no-repeat;"><input type="hidden" class="usercard_id" value="'.$id.'" />'.
				'<input type="hidden" class="card_id" value="'.$card['card_id'].'" />'.
				'<input type="hidden" class="cardtype" value="'.$card['cardtype'].'" />'.
				'<input type="hidden" class="yours" value="'.$card['yours'].'" />'.
				'<input type="hidden" class="cardstat" value="'.$card['description'].'" />'.
				'<input type="hidden" class="cardstat" value="'.$card['searchtags'].'" /></div>'.
				//'<div class="cardImage" id="front" alt="back" style="cursor:pointer; background:url('.$card['imageserver'].'cards/'.$card['image'].'_front.jpg) center center no-repeat;"></div>'.
				//'<div class="cardImage hidden" id="back" alt="front" style="cursor:pointer; background:url('.$card['imageserver'].'cards/'.$card['image'].'_back.jpg) center center no-repeat;"></div>'.
				'<div class="card-data hidden">'.
					'<input type="hidden" class="data" value="'.strtolower($card['card_id']).'" />'.
					'<input type="hidden" class="data" id="description" value="'.strtolower($card['description']).'" />'.
					'<input type="hidden" class="data" value="'.strtolower($card['owner']).'" />'.
					'<input type="hidden" class="data" value="'.strtolower($card['orientation']).'" />'.
					'<input type="hidden" class="data" value="'.strtolower($card['album']).'" />'.
				'</div>'
			;
			buildBlock
			(
				215,
				205,
				'card float-left '.$card['orientation'],
				'<span title="'.$card['description'].'">'.$description.'</span>',
				$contents,
				'margin-right:10px; display:none;',
				$id,
				$card['album']
			);
			$i++;
		}
		echo '</div>';
		clear();
		
		//view/filter options
		$contents = <<<STR
		<div class="float-left" style="position:relative; padding-top:12px;">
			<select style="width:200px;">
				<option>All on 1 page</option>
				<option>12 per page</option>
				<option>9 per page</option>
				<option>6 per page</option>
			</select>
		</div>
		<div class="float-right" style="position:relative; padding-top:12px; margin-left:12px;">
			<select style="width:150px;">
				<option>Alphabetical</option>
			</select>
		</div>
		<div class="float-left" style="position:relative; padding-top:12px; margin-left:12px; display:none;">
			<select style="width:200px;">
				<option></option>
			</select>
		</div>
STR;
/*
		buildBlock
		(
			660,
			65,
			'',
			'',
			$contents,
			'position:relative; margin-bottom:10px; margin-top:-10px; padding-left:5px; display:none;',
			''
		);
*/		
		//pagination buttons
		//for each page of 'gridsize' cards, there is a paginated number
		$pageButtons = '';
		for($i=1; $i<=$pages; $i++)
		{
			$active = ($i==1) ? 'button-small-active' : '';
			$pageButtons.= '<div id="'.$i.'" class="button-small '.$active.' float-left" style="width:10px; margin-right: 20px;">'.$i.'</div>';
		}
		
		$width = $pages * 50;
		$navButtons =
			'<div id="prev" class="button-small float-left" style="width:50px; margin-right: 15px;">&lt; PREV</div>'.
			'<div class="page-buttons" alt="'.$gridSize.'" style="width:'.$width.'px; left:50%; margin-left:-'.(($width/2)-7).'px; height:45px;">'.$pageButtons.'</div>'.
			'<div id="next" class="button-small float-right" style="width:50px;">NEXT &gt;</div>';
		buildBlock
		(
			660,
			45,
			'',
			'',
			$navButtons,
			'position:relative; margin-bottom:10px; margin-top:0px; padding-left:5px; display:none;',
			''
		);
		clear();
		
		echo '&nbsp;';
		
	if(!$inneronly) echo '</div>';
}


function printTemplates($filter='')
{
	$gridSize = 9;
	$pageHeight = 650;
	$aTemplates = getTemplates($filter);
	ksort($aTemplates);
	//echo '<pre>'.print_r($aTemplates,true).'</pre>';
	$pages = ceil(sizeof($aTemplates)/$gridSize);
	
	echo '<div id="blocks-holder float-right" style="position:relative; margin-left:auto; width:675px;">';
	
	echo '
<div id="search-message" style="font-size:14px; position:relative; float:right; width:675px; padding:24px 0px 35px 0px; text-align:center; font-style:italic; display:none;">
</div>';
	
		//view/filter options
		$contents = <<<STR
<div class="float-left" style="position:relative; padding-top:12px;">
	<select style="width:200px;">
		<option>All on 1 page</option>
		<option>12 per page</option>
		<option>9 per page</option>
		<option>6 per page</option>
	</select>
</div>
<div class="float-right hidden" style="position:relative; padding-top:12px; margin-left:12px;">
	<select style="width:150px;">
		<option>Alphabetical</option>
	</select>
</div>
<div class="float-left" style="position:relative; padding-top:12px; margin-left:12px; display:none;">
	<select style="width:200px;">
		<option></option>
	</select>
</div>
STR;
/*
		buildBlock
		(
			660,
			65,
			'',
			'',
			$contents,
			'position:relative; margin-bottom:10px; margin-top:-10px; padding-left:5px;',
			''
		);
*/
		//all templates
		echo '<div class="templates-holder" style="position:relative; min-height:460px;">';
		foreach($aTemplates as $id=>$template)
		{
			$img_dir = 'templates/'.$template['orientation'].'/';
			$images =
				'<div class="orientation-holder" style="margin-top:-47px; right:0px;">'.
					'<div id="'.$template['orientation'].'" class="orientation" title="'.ucwords($template['orientation']).'"></div>'.
				'</div>'.
				'<div class="templateImage" id="front" alt="'.$img_dir.$template['front'].'" style="cursor:pointer;"><img class="loading" src="site/loading51.gif" /></div>'.
				'<div class="templateImage hidden" id="back" alt="'.$img_dir.$template['back'].'" style="cursor:pointer;"><img class="loading" src="site/loading51.gif" /></div>'.
				'<input type="hidden" class="searchtag" value="'.$template['description'].'" />'
			;
			buildBlock
			(
				215,
				205,
				'template float-left inplay '.$template['orientation'],
				$template['description'],
				$images,
				'margin-right:10px; display:none;',
				$template['id'],
				$page
			);
			$i++;
		}
		echo '</div>';
		clear();
		
		echo '<br />';
		
		//pagination buttons
		//for each page of 'gridsize' templates, there is a paginated number
		$pageButtons = '';
		for($i=1; $i<=$pages; $i++)
		{
			$active = ($i==1) ? 'button-small-active' : '';
			$pageButtons.= '<div id="'.$i.'" class="button-small '.$active.' float-left" style="width:10px; margin-right: 20px;">'.$i.'</div>';
		}
		
		$width = $pages * 50;
		$navButtons =
			'<div id="prev" class="button-small float-left" style="width:50px; margin-right: 15px;">&lt; PREV</div>'.
			'<div class="page-buttons" alt="'.$gridSize.'" style="width:'.$width.'px; left:50%; margin-left:-'.(($width/2)-7).'px; height:45px;">'.$pageButtons.'</div>'.
			'<div id="next" class="button-small float-right" style="width:50px;">NEXT &gt;</div>';
		buildBlock
		(
			660,
			50,
			'',
			'',
			$navButtons,
			'position:relative; margin-bottom:15px; margin-top:-10px; padding-left:5px;',
			'',
			$gridSize
		);
		clear();
		
		echo '&nbsp;';
		
	echo '</div>';
}


function getFieldtypesCombo($contentsOnly=false)
{
	$sql = "SELECT * FROM mytcg_categorystat";
	$aStats = myqu($sql);
	
	$sCombo = '';
	$sCombo.= '<option value="-1">Select Type</option>';
	if(sizeof($aStats) > 0)
	{
		foreach($aStats as $stat)
		{
			if($stat['categorystat_id'] != '1')
			{
				$sCombo.= '<option value="'.$stat['categorystat_id'].'">'.$stat['description'].'</option>';
			}
		}
	}
	
	return $sCombo;
}



function createCardImage($filename, $width, $height, $top, $left, $background)
{
	//
}


function make_thumb($src, $dest, $desired_width, $desired_height)
{

	// read the source image
	$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	// find the "desired height" of this thumbnail, relative to the desired width
	//$desired_height = floor($height*($desired_width/$width));
	
	// create a new, "virtual" image
	$virtual_image = imagecreatetruecolor($desired_width,$desired_height);
	
	// copy source image at a resized size
	imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
	
	// create the physical thumbnail image to its destination
	imagejpeg($virtual_image,$dest,100);
	
}


function make_jpg($src, $dest)
{

	// read the source image
	$source_image = imagecreatefrompng($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	// find the "desired height" of this thumbnail, relative to the desired width
	//$desired_height = floor($height*($desired_width/$width));
	
	// create a new, "virtual" image
	$virtual_image = imagecreatetruecolor($width,$height);
	
	// copy source image at a resized size
	imagecopyresized($virtual_image,$source_image,0,0,0,0,$width,$height,$width,$height);
	
	// create the physical thumbnail image to its destination
	imagejpeg($virtual_image,$dest,100);
	
}


function saveCard($description, $orientation, $imgFront, $imgBack, $aFrontFields, $aBackFields, $searchTags,$pro, $cardtype, $template ,$username='')
{
	$start = strrpos($template,"/");
	$template = substr($template,$start+1);
	$template = str_replace(")","",$template);
	$template = str_replace('"',"",$template);
	$template = str_replace("png","jpg",$template);
	
	$user_id = $_SESSION['user'];
	if($username != '')
	{
		//user is registering while saving a card
		$sql = "SELECT user_id FROM mytcg_user WHERE username = '{$username}'";
		$userQuery = myqu($sql);
		if(sizeof($userQuery) > 0)
		{
			$user_id = $userQuery[0]['user_id'];
		}
		else
		{
			//username not found - should never get here!!
			return false;
		}
	}
	
	// get next card id
	
	// insert card record
	if($_SESSION['edit']){
		$card_id = $_SESSION['edit'];
		$sql = "UPDATE mytcg_card SET
				description='{$description}',
				date_updated = now(),
				cardorientation_id = {$orientation}
				WHERE card_id = ".$card_id;
	}else{
		$sql = "SELECT (MAX(card_id)+1) AS 'new_id' FROM mytcg_card";
		$card_id = myqu($sql);
		$card_id = (sizeof($card_id) > 0) ? $card_id[0]['new_id'] : 1 ;
		$sql =
		"
			INSERT INTO `mytcg_card`
			(
				`category_id`,`cardtype`,
				`back_imageserver_id`,`front_imageserver_id`,`thumbnail_imageserver_id`,`back_phone_imageserver_id`,`front_phone_imageserver_id`,`thumbnail_phone_imageserver_id`,`cardquality_id`,
				`description`,
				`image`,
				`datetime_added`,
				`cardstatus_id`,
				`user_id`,
				`date_updated`,
				`redeem_code`,
				`cardorientation_id`,
				`template`
			)
			VALUES
			(
				11,{$cardtype},
				1,1,1,1,1,1,1,
				'{$description}',
				'{$card_id}',
				NOW(),
				'1',
				{$user_id},
				'0000-00-00 00:00:00',
				'{$redeemcode}',
				'{$orientation}',
				'{$template}'
			)
		";
		}
	myqui($sql);
	//echo($sql);
	//echo $sql;exit;
	
	
	if(true)
	{
		
		//copy temp images to cards
		
 /**
 * Initialize the cURL session
 */
 $ch = curl_init();

		//front
		if($_SERVER['HTTP_HOST']=='localhost')
		{
			$dir = 'http://localhost/mobidex/img/temp/';
			$dst = 'C:/www/mobidex/img/cards/'.$card_id.'_front.jpg';
			$thumb = 'C:/www/mobidex/img/cards/'.$card_id.'_thumb.jpg';
		}
		else
		{
			$dir = 'http://'.$_SERVER['HTTP_HOST'].'/img/temp/';
			$dst = $_SERVER['DOCUMENT_ROOT'].'/img/cards/'.$card_id.'_front.jpg';
			$thumb = $_SERVER['DOCUMENT_ROOT'].'/img/cards/'.$card_id.'_thumb.jpg';
		}
		$src = $dir.$imgFront;
		$thumb_src = '../img/temp/'.$imgFront;
		
		//phone thumb
		if($orientation=='1')
		{
			$width = 46;
			$height = 64;
		}
		else
		{
			$width = 64;
			$height = 46;
		}
		$thumb = '../img/cards/'.$card_id.'_thumb.jpg';
		make_thumb($thumb_src, $thumb, $width, $height);
		//web thumb
		if($orientation=='1')
		{
			$width = 90;
			$height = 126;
		}
		else
		{
			$width = 126;
			$height = 90;
		}
		$thumb = '../img/cards/'.$card_id.'_web.jpg';
		make_thumb($thumb_src, $thumb, $width, $height);
		
 /**
 * Set the URL of the page or file to download.
 */
 curl_setopt($ch, CURLOPT_URL, $src);

 /**
 * Create a new file
 */
 $fp = fopen($dst, 'w');

 /**
 * Ask cURL to write the contents to a file
 */
 curl_setopt($ch, CURLOPT_FILE, $fp);

 /**
 * Execute the cURL session
 */
 curl_exec ($ch);

		//back
		if($_SERVER['HTTP_HOST']=='localhost')
		{
			$dst = 'C:/www/mobidex/img/cards/'.$card_id.'_back.jpg';
		}
		else
		{
			$dst = $_SERVER['DOCUMENT_ROOT'].'/img/cards/'.$card_id.'_back.jpg';
		}
		$src = $dir.$imgBack;

 /**
 * Set the URL of the page or file to download.
 */
 curl_setopt($ch, CURLOPT_URL, $src);

 /**
 * Create a new file
 */
 $fp = fopen($dst, 'w');

 /**
 * Ask cURL to write the contents to a file
 */
 curl_setopt($ch, CURLOPT_FILE, $fp);

 /**
 * Execute the cURL session
 */
 curl_exec ($ch);

 /**
 * Close cURL session and file
 */
 curl_close ($ch);
 fclose($fp);



		// prepare insert values
		
		if($_SESSION['edit']){
			$sql = "DELETE FROM mytcg_cardstat WHERE card_id = ".$card_id;
			$res = myqu($sql);
		}
		
		$aValues = array();
		
		//add search tags
		$aValues[] ="";
		
		
		$sql = "INSERT INTO `mytcg_cardstat`(`card_id`,`categorystat_id`,description,colour_r,colour_g,colour_b,font_id)";
		$sql .=" VALUES ({$card_id},1,'{$searchTags}',NULL,NULL,NULL,1)";
		$res = myqui($sql);
		
		//front fields
		if(sizeof($aFrontFields) > 0)
		{
			foreach($aFrontFields as $field)
			{
				$frontorback = '1';
				$categorystat = intval($field[0]);
				$description = rawurldecode( trim($field[2]) );
				$statvalue = $field[2];
				$left = $field[3];
				$top = $field[4];
				$width = $field[5];
				$height = $field[6];
				$size = $field[8];
				$color = $field[9];
				$font_id = $field[10];
				$border = hex_to_rgb($field[11]);
				$rgb = $color;
				
				$sql = "INSERT INTO `mytcg_cardstat`(`card_id`,`categorystat_id`,`description`,`statvalue`,`left`,`top`,`width`,`height`,`frontorback`,`colour_r`,`colour_g`,`colour_b`,`font_id`,`text_colour`,`font_size`)";
				$sql .=" VALUES ({$card_id},{$categorystat},'{$description}',NULL,{$left},{$top},{$width},{$height},{$frontorback},{$border['red']},{$border['green']},{$border['blue']},{$font_id},'{$color}','{$size}')";
				$res = myqui($sql);
				if($categorystat == 0){
					write_log($sql);
				}
			}
		}
		
		//back fields
		if(sizeof($aBackFields) > 0)
		{
			foreach($aBackFields as $field)
			{
				$frontorback = '2';
				$categorystat = intval($field[0]);
				$description = rawurldecode( trim($field[2]) );
				$statvalue = $field[2];
				$left = $field[3];
				$top = $field[4];
				$width = $field[5];
				$height = $field[6];
				$size = $field[8];
				$color = $field[9];
				$font_id = $field[10];
				$border = hex_to_rgb($field[11]);
				$rgb = $color;
				
				$sql = "INSERT INTO `mytcg_cardstat`(`card_id`,`categorystat_id`,`description`,`statvalue`,`left`,`top`,`width`,`height`,`frontorback`,`colour_r`,`colour_g`,`colour_b`,`font_id`,`text_colour`,`font_size`)";
				$sql .=" VALUES ({$card_id},{$categorystat},'{$description}',NULL,{$left},{$top},{$width},{$height},{$frontorback},{$border['red']},{$border['green']},{$border['blue']},{$font_id},'{$color}','{$size}')";
				$res = myqui($sql);
				if($categorystat == 0){
					write_log($sql);
				}
			}
		}
		if(!$_SESSION['edit']){
			$sql = "INSERT INTO `mytcg_usercard`(`user_id`,`card_id`,`deck_id`,`usercardstatus_id`,`is_new`)
					VALUES (
						{$_SESSION['user']},
						{$card_id},
						NULL,
						1,
						1
					)";
			myqui($sql);
		}
		
		return true;
	
		exit;
		
	}
	
	
}


/*
    attempt to create an image containing the error message given.
    if this works, the image is sent to the browser. if not, an error
    is logged, and passed back to the browser as a 500 code instead.
*/
function fatal_error($message)
{
    // send an image
    if(function_exists('ImageCreate'))
    {
        $width = ImageFontWidth(5) * strlen($message) + 10 ;
        $height = ImageFontHeight(5) + 10 ;
        if($image = ImageCreate($width,$height))
        {
            $background = ImageColorAllocate($image,255,255,255) ;
            $text_color = ImageColorAllocate($image,0,0,0) ;
            ImageString($image,5,5,5,$message,$text_color) ;
            header('Content-type: image/png') ;
            ImagePNG($image) ;
            ImageDestroy($image) ;
            exit ;
        }
    }

    // send 500 code
    header("HTTP/1.0 500 Internal Server Error") ;
    print($message) ;
    exit ;
}


/*
    decode an HTML hex-code into an array of R,G, and B values.
    accepts these formats: (case insensitive) #ffffff, ffffff, #fff, fff
*/
function hex_to_rgb($hex) {
    // remove '#'
    if(substr($hex,0,1) == '#')
        $hex = substr($hex,1) ;

    // expand short form ('fff') color to long form ('ffffff')
    if(strlen($hex) == 3) {
        $hex = substr($hex,0,1) . substr($hex,0,1) .
               substr($hex,1,1) . substr($hex,1,1) .
               substr($hex,2,1) . substr($hex,2,1) ;
    }

    if(strlen($hex) != 6)
        fatal_error('Error: Invalid color "'.$hex.'"') ;

    // convert from hexidecimal number systems
    $rgb['red'] = hexdec(substr($hex,0,2)) ;
    $rgb['green'] = hexdec(substr($hex,2,2)) ;
    $rgb['blue'] = hexdec(substr($hex,4,2)) ;

    return $rgb ;
}



/**
 * Security Check
 */

if(isUserLoggedIn())
{
	$offlimits = array
	(
	 'home'=>'browse',
	 'signup'=>'browse'
	);
}
else
{
	$offlimits = array
	(
	 'browse'=>'home',
	 'track'=>'signup'
	);
}

foreach($offlimits as $page=>$bounce)
{
	if(strpos($_SERVER['QUERY_STRING'],'page='.$page) > -1)
	{
		header("Location: ?page=".$bounce);
		//die('Permission Denied!... Redirect -> '.$bounce);
	}
}

function write_log($sString){
    $fc = "[".date("Y-m-d h:i:s")."] : ".$sString."\r\n";
    $handle=fopen("../log.txt", "a+");
    fwrite($handle, $fc);
    fclose($handle);
	$handle=null;
}

?>