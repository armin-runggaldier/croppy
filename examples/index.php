<?php

require '../src/Croppy.php';
require '../src/Exception.php';

use Croppy\Croppy;

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.jpg');
$croppy->cropresize(800, 600, true);
$croppy->save(__DIR__.'/output/image1.jpg');