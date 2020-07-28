<?php

ini_set('display_errors', 1);

require '../src/Croppy.php';
require '../src/Exception.php';

use Croppy\Croppy;

//unlink(__DIR__.'/output/testoutput.jpg');

$croppy = new Croppy();
$croppy->setSourcePath(__DIR__.'/images/image1.jpg');
$croppy->cropresize(800, 600, true);
$croppy->save(__DIR__.'/output/testoutput.jpg');
// $croppy->output();

// echo $croppy->getSourcePath();