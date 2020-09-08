<?php

require '../src/Croppy.php';
require '../src/Exception.php';

use Croppy\Croppy\Croppy;


/* resize */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image.jpg');
$croppy->resize(800, 600, false);
$croppy->save(__DIR__.'/output/output1.jpg');


/* with crop */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image.jpg');
$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPCENTER);
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/output2.jpg');


/* png */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image.png');
$croppy->setCropPosition($croppy::CROPCENTER, $croppy::CROPCENTER);
$croppy->setBackground(255, 255, 255, 60);
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/output1.png');


/* gif */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image.gif');
$croppy->setBackground(255, 255, 255, 100);
$croppy->resize(600, 600, true);
$croppy->save(__DIR__.'/output/output2.gif');


/* webp */
$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image.webp');
$croppy->resize(800, 600, true);
$croppy->save(__DIR__.'/output/output1.webp');
