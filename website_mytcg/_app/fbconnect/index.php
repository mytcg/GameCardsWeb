<?php
/* This is the core file for the FBConnect component */
//GET REQUIRED FILES
require_once("../../config.php");
require_once("../../func.php");
require 'facebook.php';

$sCRLF="\r\n";
$sHTMLCRLF="<br />";
$sTab=chr(9);
$pre = "mytcg";
$userID = $_SESSION["user"]["id"];

/* Description
   This class will interface the
   
   JQuery frontend with the
   
   Facebook Connect backend      */
class FBConnect{

/* Description
   Variable for the prefix
   
   the tables use          */
public $table_prefix;
/* Description
   Variable to keep the controlbreak
   
   for xml output                    */
public $sCRLF="\r\n";
/* Description
   Variable to keep the
   
   controlbreak for html */
public $sHTMLCRLF="<br />";
/* Description
   Variable to keep the tab
   
   character for xml output */
public $sTab="\t";
/* Description
   Instance of the facebook
   
   object                   */
public $facebook = null;
/* Description
   The current facebook session
   
   is kept here                 */
public $session = null;
  

/* \ \ 
   Description
   Constructor for FBConnect
   
   
   Parameters
   pre :  Prefix for database tables */
public function __construct($pre){
  
    $this->table_prefix = $pre;
    // Create our Application instance (replace this with your appId and secret).
    $this->facebook = new Facebook(array(
      'appId' => '196238593746312',
      'secret' => '47614e6aef34b665541f865d2f5c9673',
      'cookie' => true,
    ));
    
    $this->session = $this->facebook->getSession();
}

  
 /* Description
    This function checks that the
    
    minimum database requirements
    
    are met and acts accordingly. */
 public function setup(){
    
    
    echo "Checking database...".$this->sHTMLCRLF;
    //check if fbuid exists in user table, and add if not...
    
    $exists = false;
    $columns = myqu("show columns from ".$this->table_prefix."_user");
    
    foreach($columns as $column){
        if($column['Field'] == "fbuid"){
            $exists = true;
            break;
        }
    }      
    if(!$exists){
      echo "Updating database...".$this->sHTMLCRLF;
      myqu("ALTER TABLE `".$this->table_prefix."_user` ADD `fbuid` varchar(25)");
      echo "Setup Complete.".$this->sHTMLCRLF;
    }else{
      echo "Everything seems to be fine.".$this->sHTMLCRLF;
      
    }
  }
  
 
 /* Description
    Generates the needed javascript for
    
    the facebook connect to function
    
    on the front end
    Returns
    Javascript Text                     */
 public function generateJavascript(){
    
     $sCRLF = $this->sCRLF;

      echo "window.fbAsyncInit = function() {".$sCRLF;
      echo "FB.init({".$sCRLF;
      echo "appId : '".$this->facebook->getAppId()."',".$sCRLF;
      echo "session : ".json_encode($this->session).",".$sCRLF; 
      echo "status : true, // check login status".$sCRLF;
      echo "cookie : true, // enable cookies to allow the server to access the session".$sCRLF;
      echo "xfbml : true // parse XFBML".$sCRLF;
      echo "});".$sCRLF;
    
      echo "// whenever the user logs in, we refresh the page".$sCRLF;
      echo "FB.Event.subscribe('auth.login', function() {".$sCRLF;
      echo "ZA.callAjax(FBC.sBaseURL+\"?checklocalaccount=1\",function(xml){ FBC.checklocalaccountcallback(xml); });".$sCRLF;
      echo "});".$sCRLF;
      echo "};".$sCRLF;
      echo "(function() {".$sCRLF;
      echo "var e = document.createElement('script');".$sCRLF;
      echo "e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';".$sCRLF;
      echo "e.async = true;".$sCRLF ;
      echo "document.getElementById('fb-root').appendChild(e);".$sCRLF;
      echo "}());".$sCRLF;
      
         
  }

 /* Description
    Depending on wether the user
    
    is logged in or not, this
    
    function will return either
    
    a login or logout button.
    Returns
    \Returns HTML code           */
 public function getFBButton(){
    
          
      $me = null;
      // Session based API call.
      if ($this->session) {
        try {
          $uid = $this->facebook->getUser();
          $me = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
          error_log($e);
        }
      }
    
      // login or logout url will be needed depending on current user state.
      if ($me) {
        echo "<a onclick=\"FB.logout(function(response){window.location.reload();}); return false;\" style=\"float: right;\" href=\"#\"><img alt=\"Connect\" src=\"http://static.ak.fbcdn.net/images/fbconnect/logout-buttons/logout_small.gif\" id=\"fb_logout_image\"></a>";
      } else {
        echo "<fb:login-button  perms=\"email,publish_stream\">Connect</fb:login-button>";
      }
    
  }
  
 
 /* Description
    Checks if a facebook user has a
    
    registered account on the website
    Returns
    XML:
    
    <c>\<user\></c>
    
    <c> \<hasaccount\>0\</hasaccount\></c>
    
    <c> \<loggedin\>0\</loggedin\></c>
    
    <c> \<fbuid\>1089362254\</fbuid\></c>
    
    <c> \<name\>Pieter Marais\</name\></c>
    
    <c> \<first_name\>Pieter\</first_name\></c>
    
    <c> \<last_name\>Marais\</last_name\></c>
    
    <c> \<gender\>male\</gender\></c>
    
    <c> \<email\>psmarais@mtnloaded.co.za\</email\></c>
    
    <c>\</user\></c>
    
    
                                                        */
 public function checkForLocalAccount(){
      
      $me = null;
      
      $sCRLF = $this->sCRLF;
      $sTab = $this->sTab;
      // Session based API call.
      if ($this->session) {
        try {
          $uid = $this->facebook->getUser();
          $me = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
          error_log($e);
        }
        
        //check for linked user account
        $aUser = myqu("SELECT * FROM ".$this->table_prefix."_user where fbuid = '".$uid."'");
    
        if($aUser){
            //autologin logs the user in if an account was found for the user
            $_SESSION['user']['id'] = $aUser[0]['user_id'];
            $_SESSION['user']['username']=$aUser[0]['username'];
            $_SESSION['user']['credits']=$aUser[0]['credits'];
            $_SESSION['user']['xp']=$aUser[0]['xp'];
    
            $loggedin = 1;
    
            echo "<user>".$sCRLF;
            echo $sTab."<hasaccount>1</hasaccount>".$sCRLF;
            echo $sTab."<loggedin>".$loggedin."</loggedin>".$sCRLF;
            echo $sTab."<fbuid>".$uid."</fbuid>".$sCRLF;
            echo $sTab."<name>".$me[name]."</name>".$sCRLF;
            echo $sTab."<first_name>".$me[first_name]."</first_name>".$sCRLF;
            echo $sTab."<last_name>".$me[last_name]."</last_name>".$sCRLF;
            echo $sTab."<gender>".$me[gender]."</gender>".$sCRLF;
            echo $sTab."<email>".$me[email]."</email>".$sCRLF;
            echo "</user>".$sCRLF;

  
        }else{

          if($_SESSION["user"]["id"]){
            $aUserExists=myqu("update ".$this->table_prefix."_user set fbuid = '".$uid."' where user_id = '".$_SESSION["user"]["id"]."'");
            
          }else{
          
              $sUsername=$me[first_name].$me[last_name];
              $sPassword=gen_trivial_password();
              $sEmailAddress=$me[email];
              $fbuid=$uid;
              $sFullName=$me[name];
              
              $aConf=myqu(
                'SELECT category, keyname, keyvalue '
                .'FROM '.$this->table_prefix.'_system'
              );
              
              $aUserExists=myqu(
                "SELECT user_id, username, email_address "
                ."FROM ".$this->table_prefix."_user "
                ."WHERE email_address='".$sEmailAddress."' "
                ."OR username='".$sUsername."'"
              );

              if ($aUserExists[0]['user_id'] > 0){
                $_SESSION['user']['id'] = $aUserExists[0]['user_id'];
                $_SESSION['user']['username']=$aUserExists[0]['username'];
                $_SESSION['user']['credits']=$aUserExists[0]['credits'];
                $_SESSION['user']['xp']=$aUserExists[0]['xp'];
                
  
              } else {

                $sDate=date("Y-m-d H:i:s");
                $aUserInsert=myqu(
                  "INSERT INTO "
                  .$this->table_prefix."_user "
                  ."(username, email_address, name, "
                  ."is_active, date_register, credits,fbuid) "
                  ."VALUES ('".$sUsername."', '".$sEmailAddress."', "
                  ."'".$sFullName."', '1', '".$sDate."',"
                  ." '".findSQLValueFromKey($aConf,"system","credits_register")."','".$fbuid."')"
                );
  
                
                $aUserID=myqu(
                  "SELECT * FROM "
                  .$this->table_prefix."_user "
                  ."WHERE username='".$sUsername."' "
                  ."AND email_address='".$sEmailAddress."' "
                  );
                
                $iUserID=intval($aUserID[0]["user_id"]);
                $iMod=($iUserID % 10)+1;
                $sSalt=substr(md5($iUserID),$iMod,10);
                
                $aSaltPassword=myqu(
                  "UPDATE "
                  .$this->table_prefix."_user "
                  ."SET password='".$sSalt.md5($sPassword)."' "
                  ."WHERE user_id='".$iUserID."'"
                );
              
                $_SESSION['user']['id'] = $aUserID[0]['user_id'];
                $_SESSION['user']['username']=$aUserID[0]['username'];
                $_SESSION['user']['credits']=$aUserID[0]['credits'];
                $_SESSION['user']['xp']=$aUserID[0]['xp'];
          

              }

            }

            echo "<user>".$sCRLF;
            echo $sTab."<hasaccount>1</hasaccount>".$sCRLF;
            echo $sTab."<loggedin>1</loggedin>".$sCRLF;
            echo $sTab."<fbuid>".$uid."</fbuid>".$sCRLF;
            echo $sTab."<name>".$me[name]."</name>".$sCRLF;
            echo $sTab."<first_name>".$me[first_name]."</first_name>".$sCRLF;
            echo $sTab."<last_name>".$me[last_name]."</last_name>".$sCRLF;
            echo $sTab."<gender>".$me[gender]."</gender>".$sCRLF;
            echo $sTab."<email>".$me[email]."</email>".$sCRLF;
          
            echo "</user>".$sCRLF;
            exit;
        }   
      }
    }
    
    /* Description
       \Links a standerd user account
       
       to a facebook account
       Parameters
       uid :    id of standard user account
       fbuid :  id of facebook account
       
       Returns
       \Returns xml:
       
       
       
       <c>\<user\></c>
       
       <c>\<linked\>1\</linked\></c>
       
       <c>\</user\> </c>                    */
    public function linkAccount($uid,$fbuid){
        $aUser = myqu("update table ".$this->table_prefix."_user set fbuid = '".$fbuid."' where user_id = ".$uid);
         echo "<user>".$sCRLF;
            echo $sTab."<linked>1</linked>".$sCRLF;
         echo "</user>".$sCRLF;
    }
  
}

//////// Ajax handlers ///////
$fbconnect = new FBConnect($pre);

//Setup the database for the component
if($_GET['setup'] == 1){
    $fbconnect->setup();
    exit;
}

if($_GET['linkfb'] == 1){
  
  if($_GET['uid'] && $_GET['fbuid']){
    $fbconnect->linkAccount($_GET['uid'],$_GET['fbuid']);
  }
  exit;
}

if($_GET['getfbconnecthtml'] == 1){
  $fbconnect->getFBButton();
  exit;
}

//Get javascript code needed by componenent on the html pages
if($_GET['javascriptinit'] == 1){
  $fbconnect->generateJavascript();
  exit;
}

if($_GET['checklocalaccount'] == 1){
  $fbconnect->checkForLocalAccount();
  exit;
}

function gen_trivial_password($len = 6)
{
    $r = '';
    for($i=0; $i<$len; $i++)
        $r .= chr(rand(0, 25) + ord('a'));
    return $r;
}




?>

