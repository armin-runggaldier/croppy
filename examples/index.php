<?php

use Croppy\Croppy;

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.jpg');
$croppy->resize(800, 600, true);