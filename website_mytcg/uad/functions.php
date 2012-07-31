<?php
function writeSQLLog($sUseragent,$sPath,$sBrowser){
  global $iUAD;
  $iSize=sizeOf($aData);
  $sQuery='INSERT INTO mytcg_useragent_detection (';
  $sQuery.='`useragent_http_user_agent`,';
  $sQuery.='`useragent_path_chosen`,';
  $sQuery.='`useragent_completed`,';
  $sQuery.='`useragent_browser`,';
  $sQuery.='`useragent_date_captured`';
  $sQuery.=') VALUES ("'.$sUseragent.'","'.$sPath.'",1,"'.$sBrowser.'","'.date('Y-m-d H:i:s').'")';
  if(!mysql_query($sQuery)){
    $aFileHandle=fopen("sqlq.log","a+");
    fwrite($aFileHandle,$sQuery."\r\n");
    fclose($aFileHandle);
  }else{
    $iUAD = mysql_insert_id();
  }
}

function checkHTTPHeaders(){
  $sBrowser="Default";
  $sCompareUA = $_SERVER['HTTP_USER_AGENT'];
 if (array_key_exists("HTTP_X_OPERAMINI_PHONE_UA", $_SERVER)) {
   $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
   $sBrowser = "Opera Mini";
 }
 if(strpos(strtolower($sCompareUA),"opera mobi") > 0){
   $sBrowser = "Opera Mobile";
 }
 return $sBrowser;
}

function detectUserAgent(){
  global $aPhoneData;
  require_once 'Tera-WURFL/TeraWurfl.php';
  $wurflObj = new TeraWurfl();
  $wurflObj->getDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);
  $aPhoneData = $wurflObj->capabilities;
}

function checkFileAndDir($path,$filename,$ext){
  if (is_dir($path)){
    if ($handle = opendir($path)){
      while (false !== ($file = readdir($handle))) {
        if(stripos($file, $filename)!==false){
          return $file;
        }
      }
      closedir($handle);
    }
  }
  return "";
}

function getExtension($sDeviceOS){
  if(stripos($sDeviceOS, "Android")!==false){ return ".apk"; }
  if(stripos($sDeviceOS, "J2ME")!==false){ return ".jad"; }
  if(stripos($sDeviceOS, "Windows")!==false){ return ".cab"; }
  if(stripos($sDeviceOS, "iPhone")!==false){ return ""; }
  if(stripos($sDeviceOS, "Symbian")!==false){ return ".sis"; }  
  return "";
}

function buildFilePath($aPathData,$sFilePath,$sFileName){
  global $aPathTries;
  global $bFound;
  $aPath = $sFilePath;
  $sPathData = implode(",",$aPathData);
  $sExt = getExtension($sPathData);
  $sFile = $sFileName.$sExt;
  
  //JAVA BUILDS
  global $aInsertData;
  $header = $aInsertData[0];
  if((stripos($header,"MIDP") > 0)||(stripos($header,"JavaPlatform") > 0)){
  	if(($aPathData[2]=="")&&($aPathData[3]=="")){
  		$aPathData[2]="Java";
		$aPathData[3]=$aPathData[0];
  	}
  }
  
  //Try the model and make path to find build folder
  $sCheck = checkPath($aPath,$aPathData[0]);
  if ($sCheck != ""){
    $aPath .= $sCheck."/";
    $sCheck = checkPath($aPath,$aPathData[1]);
    if ($sCheck != ""){
      $aPath .= $sCheck."/";
    }
  }
  
  //Check for file presence
  $sResponse = checkFileAndDir($aPath."package/",$sFile,$sExt);
  if($sResponse != ""){
    $aPath .= "package/".$sResponse;
    array_push($aPathTries,$aPath);
    array_push($aPathTries,"Matched");
    array_push($aPathTries,"Matched");
    $bFound=true;
    return $aPath;
  }else{
    array_push($aPathTries,$sFilePath.$aPathData[0]."/".$aPathData[1]."/package/".$sFile);
  }

  //Try the OS and Version build
  $aPath = $sFilePath;
  $sCheck = checkPath($aPath,$aPathData[2]);
  if ($sCheck != ""){
    $aPath .= $sCheck."/";
    $sCheck = checkPath($aPath,$aPathData[3]);
    if ($sCheck != ""){
      $aPath .= $sCheck."/";
    }
  }
  //Check for file presence
  $sResponse = checkFileAndDir($aPath."package/",$sFile,$sExt);
  if($sResponse != ""){
    $aPath .= "package/".$sResponse;
    array_push($aPathTries,$aPath);
    array_push($aPathTries,"Matched");
    $bFound=true;
    return $aPath;
  }else{
    array_push($aPathTries,$sFilePath.$aPathData[2]."/".$aPathData[3]."/package/".$sFile);
  }
  
  //Symbian SERIES Check
  if((stripos($header, "Symbian")!==false)||(stripos($aPathData[2], "Symbian")!==false)){
  	$aPathReturn = symbianVersionCheck($header);
	if($aPathReturn){
		array_push($aPathTries,$aPathReturn);
		return $aPathReturn;
	}
  }
  
  //Generic Build
  $aPath = $sFilePath."genericos/";
  $sCheck = checkPath($aPath,str_replace(" ","",strtolower($aPathData[2])));
  if ($sCheck != ""){
    $aPath .= $sCheck."/".$sFile;
    if (file_exists($aPath)){
      array_push($aPathTries,$aPath);
      return $aPath;
    }
  }else{
    array_push($aPathTries,$aPath.str_replace(" ","",strtolower($aPathData[2]))."/package/".$sFile);
  }
  
  //Generic Java
  return "No path";
}

function checkPath($path,$find){
  if (!$find) { return ""; }
  if(!is_dir($path)){ return ""; }
  //Get array of all directories
  if ($handle = opendir($path)){
    while (false !== ($file = readdir($handle))) {
      if ($file!="." && $file!=".."){
        $aDir[] = $file;
      }
    }
    closedir($handle);
  }
  
  //Try to match folder name to data
  foreach($aDir as $dir){
    
    $mFind = preg_replace("/[^a-z\d]/i", "", strval($find));
    $mDir = preg_replace("/[^a-z\d]/i", "", strval($dir));
	
    if (strtolower($mFind) == strtolower($mDir)){
      return strtolower($dir);
    }
  }
  //Return directory if found or ""
  return "";
}
function findRemoveEnd($string,$find){
	$series = "Series";
	$string = str_replace($series,"",$string); 
	$pos = stripos($string,$find);
	if($pos > 0){
		$string = substr($string,0,$pos);
	}
	return $series.$string;
}

function symbianLookup($sVersion){
	global $sFileName;
	$return = false;
	$tPath = strtolower("Symbian/".$sVersion."/package/");
	if (file_exists("Build/".$tPath.$sFileName.".sis")){
      $return = "sis";
    }elseif(file_exists("Build/".$tPath.$sFileName.".sisx")){
      $return = "sisx";
    }
	return $return;
}

function symbianVersionCheck($sHEADER){
	global $sFileName;
	$sVersion = substr($sHEADER,strpos($sHEADER,"Series"));
	$sVersion = substr($sVersion,0,strpos($sVersion," "));
	$sVersion = str_replace("/","E",$sVersion);
	$sVersion = str_replace(".","FP",$sVersion);
	
	if(stripos($sVersion, "E5")!==false){
		return "Build/Symbian/S60E5/Mobidex.sisx";
	}elseif(stripos($sVersion, "E3")!==false){
		return "Build/Symbian/S60E3/Mobidex.sisx";
	}elseif(stripos($sVersion, "E2")!==false){
		return "Build/Symbian/S60E2/Mobidex.sis";
	}
}
?>