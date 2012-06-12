<?php
/**
 * Create the Card Image
 *
 * Receive information fields and their positions via post
 *
 */



function getExtension($str)
{
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}




// get information fields

$aFields = array();

if(isset($_GET))
{
	$orientation = $_GET['orienation']; // 1-portrait, 2-landscape
	$image_file = $_GET['file'];
	$ext = getExtension($image_file);
	
	if($orientation == '1')
	{
		$width = 250;
		$height = 350;
	}
	else
	{
		$width = 350;
		$height = 250;
	}
	
	if(isset($_GET['fields']))
	{
		if(strlen($_GET['fields']) > 0)
		{
			$fields = $_GET['fields'];
			//fatal_error($fields);
			$fields = explode('^!^', $fields);
			foreach($fields as $field)
			{
				$info = explode('|', urldecode($field));
				$aFields[] = $info;
			}
		}
	}
}
else
{
	fatal_error('Error: No information specified.');
}


if($ext == 'jpg' || true)
{
	$mime_type 				= 'image/jpg' ;
	
	$extension 				= '.jpg' ;
}
elseif($ext == 'png')
{
	$mime_type 				= 'image/png' ;	
	$extension 				= '.png' ;}

$s_end_buffer_size 	= 4096 ;



// check for GD support
if(!function_exists('ImageCreate')){
	fatal_error('Error: Server does not support PHP image generation') ;
}

		$image = null;
	
		if($ext == 'jpg')
		{
			$image = imagecreatefromjpeg($image_file);
		}
		elseif($ext == 'png')
		{
			$image = imagecreatefrompng($image_file);
		}
	
				
		if(!image)
		{
			fatal_error('Error: The server could not create this image.') ;
		}		
				
		// allocate colors and measure final text position		
		$font_color = ImageColorAllocate($image, $font_rgb['red'], $font_rgb['green'], $font_rgb['blue']) ;		
				
		$image_width = imagesx($image);		
				
		//$put_text_x = $image_width - $text_width - ($image_width - $x_finalpos);
		$x = 0;
		//$put_text_y = $y_finalpos;
		$y = 0 + $text_height;		
				
		// Write the field labels text
		
		
		if(sizeof($aFields) > 0 && !isset($_GET['upload']))
		{
			foreach($aFields as $field)
			{
				
				$font_size = $field[8];
				$text = rawurldecode($field[2]);
				$font_size = $field[8];
				$font_color = "#".$field[9];
				$font_rgb = hex_to_rgb($font_color);
				$font_file = "fonts/".strtolower($field[7]);
				
				
				
				
				// check font availability;
				
				if(!is_readable($font_file))
				{
					$font_file = "fonts/arial.ttf";
					//fatal_error('Error: Missing font: '.$font_file) ;
				}
				
				
				
				
				// create and measure the text
				
				$font_rgb = hex_to_rgb($font_color) ;
				
				$box = @ImageTTFBBox($font_size,0,$font_file,$text) ;
				
				$font_color = ImageColorAllocate($image, $font_rgb['red'], $font_rgb['green'], $font_rgb['blue']);
				
				$text_width = abs($box[2]-$box[0]);
				
				$text_height = abs($box[5]-$box[3]);
				
				
				
				
				
				$x = $field[3];
				
				$y = $field[4] + $field[6]; //$text_height;
				
				
				
				imagettftext($image, $font_size, 0, $x, $y, $font_color, $font_file, $text);
			}
		}
				
		
		
header('Content-type: ' . $mime_type) ;

if($ext == 'jpg' || true)
{
	imagejpeg($image, null, 100) ;
}
//elseif($ext == 'png')
else
{
	ImagePNG($image) ;
}



ImageDestroy($image) ;
exit ;


/*
	attempt to create an image containing the error message given. 
	if this works, the image is sent to the browser. if not, an error
	is logged, and passed back to the browser as a 500 code instead.
*/
function fatal_error($message)
{
	// send an image
	if(function_exists('ImageCreate'))
	{
		$width = ImageFontWidth(5) * strlen($message) + 10 ;
		$height = ImageFontHeight(5) + 10 ;
		if($image = ImageCreate($width,$height))
		{
			$background = ImageColorAllocate($image,255,255,255) ;
			$text_color = ImageColorAllocate($image,0,0,0) ;
			ImageString($image,5,5,5,$message,$text_color) ;    
			header('Content-type: image/png') ;
			ImagePNG($image) ;
			ImageDestroy($image) ;
			exit ;
		}
	}

	// send 500 code
	header("HTTP/1.0 500 Internal Server Error") ;
	print($message) ;
	exit ;
}


/* 
	decode an HTML hex-code into an array of R,G, and B values.
	accepts these formats: (case insensitive) #ffffff, ffffff, #fff, fff 
*/    
function hex_to_rgb($hex) {
	// remove '#'
	if(substr($hex,0,1) == '#')
		$hex = substr($hex,1) ;

	// expand short form ('fff') color to long form ('ffffff')
	if(strlen($hex) == 3) {
		$hex = substr($hex,0,1) . substr($hex,0,1) .
				substr($hex,1,1) . substr($hex,1,1) .
				substr($hex,2,1) . substr($hex,2,1) ;
	}

	if(strlen($hex) != 6) {
	
		//fatal_error('Error: Invalid color "'.$hex.'"') ;
		
		//set default color to white
		$hex = 'ffffff';
	
	}

	// convert from hexidecimal number systems
	$rgb['red'] = hexdec(substr($hex,0,2)) ;
	$rgb['green'] = hexdec(substr($hex,2,2)) ;
	$rgb['blue'] = hexdec(substr($hex,4,2)) ;

	return $rgb ;
}
?>