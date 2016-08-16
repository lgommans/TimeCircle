#!/usr/bin/env php
<?php 

$time = 4; // seconds
$fps = 20; // frames per second

$frames = $fps * $time;

if ($argc < 2) {
	echo "Usage:\n";
	echo "  timecircle image1 image2 image3 ...\n";
	echo "This will create a subdirectory timecircle/ and place output images\n";
	echo "there. These only need to be concatenated into a gif, which can be\n";
	echo "done with imagemagick:\n";
	echo "  convert timecircle/*.jpg my.gif\n";
	echo "\n";
	echo "Any other options are currently not implemented.\n";
	echo "\n";
	echo "Tip: convert existing, conventional timelapse gifs to image sequences\n";
	echo "using: convert -coalesce your.gif %d.jpg\n";
	exit;
}

if (!file_exists('timecircle')) {
	mkdir("timecircle");
}

$in_imgs = [];
for ($i = 1; $i < $argc; $i++) {
	$in_imgs[] = imagecreatefromstring(file_get_contents($argv[$i]));
}

$width = imagesx($in_imgs[0]);
$height = imagesy($in_imgs[0]);

$out_imgs = [];
for ($i = 0; $i < $frames; $i++) {
	$out_imgs[] = imagecreatetruecolor($width, $height);
}

for ($i = 0; $i < $frames; $i++) {
	for ($x = 0; $x < $width; $x++) {
		for ($y = 0; $y < $height; $y++) {
			// Determine which image to get a pixel from
			$angleOfPixel = radtodeg(atan2($width / 2 - $x, $height / 2 - $y)) + 360; // Angle between ($x,$y) and ($width/2,$height/2).
			$angleModifier = $i / $frames * 360; // The time of day at the pixel is shifted by the current frame number. Determine by how many degrees we should shift.
			$timeOfDay = (($angleOfPixel + $angleModifier) % 360) / 360; // Fraction from 0 to 1
			$source_img = floor($timeOfDay * count($in_imgs));

			// Get the rgb value at that pixel
			$rgb = imagecolorat($in_imgs[$source_img], $x, $y);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			// Allocate (or find) the rgb value in the new image
			$color = imagecolorexact($out_imgs[$i], $r, $g, $b);
			if ($color == -1) {
				$color = imagecolorallocate($out_imgs[$i], $r, $g, $b);
			}

			imagesetpixel($out_imgs[$i], $x, $y, $color);
		}
	}
	imagejpeg($out_imgs[$i], "timecircle/$i.jpg", 78);
	echo round($i / $frames * 100) . "% ";
}
echo "done.\n";

function radtodeg($rad) {
	return $rad / pi() * 180;
}

