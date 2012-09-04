<?php
require_once("conn.php");

function myqu($sQuery){
	global $db;
  	$aOutput=array();
  	$pattern = '/INSERT/i';
  
	$aLink=mysqli_connect($db["host"],$db["username"],$db["password"],$db["database"]);
	$sQuery=str_replace("&nbsp;","",$sQuery);
  
	if($aResult=@mysqli_query($aLink, $sQuery))
	{
		//If insert - return last insert id
		if(preg_match($pattern, $sQuery)){
			$mp = mysqli_insert_id($aLink);
			@mysqli_free_result($aResult);
      		mysqli_close($aLink);
			return $mp;
		}
    //Else build return array
		while ($aRow=@mysqli_fetch_array($aResult,MYSQL_BOTH)){
			$aOutput[]=$aRow;
		}
		return $aOutput;
	}
  else{
    die("[".date("Y-m-d H:i:s")."] Err:".mysqli_errno($aLink)." - ".mysqli_error($aLink)." - ".$_SERVER['PHP_SELF']." - ".$sQuery);
    @mysqli_free_result($aResult);
    mysqli_close($aLink);
  }
}
?>