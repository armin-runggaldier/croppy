![Croppy](images/logo.svg)
# Croppy - PHP Framework for image manipulation
Version: 1.2  
PHP Version: php >= 7.1

## Features
- image resize 
- image crop
- extend image area
- image conversion
- supported formats: .jpeg, .jpg, .png, .gif, .webp
- image quality management
- image save to filesystem or output
- designed for PHP >= 7.1

## Installation
Croppy is actually available trough [Composer](https://getcomposer.org) (recommended) or direct download.

To use [Composer](https://getcomposer.org), just add this line to your `composer.json` file:
```json
"croppy/croppy": "~1.1"
```

and run the follow command:

```sh
composer install
```

You can also run the follow command in the terminal (you must be in the project directory):

```sh
composer require croppy/croppy
```

## Usage

**Basic usage:**
```php
use Croppy\Croppy\Croppy;
use Croppy\Croppy\Exception;

require 'path/to/src/Croppy.php';
require 'path/to/src/Exception.php';

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/path/to/image.jpg');
$croppy->setJpegQuality(60);
$croppy->resize(700, 700, false); // width, height, crop
$croppy->save(__DIR__.'/path/to/destination.jpg');
```

**Resize, crop and output:**
```php
use Croppy\Croppy\Croppy;
use Croppy\Croppy\Exception;

require 'path/to/src/Croppy.php';
require 'path/to/src/Exception.php';

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/path/to/image.png');
$croppy->setPngCompression(6);
// set crop position x, y 
$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPSTART); // $croppy::CROPSTART | $croppy::CROPCENTER | $croppy::CROPEND
// set background color
$croppy->setBackground(255, 255, 255, 100); // R, G, B, opacity
$croppy->resize(700, 700, true); // width, height, crop
$croppy->output();

// get path of input file
echo $croppy->getSourcePath();
```

## Documentation
**setSourcePath** `(string $sourcePath) : void`  
Defines the path of the source image, can be relative or absolute

**setJpegQuality** `(int $quality = 80) : void`  
Defines quality for JPEG images (0 - 100), 100 best quality

**setWebpQuality** `(int $quality = 80) : void`  
Defines quality for WEBP images (0 - 100), 100 best quality

**setPngCompression** `(int $compression = 6) : void`  
Defines quality for PNG images (0 - 9), 0 means no compression

**setUpScaleAllowed** `(bool $upScaleAllowed = false) : void`  
Deinfes if images should be scaled up (distort image), default is false

**getSourcePath** `() : string`  
Returns the path to the source image. It could return a relative or absolute path (depends on input)

**setCropPosition** `($cropAlignmentX = Croppy::CROPCENTER, $cropAlignmentY = Croppy::CROPCENTER) : void`  
Defines the crop position of the image. `$crop` flag must be set to true.  
$cropAlignmentX defines the horizontal alignment, $cropAlignmentY defines the vertical alignment. Possible values: Croppy::CROPSTART, Croppy::CROPCENTER, Croppy::CROPEND

**setBackground** `($backgroundColorR, $backgroundColorB, $backgroundColorG, $opacity = 100) : void`  
Set background color, which is always set in `resize` and `extend` method. Actually Croppy only supports RGB. `$opacity` defines the opacity of the background color (100 is complete transparent).

**resize** `(float $destinationWidth, float $destinationHeight, bool $crop = false) : bool`  
Resize image to the given `$destinationWidth` and `$destinationHeight`. If `$crop` is set to true, the image will be cropped. If `$crop` is set to false, the image will be adjusted (max width and max height).  

**extend** `(float $destinationWidth, float $destinationHeight) : bool`  
Extends image area and fill background with `$backgroundColor`. Could also used for crop image without resize.

**save** `($destinationPath, $convertType = false) : bool`  
Writes the generated image to the defined `$destinationPath`.  
`$convertType` enables the conversion of the image type. The destination image type will be identify from the filename in `$destinationPath` (ex. path/to/image.webp will convert the image to WEBP). Return true on success or false on failure.

**output** `($convertType = false) : void`  
Streams the image direct to output and set `Content-Type` header.  
`$convertType` enables the conversion of the image type. The destination image type will be identify from the filename in `$destinationPath` (ex. path/to/image.webp will convert the image to WEBP). Return true on success or false on failure.

<br>

## Planned features for further releases:
- image ratio resize

## Changelog version 1.2 (31.12.2020):
- image upscale definiton
- extend image area and fill with background color
- image type conversion (for example .png to .jpg)

## Changelog version 1.1 (19.09.2020):
- image quality settings (jpeg, png and webp)

If you are missing a feature, please create a pull request :)

<br>

Made with ❤ in _South Tyrol_ by **Armin Runggaldier**
