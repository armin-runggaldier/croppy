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
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return bool
	 */
	public function cropresize($width, $height, $crop = false) {
		$this->checkImageSource();
		$this->createImage();

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


	private function createImage() {
		$this->image = imagecreatefromjpeg($this->sourcePath);
	}


	public function save($destinationPath) {
		imagejpeg($this->image, $destinationPath);

		return true;
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
		} else {
			return true;
		}
	}


}


