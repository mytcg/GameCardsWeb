<?php
echo 'tedt';
die();

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
session_start();

require_once('../common/SimpleImage.php');

$userID = $_SESSION['user']['user_id'];
$cardID = $_SESSION['card_id'];
$savePath = "";
$image = new SimpleImage();

if($_SESSION['action']=="add"){
  $savePath = 'img/'.$_SESSION['ruid'];
}

if($_SESSION['action']=="edit"){
  $savePath = 'img/cards/'.$cardID;
}

if($_FILES['front']){
  $img = $_FILES['front'];
  $ext = "_front.png";
}
if($_FILES['back']){
  $img = $_FILES['back'];
  $ext = "_back.png";
}

$image->load($img['tmp_name']);

$width = $image->getWidth();
$height = $image->getHeight();

if($width > $height){
  $image->resizeToWidth(250);
  $image->setHeight(350);
}else{
  $image->resizeToHeight(350);
}
$image->save($savePath.$ext);

if($_FILES['front']){
  if($width >= $height){
    $image->resizeToWidth(64);
    $image->setHeight(90);
  }else{
    $image->resizeToHeight(90);
  }
  $image->save($savePath."_thumb.png");
}
$image = null;

echo($savePath.$ext);

exit;

?>