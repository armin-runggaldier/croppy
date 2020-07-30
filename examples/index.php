<?php

ini_set('display_errors', 1);

require '../src/Croppy.php';
require '../src/Exception.php';

use Croppy\Croppy;

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.jpg');
$croppy->resize(800, 600, false);
$croppy->save(__DIR__.'/output/testoutput1.jpg');

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image2.jpg');
$croppy->setCropPosition($croppy::CROPSTART, $croppy::CROPCENTER);
$croppy->resize(700, 700, true);
$croppy->save(__DIR__.'/output/testoutput2.jpg');
$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPCENTER);
$croppy->resize(700, 700, true);
$croppy->save(__DIR__.'/output/testoutput3.jpg');
$croppy->setCropPosition($croppy::CROPEND, $croppy::CROPCENTER);
$croppy->resize(700, 700, true);
$croppy->save(__DIR__.'/output/testoutput4.jpg');

// echo $croppy->getSourcePath();