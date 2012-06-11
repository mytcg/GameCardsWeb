<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

if($_GET['email']){
	$userID = $_SESSION['userDetails']['user_id'];
  	$aUser = myqu("SELECT * FROM mytcg_user WHERE user_id = ".$userID);
    $sEmail = $aUser[0]['email_address'];
    
    $eCode = base64_encode($sEmail);
    $eCode = substr($eCode, -10);
    
    $success = sendEmail($sEmail, 'info@mytcg.net', 'Password Verification - SA Rugby Cards Application','You have sent a request to verify this email address on the SA Rugby Cards System. 
    		
    If you did not request this, please ignore it.
    
    The code below will approve your email address.

    Verification code: '.$eCode);
    
    echo "<email>".$sCRLF;
    echo "<success>1</success>";
    echo "</email>".$sCRLF;
  	exit;
}

if($_GET['answered']){
	$userID = $_SESSION['userDetails']['user_id'];
	$count = myqu("SELECT count(*) as cnt from mytcg_user_answer where answered=0 and user_id = ".$userID);
	$iCount = $count[0]['cnt'];
	echo '<response>'.$sCRLF;;
	if ($iCount == 0) {
		echo $sTab."<success>1</success>".$sCRLF;
	} else {
		echo $sTab."<success>0</success>".$sCRLF;
	}
	echo '</response>'.$sCRLF;
	exit;
}

if($_GET['verify']){
	$userID = $_SESSION['userDetails']['user_id'];
	$aUser = myqu("SELECT * FROM mytcg_user WHERE user_id = ".$userID);
    $sEmail = $aUser[0]['email_address'];
  	$theCode = base64_encode($sEmail);
  	$theCode = substr($theCode, -10);
	echo "<verify>".$sCRLF;

  	if($theCode == $_GET['verify']){
  		echo $sTab."<success>1</success>".$sCRLF;
  		myqu("UPDATE mytcg_user SET email_verified = 1 WHERE user_id = ".$userID);
  	}
  	else{
		  echo $sTab."<success>0</success>".$sCRLF;
    }
    echo "</verify>".$sCRLF;
	exit;
}
?>