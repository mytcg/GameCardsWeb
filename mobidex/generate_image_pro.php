<?php
function getExtension($str)
{
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}


// The file
$filename = $_GET['i'];
$ext = getExtension($filename);

// The settings
$iOrientation = $_GET['o'];
$iLeft = intval( $_GET['l'] ) * -1;
$iTop = intval( $_GET['t'] ) * -1;
$iScale = intval( $_GET['s'] );
$iRotate = intval( $_GET['r'] );
$sColor = $_GET['b'];

//print_r($_GET);die;

// Set a maximum height and width
if($iOrientation=='portrait')
{
	$width = 250;
	$height = 350;
}
else
{
	$width = 350;
	$height = 250;
}

// Content type
if($ext == 'jpg')
{
	header('Content-Type: image/jpeg');
}
else
{
	header('Content-Type: image/png');
}

// Resample
$image_p = imagecreatetruecolor($width, $height);
//$bgcolor = imagecolorallocate($image_p, 255, 85, 0);
//imagefill($image_p, 1, 1, $bgcolor);

$dir = '';
if($_SERVER['HTTP_HOST']!='localhost')
{
	$dir = 'http://'.$_SERVER['HTTP_HOST'].'/';
	$dir = $_SERVER['DOCUMENT_ROOT'].'/';
}

if($ext == 'jpg')
{
	$image = imagecreatefromjpeg($dir.$filename);
}
else
{
	$image = imagecreatefrompng($dir.$filename);
}

$color[0] = hexdec(substr($sColor,1,2));
$color[1] = hexdec(substr($sColor,3,2));
$color[2] = hexdec(substr($sColor,5,2));
$bgcolor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
//$rotated_image = imagerotate($image, $iRotate, $bgcolor);
$rotated_image = $image;
imagecopyresampled($image_p, $rotated_image, 0, 0, intval($iLeft/($iScale/100)), intval($iTop/($iScale/100)), $width, $height, intval($width/($iScale/100)), intval($height/($iScale/100)));

// Output
if($ext == 'jpg' || true)
{
	imagejpeg($image_p, null, 100);
}
else
{
	imagepng($image_p, null);
}

ImageDestroy($image_p);
ImageDestroy($image);
?>