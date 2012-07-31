<?php
Header ("Content-type: image/gif");

/*$textfile = "quote.txt";
$quotes = array();
if(file_exists($textfile)){
   $quotes = file($textfile);
   srand ((float) microtime() * 10000000);
   $string = '-'.$quotes[array_rand($quotes)];
   $string = substr($string,0,strlen($string)-2);
}
else{
   $string = "No 'Quote' available at this time.";
}*/

$string = "Relaxxx - this is text in an image. First step done!";

$font = 2;
$width = ImageFontWidth($font) * strlen($string);
$height = ImageFontHeight($font);
$im = ImageCreate($width,$height);

$x = imagesx($im)-$width ;
$y = imagesy($im)-$height;

$background_color = imagecolorallocate ($im, 242, 242, 242); //white background
$text_color = imagecolorallocate ($im, 0, 0,0);//black text
$trans_color = $background_color;//transparent colour

imagecolortransparent($im, $trans_color);
imagestring ($im, $font, $x, $y, $string, $text_color);

imagegif($im);
ImageDestroy($im);
?>