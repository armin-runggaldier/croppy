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
$croppy->resize(900, 600, true);
$croppy->save(__DIR__.'/output/testoutput2.jpg');

$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPCENTER);
$croppy->resize(700, 700, true);
$croppy->save(__DIR__.'/output/testoutput3.jpg');

$croppy->setCropPosition($croppy::CROPEND, $croppy::CROPCENTER);
$croppy->resize(700, 700, true);
$croppy->save(__DIR__.'/output/testoutput4.jpg');

/* vertical */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image3.jpg');
$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPCENTER);
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/testoutput-vert1.jpg');

$croppy->setSourcePath(__DIR__.'/images/image3.jpg');
$croppy->resize(800, 600);
$croppy->save(__DIR__.'/output/testoutput-vert.jpg');

$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPSTART);
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/testoutput-vert2.jpg');

$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPEND);
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/testoutput-vert3.jpg');

/* transparent */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.png');
$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPCENTER);
$croppy->setBackground(255, 255, 255, 60);
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/testoutput.png');

/* gif */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.gif');
//$croppy->setBackground(255, 255, 255, 60);
$croppy->resize(300, 600, true);
$croppy->save(__DIR__.'/output/testoutput.gif');

/* webp */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.webp');
//$croppy->setBackground(255, 255, 255, 60);
$croppy->resize(300, 600, true);
$croppy->save(__DIR__.'/output/testoutput.webp');


// echo $croppy->getSourcePath();