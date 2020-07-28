<?php

/*
 * @author: Armin Runggaldier
 * @version: 1.0
 */

namespace Croppy;

class Croppy {


	private $availableTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP, IMAGETYPE_WEBP];
	private $sourceType = null;
	private $sourcePath = null;

	private $image = null;

	public function __construct() {

	}


	/**
	 * Defines the path of the source file
	 * @param string $sourcePath
	 * @throws Exception
	 */
	public function setSourcePath($sourcePath) : void {
		$this->sourcePath = $sourcePath;
		$this->sourceType = exif_imagetype($this->sourcePath);

		if($this->checkImageExtension() === false) {
			throw new Exception(sprintf('Filetype %s is currently not supported. Supported types: %s!', $this->sourceType, $this->availableTypes));
		}
	}


	/**
	 * Return path of input source
	 * @return string
	 */
	public function getSourcePath() : string {
		return $this->sourcePath;
	}


	/**
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return bool
	 */
	public function cropresize($width, $height, $crop = false) {
		$this->checkImageSource();

		$imageDimensions = getimagesize($this->sourcePath);
		$image = $this->createImageFromSource();

		$newImage = imagecreatetruecolor($width, $height);

		//TRANSPARENT BACKGROUND
		/*$color = imagecolorallocatealpha($newImage, 0, 0, 0, 127); //fill transparent back
		imagefill($newImage, 0, 0, $color);
		imagesavealpha($newImage, true);*/

		// image resample
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $imageDimensions[0], $imageDimensions[1]);

		$this->image = $newImage;
		return true;
	}


	/**
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return bool
	 */
	public function resize($width, $height, $crop = false) {


		return true;
	}


	/**
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return bool
	 */
	public function crop($width, $height, $crop = false) {

		return true;
	}


	private function createImageFromSource() {
		$image = imagecreatefromjpeg($this->sourcePath);
		return $image;
	}


	public function save($destinationPath) {
		$dirname = dirname($destinationPath);
		if(is_writable($dirname) === false) {
			throw new Exception(sprintf('Directory %s is not writable!', $dirname));
		}

		$result = imagejpeg($this->image, $destinationPath);
		return $result;
	}

	public function output() {
		// Set the content type header - in this case image/jpeg
		header('Content-Type: image/jpeg');

		// Output the image
		imagejpeg($this->image);

		// Free up memory
		imagedestroy($this->image);
	}


	/**
	 * Check file extension
	 * @return bool
	 */
	private function checkImageExtension()  {
		if(in_array($this->sourceType, $this->availableTypes)) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Check file extension
	 * @return bool
	 */
	private function checkImageSource()  {
		if(isset($this->sourcePath) === false || empty($this->sourcePath) === true) {
			throw new Exception('Source file was not set. Please use $obj->setSourcePath() first!');
		} else if(is_file($this->sourcePath) === false) {
			throw new Exception(sprintf('File %s not found!', $this->sourcePath));
		} else {
			return true;
		}
	}


}


