<?php
  require_once("_phone/SimpleImage.php");
  
  echo(time()." - ".strtotime("2011-07-12 15:40:00"));
  /*
  $path = "img/cards/";
  $aDir = null;
  $iMod = 0;
  
  if($handle = opendir($path)){
    while (false !== ($file = readdir($handle))) {
      if (strpos($file,"front")){
        
        $tmp = str_replace("front","web",$file);
        
          $image = new SimpleImage();
          $image->load($path.$file);
          $image->resize(64,90);
          $image->save($path.$tmp);
          $image = null;
          echo($tmp." created<br>");

      }
    }
    closedir($handle);
    echo("All Done");
  }
   
   */
?>