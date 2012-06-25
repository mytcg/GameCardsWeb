<?php
/* This is the core file for the FBConnect component */
//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
require 'tmhOAuth.php';


include 'EpiCurl.php';
include 'EpiOAuth.php';
include 'EpiTwitter.php';
include 'secret.php';

$sCRLF="\r\n";
$sHTMLCRLF="<br />";
$sTab=chr(9);
$pre = $Conf["database"]["table_prefix"];




if($_GET['setup'] == 1){
    
    
    echo "Checking database...".$sHTMLCRLF;
    //check if fbuid exists in user table, and add if not...
    
    $exists = false;
    $columns = myqu("show columns from ".$pre."_user");
    
    foreach($columns as $column){
        if($column['Field'] == "tuid"){
            $exists = true;
            break;
        }
    }
    
    if(!$exists){
      echo "Updating database...".$sHTMLCRLF;
      myqu("ALTER TABLE `".$pre."_user` ADD `tuid` varchar(25)");
      echo "Setup Complete.".$sHTMLCRLF;
    }else{
      echo "Everything seems to be fine.".$sHTMLCRLF;
      
    }
  }
  


//Damage control!!!!!!!! YEEEHAAAAA!!!!
//NB: this needs to be done before login status... can I pass cookies to php via jquery?
if($_GET['oauth_token']){
  //login call back... this needs to ocure with a postback... it is how twitter does things.
  $twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
  $twitterObj->setToken($_GET['oauth_token']);
  $token = $twitterObj->getAccessToken();
  $twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);
  // save to cookies
  setcookie('oauth_token', $token->oauth_token);
  setcookie('oauth_token_secret', $token->oauth_token_secret);
  $twitterInfo= $twitterObj->get_accountVerify_credentials();
  
  //do we have a local acount?
  $aUser = myqu("SELECT * FROM ".$pre."_user where tuid = '".$twitterInfo->id."'");
  
  if($aUser){
           //autologin logs the user in if an account was found for the user
            $_SESSION['user']['id'] = $aUser[0]['user_id'];
            $_SESSION['user']['username']=$aUser[0]['username'];
            $_SESSION['user']['credits']=$aUser[0]['credits'];
            $_SESSION['user']['xp']=$aUser[0]['xp'];
            $loggedin = 1;

  
            
   }else{
          if($_SESSION["user"]["id"]){
            $aUserExists=myqu("update ".$pre."_user set tuid = '".$twitterInfo->id."' where user_id = '".$_SESSION["user"]["id"]."'");
            
          }else{
            //return userid and fail status[email]
            
            $sUsername=$twitterInfo->screen_name;
            $sPassword=gen_trivial_password();
            //$sEmailAddress=$_GET["email"];
            $tuid=$twitterInfo->id;
            $sFullName=$twitterInfo->name;
            $aConf=myqu(
              'SELECT category, keyname, keyvalue '
              .'FROM '.$pre.'_system'
            );
            $aUserExists=myqu(
              "SELECT user_id, username, email_address "
              ."FROM ".$pre."_user "
              //."WHERE email_address='".$sEmailAddress."' "
              ."WHERE username='".$sUsername."'"
            );
            //echo '<register>'.$sCRLF;
            if ($aUserExists){
              //echo $sTab.'<action val="fail" />'.$sCRLF;
              //echo $sTab.'<message val="'.findSQLValueFromKey
               // ($aConf,"memo","signup_message_fail")
               // .'" />'.$sCRLF;
            } else {
              //echo $sTab.'<action val="success" />'.$sCRLF;
              $sDate=date("Y-m-d H:i:s");
              $sActivateMD5=md5($sDate.$sUsername.$sEmailAddress.$sPassword.$sFullName);
              $aUserInsert=myqu(
                "INSERT INTO "
                .$pre."_user "
                ."(username, name, "
                ."is_active, date_register, credits,tuid) "
                ."VALUES ('".$sUsername."', "
                ."'".$sFullName."', '1', '".$sDate."',"
                ." '".findSQLValueFromKey($aConf,"system","credits_register")."','".$twitterInfo->id."')"
              );
              $aUserIDs=myqu(
                "SELECT * FROM "
                .$pre."_user "
                ."WHERE username='".$sUsername."'"
                );
              $iUserID=intval($aUserIDs[0]["user_id"]);
              $iMod=($iUserID % 10)+1;
              $sSalt=substr(md5($iUserID),$iMod,10);
              $aSaltPassword=myqu(
                "UPDATE "
                .$pre."_user "
                ."SET password='".$sSalt.md5($sPassword)."' "
                ."WHERE user_id='".$iUserID."'"
              );
          
              //echo $sTab.'<message val="'.findSQLValueFromKey($aConf,"memo","signup_message_success")
              //  .'" />'.$sCRLF;
            }
            //echo '</register>'.$sCRLF;
            
           // $aUser = myqu("SELECT * FROM ".$pre."_user where tuid = '".$twitterInfo->id."'");
            $_SESSION['user']['id'] = $aUserIDs[0]['user_id'];
            $_SESSION['user']['username']=$aUserIDs[0]['username'];
            $_SESSION['user']['credits']=$aUserIDs[0]['credits'];
            $_SESSION['user']['xp']=$aUserIDs[0]['xp'];
          }

        }   
        header( 'Location: http://www.mytcg.net/' );
  
  
  
}

if($_GET['tweet'] == 1){
  $twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']);
  $twitterObj.httpRequest("POST","http://api.twitter.com/1/statuses/update",array('status' => $_GET['status']));
}

if($_GET['loginstatus'] == 1){
//login http://mobidex.mytcg.net/?oauth_token=I0KLd7QiBhjrQppsp2oJX3uMzF7ayAUbqJuFoycpnlc&oauth_verifier=Pe90cTsh1C50M7vg6Zu20UK1qHHnM9vtWCRxNrpHQ


  if($_COOKIE['oauth_token']){
    $twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']);
    $twitterInfo= $twitterObj->get_accountVerify_credentials();
    echo '<a href="_app/twitterconnect/?logout=1"><img style="border:none;" src="_app/twitterconnect/btn-twitter-logout.png" /></a>';
 
  }else{
    $twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
    echo '<a href="' . $twitterObj->getAuthenticateUrl() . '"><img style="border:none;" src="_app/twitterconnect/twitter-connect.png" /></a>';
  }
  exit;
}

if($_GET['linkaccount'] == 1){
        $aUser = myqu("update table ".$pre."_user set tuid = '".$_GET['tuid']."' where user_id = ".$_GET['uid']);
         echo "<user>".$sCRLF;
            echo $sTab."<linked>1</linked>".$sCRLF;
         echo "</user>".$sCRLF;
}

if($_GET['logout'] == 1){
       
       unset($_SESSION['user']['id']);
       unset($_SESSION['user']['username']);
       setcookie("oauth_token", "", time()-3600);
       setcookie("oauth_token_secret", "", time()-3600);
       
       //header( 'Location: http://'.$_SERVER['SERVER_NAME'].'mytcg4/' );
       header( 'Location: http://mytcg.net/' );
}

function gen_trivial_password($len = 6)
{
    $r = '';
    for($i=0; $i<$len; $i++)
        $r .= chr(rand(0, 25) + ord('a'));
    return $r;
}


?>

