<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:12
 */

if( !function_exists('imageResizeByWidth')){
	function imageResizeByWidth($originalImage, $move_to_path, $toWidth)
	{

		// Get the original geometry and calculate scales
		list($width, $height, $type) = getimagesize($originalImage);
		$ImageType = ($type == 1 ? "gif" : ($type == 2 ? "jpeg" : ($type == 3 ? "png" : false)));

		$CreateFunction = "imagecreatefrom" . $ImageType;
		$OutputFunction = "image" . $ImageType;

		$scale = $toWidth / $width;
		$new_width = $toWidth;
		$new_height = $height * $scale;

		// Resize the original image
		$imageResized = imagecreatetruecolor($new_width, $new_height);
		$imageTmp = $CreateFunction($originalImage);
		imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		$OutputFunction($imageResized, $move_to_path);
	}
}

if( !function_exists('imageResizeByHeight')){
	function imageResizeByHeight($originalImage, $move_to_path, $toHeight, $maxWidth = 0)
	{

		// Get the original geometry and calculate scales
		list($width, $height, $type) = getimagesize($originalImage);
		$ImageType = ($type == 1 ? "gif" : ($type == 2 ? "jpeg" : ($type == 3 ? "png" : false)));

		$CreateFunction = "imagecreatefrom" . $ImageType;
		$OutputFunction = "image" . $ImageType;

		$scale = $toHeight / $height;
		$new_width = floor($width * $scale);
		$new_height = $toHeight;
		if($maxWidth>0){
			if($new_width>$maxWidth){
				$new_width = $maxWidth;
			}
		}
		// Resize the original image
		$imageResized = imagecreatetruecolor($new_width, $new_height);
		$imageTmp = $CreateFunction($originalImage);
		imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		$OutputFunction($imageResized, $move_to_path);
	}
}