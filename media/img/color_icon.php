<?php

/*
* Creates a square image with one color
* Params:
*   w - width, in pixels
*   h - height, in pixels
*   c - color, in hex (ex: FF0000)
*/

header("Content-type: image/png"); 

$width = 100;
$height = 100;
$r = 0;
$g = 0;
$b = 255;
//$alpha = 0;

if(isset($_GET['w'])) $width = $_GET['w'];
if(isset($_GET['h'])) $height = $_GET['h'];
if(isset($_GET['c'])) {
	$c = $_GET['c'];
	$r = hexdec($c{0}.$c{1});
	$g = hexdec($c{2}.$c{3});
	$b = hexdec($c{4}.$c{5});
}

$im = imagecreatetruecolor($width, $height);

$color = ImageColorAllocate($im, $r, $g, $b);
$white = ImageColorAllocate($im, 255, 255, 255);

imagefilltoborder($im, 0, 0, $white, $white);

imagecolortransparent($im, $white);

$cx = ceil(($width / 2));
$cy = ceil(($height / 2));
ImageFilledEllipse($im, $cx, $cy, ($width-1), ($height-1), $color);

// send the new PNG image to the browser
imagepng($im); 
 
// destroy the reference pointer to the image in memory to free up resources
imagedestroy($im); 

?>