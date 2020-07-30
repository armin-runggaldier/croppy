<?php

/*
 * @author: Armin Runggaldier
 * @version: 1.0
 */

namespace Croppy;

class Croppy {

	public const CROPSTART = 'start';
	public const CROPCENTER = 'center';
	public const CROPEND = 'end';

	private $availableTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP, IMAGETYPE_WEBP];
	private $availableMimeTypes = [
		IMAGETYPE_JPEG => 'image/jpeg',
		IMAGETYPE_PNG => 'image/png',
		IMAGETYPE_GIF => 'image/gif',
		IMAGETYPE_WEBP => 'image/webp',
	];
	private $sourceType = null;
	private $sourcePath = null;
	private $cropAlignmentX = self::CROPCENTER;
	private $cropAlignmentY = self::CROPCENTER;

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


	public function setCropPosition($cropAlignmentX, $cropAlignmentY) {
		$this->cropAlignmentX = $cropAlignmentX;
		$this->cropAlignmentY = $cropAlignmentY;
	}


	/**
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return void
	 */
	public function resize($width, $height, $crop = false) {
		$this->checkImageSource();

		$sourceImageDimensions = getimagesize($this->sourcePath);
		$sourceWidth = $sourceImageDimensions[0];
		$sourceHeight = $sourceImageDimensions[1];

		// get new dimension size
		list($width, $height) = $this->calculateDimensions($sourceWidth, $sourceHeight, $width, $height, $crop);

		// get crop position
		$x = 0;
		$y = 0;
		if($crop === true) {
			list($x, $y) = $this->calcuateCroPosition($sourceWidth, $sourceHeight, $width, $height);
		}
		var_dump($x);
		var_dump($y);

		$image = $this->createImageFromSource();
		$newImage = imagecreatetruecolor($width, $height);

		// TRANSPARENT BACKGROUND
		/*$color = imagecolorallocatealpha($newImage, 0, 0, 0, 127); //fill transparent back
		imagefill($newImage, 0, 0, $color);
		imagesavealpha($newImage, true);*/

		// image resample
		imagecopyresampled($newImage, $image, $x, $y, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

		$this->image = $newImage;
	}


	/**
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return bool
	 */
	/*public function cropresize($width, $height) {
	}*/


	/**
	 * Resize image and crop if requestet
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 * @return void
	 */
	/*public function crop($width, $height, $crop = false) {

	}*/


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
	 * @throws Exception
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


	private function createImageFromSource() {
		// IMAGETYPE_JPEG
		if($this->sourceType === IMAGETYPE_JPEG) {
			$image = imagecreatefromjpeg($this->sourcePath);
		}

		// IMAGETYPE_PNG
		else if($this->sourceType === IMAGETYPE_PNG) {
			$image = imagecreatefrompng($this->sourcePath);
		}

		// IMAGETYPE_GIF
		else if($this->sourceType === IMAGETYPE_GIF) {
			$image = imagecreatefromgif($this->sourcePath);
		}

		// IMAGETYPE_WEBP
		else if($this->sourceType === IMAGETYPE_WEBP) {
			$image = imagecreatefromwebp($this->sourcePath);
		}

		else {
			$image = false;
		}

		return $image;
	}


	/**
	 * Save image on $destinationPath
	 * @param $destinationPath
	 * @return bool
	 * @throws Exception
	 */
	public function save($destinationPath, $convertType = false) {
		$outputType = $this->sourceType;
		if($convertType !== false && in_array($convertType, $this->availableTypes)) {
			$outputType = $convertType;
		}

		$dirname = dirname($destinationPath);
		if(is_dir($dirname) === false) {
			throw new Exception(sprintf('Directory %s does not exists!', $dirname));
		} else if(is_writable($dirname) === false) {
			throw new Exception(sprintf('Directory %s is not writable!', $dirname));
		}

		// IMAGETYPE_JPEG
		if($outputType === IMAGETYPE_JPEG) {
			$result = imagejpeg($this->image, $destinationPath);
		}

		// IMAGETYPE_PNG
		else if($outputType === IMAGETYPE_PNG) {
			$result = imagepng($this->image, $destinationPath);
		}

		// IMAGETYPE_GIF
		else if($outputType === IMAGETYPE_GIF) {
			$result = imagegif($this->image, $destinationPath);
		}

		// IMAGETYPE_WEBP
		else if($outputType === IMAGETYPE_WEBP) {
			$result = imagewebp($this->image, $destinationPath);
		}

		else {
			$result = false;
		}

		return $result;
	}


	/**
	 * Stream image
	 * @return void
	 */
	public function output($convertType = false) {
		$outputType = $this->sourceType;
		if($convertType !== false && in_array($convertType, $this->availableTypes)) {
			$outputType = $convertType;
		}

		// set the content type header and output the image by mime type
		header('Content-Type: '.$this->availableMimeTypes[$outputType]);

		// IMAGETYPE_JPEG
		if($outputType === IMAGETYPE_JPEG) {
			imagejpeg($this->image);
		}

		// IMAGETYPE_PNG
		else if($outputType === IMAGETYPE_PNG) {
			imagepng($this->image);
		}

		// IMAGETYPE_GIF
		else if($outputType === IMAGETYPE_GIF) {
			imagegif($this->image);
		}

		// IMAGETYPE_WEBP
		else if($outputType === IMAGETYPE_WEBP) {
			imagewebp($this->image);
		}

		// Free up memory
		imagedestroy($this->image);
	}


	private function calculateDimensions($sourceWidth, $sourceHeight, $width, $height, $crop) {
		$newHeight = $sourceHeight * $width / $sourceWidth;
		$newWidth = $width;

		// recalculare width without crop
		if($newHeight > $height && $crop === false) {
			$newHeight = $height;
			$newWidth = $sourceWidth * $height / $sourceHeight;
		}

		// recalculare width when crop
		else if($newHeight < $height && $crop === true) {
			$newHeight = $height;
			// $newWidth = $sourceWidth * $height / $sourceHeight;
		}

		return array($newWidth, $newHeight);
	}


	private function calcuateCroPosition($sourceWidth, $sourceHeight, $width, $height) {
		$x = 0;
		$y = 0;
		$overflowX = $sourceWidth - $width;
		$overflowY = $sourceHeight - $height;

		if($overflowX > 0) {
			if($this->cropAlignmentX === 'center') {
				$x = $overflowX / 2;
			} else if($this->cropAlignmentX === 'end') {
				$x = $overflowX;
			}
		}

		if($overflowY > 0) {
			if($this->cropAlignmentY === 'center') {
				$y = $overflowY / 2;
			} else if($this->cropAlignmentY === 'end') {
				$y = $overflowY;
			}
		}

		$x = $x * -1;
		$y = $y * -1;

		return array($x, $y);
	}

}


