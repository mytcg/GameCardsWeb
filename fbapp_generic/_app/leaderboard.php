<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

if($_GET['list']){
  $boardID = $_GET['list'];
  $query = "SELECT * FROM mytcg_leaderboards WHERE leaderboard_id = ".$boardID;
  $aQueries=myqu($query);
  $aList = myqu($aQueries[0]['lquery']);
  
  if(sizeof($aList) > 0){
    echo '<leaders>'.$sCRLF;
    echo $sTab.'<count>'.sizeof($aList).'</count>'.$sCRLF;
        $k = 0;
        foreach($aList as $leader){
          echo $sTab.'<leader_'.$k.'>'.$sCRLF;
		  echo $sTab.$sTab.'<facebook_id val="'.$leader['fbid'].'" />'.$sCRLF;
          echo $sTab.$sTab.'<username val="'.$leader['usr'].'" />'.$sCRLF;
          echo $sTab.$sTab.'<value val="'.$leader['val'].'" />'.$sCRLF;
          echo $sTab.'</leader_'.$k.'>'.$sCRLF;
          $k++;
        }
        echo '</leaders>'.$sCRLF;
  }
  exit;
}
?>