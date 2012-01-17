<?php

/**
 * Creates an image resource from a url or path to a file of type jpg, gif or png.
 * 
 * @param string $img_url URL or pathname to the image file
 * @return image resource
 */
function myImageCreate($img_url) {
	$img = 0;
	if (eregi(".jpg$", $img_url) > 0) {
		$img = imagecreatefromjpeg($img_url);
	}
	elseif (eregi(".png$", $img_url) > 0) {
		$img = imagecreatefrompng($img_url);
	}
	elseif (eregi(".gif$", $img_url) > 0) {
		$img = imagecreatefromgif($img_url);
	}
	return $img;
}

//generate a random name for image file
function generateName() {
	$name = "";
	for ($i = 0; $i < 9; $i++) {
		$name = $name . rand(0,9);
	}
	return $name;
}

/**
 * Saves an image resource to a file.
 * 
 * @param resource $img The image to be saved.
 * @param string $img_url pathname to the destination file.
 * @return true iff save was successful
 */
function myImageSave($img, $img_url) {
	if (!is_resource($img)) {
		return false;
	}
	if (eregi(".jpg$", $img_url) > 0) {
		imagejpeg($img, $img_url);
		return true;
	}
	elseif (eregi(".png$", $img_url) > 0) {
		imagepng($img, $img_url);
		return true;
	}
	elseif (eregi(".gif$", $img_url) > 0) {
		imagegif($img, $img_url);
		return true;
	}
	return false;
}


?>
