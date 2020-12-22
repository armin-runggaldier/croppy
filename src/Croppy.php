<?php

/*
 * @author: Armin Runggaldier
 * @version: 1.2
 * @github: https://github.com/armin-runggaldier/croppy/
 */

namespace Croppy\Croppy;

class Croppy {

	public const CROPSTART = 'start';
	public const CROPCENTER = 'center';
	public const CROPEND = 'end';
	public const OPACITYMAX = 127;

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
	private $backgroundColor = array(255, 255, 255);
	private $backgroundOpacity = 127; // percent
	private $jpegQuality = 80; // percent
	private $webpQuality = 80; // percent
	private $pngCompression = 6; // 0-9
	private $upScaleAllowed = false; // prevent image upscale

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
		$this->checkImageSource();

		$this->sourceType = exif_imagetype($this->sourcePath);

		if($this->checkImageExtension() === false) {
			throw new Exception(sprintf('Filetype %s is currently not supported. Supported types: %s!', $this->sourceType, implode(', ', $this->availableMimeTypes)));
		}
	}


	/**
	 * @param int $quality
	 * @return void
	 */
	public function setJpegQuality($quality) {
		if(is_int($quality) === false) {
			throw new Exception(sprintf('Given image quality %s is not a valid number!', $quality));
		} else {
			$this->jpegQuality = $quality;
		}
	}


	/**
	 * @param int $quality
	 * @return void
	 */
	public function setWebpQuality($quality) {
		if(is_int($quality) === false) {
			throw new Exception(sprintf('Given image quality %s is not a valid number!', $quality));
		} else {
			$this->webpQuality = $quality;
		}
	}


	/**
	 * @param int $compression
	 * @return void
	 */
	public function setPngCompression($compression) {
		if(is_int($compression) === false) {
			throw new Exception(sprintf('Given png quality %s is not a valid number!', $compression));
		} else {
			$this->pngCompression = $compression;
		}
	}


	/**
	 * @param int $compression
	 * @return void
	 */
	public function setUpScaleAllowed($upScaleAllowed) {
		$this->upScaleAllowed = $upScaleAllowed;
	}


	/**
	 * Return path of input source
	 * @return string
	 */
	public function getSourcePath() : string {
		return $this->sourcePath;
	}


	/**
	 * @param string $cropAlignmentX Croppy::CROPSTART | Croppy::CROPCENTER | Croppy::CROPEND
	 * @param string $cropAlignmentY Croppy::CROPSTART | Croppy::CROPCENTER | Croppy::CROPEND
	 * @return void
	 */
	public function setCropPosition($cropAlignmentX, $cropAlignmentY) {
		$this->cropAlignmentX = $cropAlignmentX;
		$this->cropAlignmentY = $cropAlignmentY;
	}


	/**
	 * Resize image and crop if requestet
	 * @param float $destinationWidth
	 * @param float $destinationHeight
	 * @param bool $crop
	 * @return boolean
	 * @throws Exception
	 */
	public function resize($destinationWidth, $destinationHeight, $crop = false) {
		$this->checkImageSource();

		$sourceImageDimensions = getimagesize($this->sourcePath);
		$sourceWidth = $sourceImageDimensions[0];
		$sourceHeight = $sourceImageDimensions[1];

		// check and prevent upscale
		if($this->upScaleAllowed === false && $crop === false && ($destinationWidth > $sourceWidth || $destinationHeight > $sourceHeight)) {
			return false;
		}

		// get new dimension size
		list($width, $height) = $this->calculateDimensions($sourceWidth, $sourceHeight, $destinationWidth, $destinationHeight, $crop);

		// allow crop upscale
		if($this->upScaleAllowed === false && $crop === true) {
			if($destinationWidth > $sourceWidth) {
				$sourceWidth = $destinationWidth;
			}
			if($destinationHeight > $sourceHeight) {
				$sourceHeight = $destinationHeight;
			}
		}

		// get crop position
		$x = 0;
		$y = 0;
		if($crop === true) {
			list($x, $y) = $this->calcuateCropPosition($width, $height, $destinationWidth, $destinationHeight);
		}

		$image = $this->createImageFromSource();
		if($crop === true) {
			$newImage = imagecreatetruecolor($destinationWidth, $destinationHeight);
		} else {
			$newImage = imagecreatetruecolor($width, $height);
		}

		// set transparent background
		$newImage = $this->setImageBackground($newImage);

		// image resample
		if($crop === true) {
			$resizedImage = imagecreatetruecolor($width, $height);

			$resizedImage = $this->setImageBackground($resizedImage);
			$color = imagecolorallocatealpha($resizedImage, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2], $this->backgroundOpacity);
			imagefill($resizedImage, 0, 0, $color);

			imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
			imagecopyresampled($newImage, $resizedImage, 0, 0, $x, $y, $width, $height, $width, $height);
		} else {
			$color = imagecolorallocatealpha($newImage, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2], $this->backgroundOpacity);
			imagefill($newImage, 0, 0, $color);

			imagecopyresampled($newImage, $image, 0, 0, $x, $y, $width, $height, $sourceWidth, $sourceHeight);
		}

		$this->image = $newImage;

		return true;
	}


	/**
	 * extends image area by filling up with backgroundColor
	 * @param float $destinationWidth
	 * @param float $destinationHeight
	 * @return boolean
	 * @throws Exception
	 */
	public function extend($destinationWidth, $destinationHeight) {
		$this->checkImageSource();

		$sourceImageDimensions = getimagesize($this->sourcePath);
		$sourceWidth = $sourceImageDimensions[0];
		$sourceHeight = $sourceImageDimensions[1];

		// get x, y positions
		$x = 0;
		$y = 0;
		list($x, $y) = $this->calcuateCropPosition($destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

		$image = $this->createImageFromSource();
		$newImage = imagecreatetruecolor($destinationWidth, $destinationHeight);

		$color = imagecolorallocatealpha($newImage, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2], $this->backgroundOpacity);

		try {
			imagefill($newImage, 0, 0, $color);
			imagesavealpha($newImage, true);
			imagealphablending($newImage, TRUE);
			imagecopy($newImage, $image, $x, $y, 0, 0, $sourceWidth, $sourceHeight); // $destinationWidth, $destinationHeight,
			$this->image = $newImage;
		} catch (Exception $ex) {
			throw new Exception('Image could not be processed! Error Message: '.$ex->getMessage());
			return false;
		}

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
	 * @throws Exception
	 */
	private function checkImageSource() {
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
	 * @param int $backgroundColorR R
	 * @param int $backgroundColorB B
	 * @param int $backgroundColorG G
	 * @param int $opacity 0-100%
	 * @return void
	 * @throws Exception
	 */
	public function setBackground($backgroundColorR, $backgroundColorB, $backgroundColorG, $opacity = 100) {
		$this->backgroundColor = array($backgroundColorR, $backgroundColorB, $backgroundColorG);
		$this->backgroundOpacity = $this->calculateOpacity($opacity);
	}


	private function calculateOpacity($opacity) {
		if($opacity < 0 || $opacity > 100) {
			throw new Exception(sprintf('Opacity must be between 0 - 100! %s give in!', $opacity));
		}

		$opacity = 100 - $opacity; // invert

		$realOpacity = $this::OPACITYMAX * $opacity / 100;

		return $realOpacity;
	}


	private function setImageBackground($image) {
		imagesavealpha($image, true);
		$transparentImage = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefill($image, 0, 0, $transparentImage);

		return $image;
	}


	/**
	 * Save image on $destinationPath
	 * @param $destinationPath
	 * @param bool $convertType
	 * @return bool
	 * @throws Exception
	 */
	public function save($destinationPath, $convertType = false) {
		$outputType = $this->sourceType;
		if($convertType !== false) { //  && in_array($convertType, $this->availableTypes)
			$fileExt = explode('.', $destinationPath);
			$fileExt = array_pop($fileExt);
			if($fileExt === 'jpg' || $fileExt === 'jpeg') {
				$outputType = IMAGETYPE_JPEG;
			} else if($fileExt === 'png') {
				$outputType = IMAGETYPE_PNG;
			} else if($fileExt === 'gif') {
				$outputType = IMAGETYPE_GIF;
			} else if($fileExt === 'webp') {
				$outputType = IMAGETYPE_WEBP;
			}

			// convert image
			imagealphablending($this->image, TRUE);
			if($this->sourceType !== $outputType) {
				$image = $this->image;
				$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
				imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
				imagealphablending($bg, TRUE);
				imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
				$this->image = $bg;
			}
			$this->sourceType = $outputType;
		}

		$dirname = dirname($destinationPath);
		if(is_dir($dirname) === false) {
			throw new Exception(sprintf('Directory %s does not exists!', $dirname));
		} else if(is_writable($dirname) === false) {
			throw new Exception(sprintf('Directory %s is not writable!', $dirname));
		}

		// IMAGETYPE_JPEG
		if($outputType === IMAGETYPE_JPEG) {
			$result = imagejpeg($this->image, $destinationPath, $this->jpegQuality);
		}

		// IMAGETYPE_PNG
		else if($outputType === IMAGETYPE_PNG) {
			$result = imagepng($this->image, $destinationPath, $this->pngCompression);
		}

		// IMAGETYPE_GIF
		else if($outputType === IMAGETYPE_GIF) {
			$result = imagegif($this->image, $destinationPath);
		}

		// IMAGETYPE_WEBP
		else if($outputType === IMAGETYPE_WEBP) {
			$result = imagewebp($this->image, $destinationPath, $this->webpQuality);
		}

		else {
			$result = false;
		}

		return $result;
	}


	/**
	 * Stream image
	 * @param bool $convertType
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
			imagejpeg($this->image, null, $this->jpegQuality);
		}

		// IMAGETYPE_PNG
		else if($outputType === IMAGETYPE_PNG) {
			imagepng($this->image, null, $this->pngCompression);
		}

		// IMAGETYPE_GIF
		else if($outputType === IMAGETYPE_GIF) {
			imagegif($this->image);
		}

		// IMAGETYPE_WEBP
		else if($outputType === IMAGETYPE_WEBP) {
			imagewebp($this->image, null, $this->webpQuality);
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
			$newWidth = $sourceWidth * $height / $sourceHeight;
		}

		return array($newWidth, $newHeight);
	}


	private function calcuateCropPosition($sourceWidth, $sourceHeight, $width, $height) {
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

		/*$x = $x * -1;
		$y = $y * -1;*/

		return array($x, $y);
	}

}
