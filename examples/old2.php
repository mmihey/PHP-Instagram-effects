<?php

require '../src/Image/Filter.php';

$image = imagecreatefromjpeg(isset($argv[1]) ? $argv[1] : "example.jpg");

$effects = (new Filter($image))->old2();

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
