<?php  
  //GET REQUIRED FILES
  require_once("../../config.php");
  require_once("../../func.php");
  $sCRLF="\r\n";
  $sTab=chr(9);
    
  //SETUP PREFIX FOR TABLES
  $pre = $Conf["database"]["table_prefix"];
  $userID = $_SESSION["user"]["id"];
  
  if($_GET['init'] == 1){
    $aUser = myqu("SELECT * FROM mytcg_user WHERE user_id = ".$userID);
    echo "<profile>".$sCRLF;
    if($aUser){
      echo $sTab."<username>".$aUser[0]['username']."</username>".$sCRLF;
      echo $sTab."<email>".$aUser[0]['email_address']."</email>".$sCRLF;
      echo $sTab."<msisdn>".$aUser[0]['msisdn']."</msisdn>".$sCRLF;
      echo $sTab."<verified>".$aUser[0]['email_verified']."</verified>".$sCRLF;
    }
    $query = "SELECT UD.description AS question, UD.credit_value, UA.answer, UA.answered
              FROM mytcg_user_detail UD
              LEFT JOIN mytcg_user_answer UA ON (UD.detail_id = UA.detail_id)
              WHERE UA.user_id = ".$userID;
    $aAnswers = myqu($query);
    if($aAnswers){
      echo $sTab."<answers>".$sCRLF;
      $iCount = 0;
      foreach($aAnswers  as $sAnswer){
        echo $sTab.$sTab."<question_".$iCount.">".$sCRLF;
        echo $sTab.$sTab.$sTab."<question>".$sAnswer['question']."</question>".$sCRLF;
        echo $sTab.$sTab.$sTab."<answer>".$sAnswer['answer']."</answer>".$sCRLF;
        echo $sTab.$sTab.$sTab."<creditval>".$sAnswer['credit_value']."</creditval>".$sCRLF;
        echo $sTab.$sTab.$sTab."<answered>".$sAnswer['answered']."</answered>".$sCRLF;
        echo $sTab.$sTab."</question_".$iCount.">".$sCRLF;
        $iCount++;
      }
      echo $sTab."<count>".$iCount."</count>".$sCRLF;
      echo $sTab."</answers>".$sCRLF;
    }
    echo "</profile>".$sCRLF;
  }
  
  if($_GET['verify'] == 1){
    $aUser = myqu("SELECT * FROM mytcg_user WHERE user_id = ".$userID);
    $sEmail = $aUser[0]['email_address'];
  	$theCode = base64_encode($sEmail);
  	$theCode = substr($theCode, -10);
	  echo "<cup>".$sCRLF;

  	if($theCode == $_GET['code']){
  		echo $sTab."<success>1</success>".$sCRLF;
  		myqu("	UPDATE mytcg_user SET email_verified = 1	WHERE user_id = ".$userID);
  		
  		$cpsQuery = myqu("SELECT completion_process_stage,credits FROM ".$pre."_user WHERE user_id = ".$userID);
  		$cps = $cpsQuery[0]['completion_process_stage'];
  		$credits = $cpsQuery[0]['credits'];
  		if($cps=='4'){
    		myqu("UPDATE mytcg_user SET completion_process_stage = 6, credits=credits+10 WHERE user_id = ".$userID);
  			myqu("INSERT INTO mytcg_transactionlog (user_id, description, date, val) VALUES (".$userID.", 'Received 10 credits for verifying email address', NOW(), 10)");
        echo $sTab."<process>6</process>".$sCRLF;
    		echo $sTab."<credits>".($credits + 10)."</credits>".$sCRLF;
  		}
  	}
  	else{
		  echo $sTab."<success>0</success>".$sCRLF;
    }
    echo "</cup>".$sCRLF;
  }
  

  
  if($_GET['sendverificationemail'] == 1){
    $aUser = myqu("SELECT * FROM mytcg_user WHERE user_id = ".$userID);
    $sEmail = $aUser[0]['email_address'];
    
    $eCode = base64_encode($sEmail);
    $eCode = substr($eCode, -10);
    
    sendEmail($sEmail, 'info@mytcg.net', 'Password Verification - Mobile Game Card Applications','Good Day Game Card user.
    
    You have requested a change of details (email address) on the Game Card System. 
    		
    If you did not request this, please ignore it.
    
    To proceed with your change of details, enter this verification code on the credit purchase 
    screen (Located in the  \' My Details \' window)

    Verification code: '.$eCode);
    
    
    echo "<email>".$sCRLF;
    echo $sTab."<success>1</success>".$sCRLF;
    echo "</email>".$sCRLF;
  }
  
  if($_GET['save'] == 1){
    $aUser = myqu("SELECT * FROM ".$pre."_user WHERE user_id = ".$userID);
    $aUser = $aUser[0];
    echo "<profile>".$sCRLF;
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $msisdn = trim($_POST['msisdn']);
    
    
    //CHECK USERNAME AND EMAIL VALIDATIONS 
    if($username!=$aUser['username']){
      $aCompare = myqu("SELECT * FROM mytcg_user WHERE username = '".$username."'");
      if(sizeof($aCompare) > 0){
        echo $sTab."<error>Username in use, please use another username.</error>".$sCRLF;
        echo "</profile>".$sCRLF;
        break;
      }
    }
    if($email!=$aUser['email_address']){
      $aCompare = myqu("SELECT * FROM mytcg_user WHERE email_address = '".$email."'");
      if(sizeof($aCompare) > 0){
        echo $sTab."<error>Email in use, please check your spelling.</error>".$sCRLF;        
        echo "</profile>".$sCRLF;
        break;
      }
    }
    
    //USERNAME
    if($username != $aUser['username']){     
      myqu("UPDATE ".$pre."_user SET username = '".$username."' WHERE user_id = ".$userID);
    }
    echo $sTab."<username>".$username."</username>".$sCRLF;

    //PASSWORD
    if($password != ""){
      $iMod=($userID % 10)+1;
      $sSalt=substr(md5($userID),$iMod,10);
      $sPassword = $sSalt.md5($password);
      myqu("UPDATE ".$pre."_user SET password = '".$sPassword."' WHERE user_id = ".$userID);
    }
    
    //EMAIL ADDRESS
    if($email != $aUser['email_address']){
      myqu("UPDATE mytcg_user SET email_address = '".$email."',email_verified = 0 WHERE user_id = ".$userID);
      myqu("UPDATE mytcg_user_answer SET answer = '".$email."',answered = 1 WHERE user_id = ".$userID);
    }
    echo $sTab."<email>".$email."</email>".$sCRLF;
    
    //MSISDN
    if($msisdn != $aUser['msisdn']){
      myqu("UPDATE ".$pre."_user SET msisdn = '".$msisdn."' WHERE user_id = ".$userID);
      myqu("UPDATE ".$pre."_user_answer SET answer = '".$msisdn."',answered = 1 WHERE user_id = ".$userID);
    }
    echo $sTab."<msisdn>".$msisdn."</msisdn>".$sCRLF;
    
    //RELOAD USER DETAILS
    $aUser = myqu("SELECT * FROM ".$pre."_user WHERE user_id = ".$userID);
    $aUser = $aUser[0];
    
    //VERIFIED
    echo $sTab."<verified>".$aUser['email_verified']."</verified>".$sCRLF;
    echo $sTab."<error>Saved</error>".$sCRLF;
    
    //QUESTIONS
    $aQuestions = myqu("SELECT * FROM ".$pre."_user_detail");
    foreach($aQuestions as $aQ){
      $title = strtolower(str_replace(" ","",$aQ['description']));
      if($_POST[$title]){
        myqu("UPDATE ".$pre."_user_answer SET answer = '".$_POST[$title]."',answered=1 WHERE detail_id = ".$aQ['detail_id']." AND user_id = ".$userID);
      }
    }
    
    $query = "SELECT UD.description AS question, UD.credit_value, UA.answer, UA.answered
              FROM mytcg_user_detail UD
              LEFT JOIN mytcg_user_answer UA ON (UD.detail_id = UA.detail_id)
              WHERE UA.user_id = ".$userID;
    $aAnswers = myqu($query);
    if($aAnswers){
      echo $sTab."<answers>".$sCRLF;
      $iCount = 0;
      foreach($aAnswers  as $sAnswer){
        echo $sTab.$sTab."<question_".$iCount.">".$sCRLF;
        echo $sTab.$sTab.$sTab."<question>".$sAnswer['question']."</question>".$sCRLF;
        echo $sTab.$sTab.$sTab."<answer>".$sAnswer['answer']."</answer>".$sCRLF;
        echo $sTab.$sTab.$sTab."<creditval>".$sAnswer['credit_value']."</creditval>".$sCRLF;
        echo $sTab.$sTab.$sTab."<answered>".$sAnswer['answered']."</answered>".$sCRLF;
        echo $sTab.$sTab."</question_".$iCount.">".$sCRLF;
        $iCount++;
      }
      echo $sTab."<count>".$iCount."</count>".$sCRLF;
      echo $sTab."</answers>".$sCRLF;
    }
    
    echo "</profile>".$sCRLF;
  }
?>