![Croppy](images/logo.svg)
# Croppy - PHP Framework for image manipulation
Version: 1.1

## Features
- image resize 
- image crop
- supported formats: .jpeg, .jpg, .png, .gif, .webp
- image quality management
- image save to filesystem or output
- designed for PHP 7, but works also with PHP 5.6 (untestet)

## Installation
Croppy is actually only available trough [Composer](https://getcomposer.org) (recommended) or direct download.

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

<br>

## Planned features for further releases:
- image type conversion
- image ratio resize

## Changelog version 1.1 (19.09.2020):
- image quality settings (jpeg, png and webp)

If you are missing a feature, please create a pull request :)

<br>

Made with ❤ in _South Tyrol_ by **Armin Runggaldier**
