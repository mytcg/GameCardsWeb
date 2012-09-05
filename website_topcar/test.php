<?php

$file = $_SERVER['DOCUMENT_ROOT']."var/errorlog.log";
  if(!file_exists($file)){
    $file = $_SERVER['DOCUMENT_ROOT']."mytcg/var/errorlog.log";
  }
  echo($file);
?>