<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$sourcePath = $root.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'favicon.svg';
$publicPath = $root.DIRECTORY_SEPARATOR.'public';

if (! class_exists(Imagick::class)) {
    fwrite(STDERR, "Imagick is required to generate favicon assets.\n");
    exit(1);
}

$sizes = [
    'favicon-16x16.png' => 16,
    'favicon-32x32.png' => 32,
    'favicon-48x48.png' => 48,
    'favicon-96x96.png' => 96,
    'apple-touch-icon.png' => 180,
    'android-chrome-192x192.png' => 192,
    'android-chrome-512x512.png' => 512,
];

foreach ($sizes as $filename => $size) {
    $image = new Imagick();
    $image->setBackgroundColor(new ImagickPixel('transparent'));
    $image->readImage($sourcePath);
    $image->setImageFormat('png');
    $image->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
    $image->setImagePage(0, 0, 0, 0);
    $image->stripImage();
    $image->writeImage($publicPath.DIRECTORY_SEPARATOR.$filename);
    $image->clear();
    $image->destroy();
}

$ico = new Imagick();
foreach ([16, 32, 48] as $size) {
    $image = new Imagick();
    $image->setBackgroundColor(new ImagickPixel('transparent'));
    $image->readImage($sourcePath);
    $image->setImageFormat('png');
    $image->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
    $image->setImagePage(0, 0, 0, 0);
    $image->stripImage();
    $ico->addImage($image);
}

$ico->setIteratorIndex(0);
$ico->setImageFormat('ico');
$ico->writeImages($publicPath.DIRECTORY_SEPARATOR.'favicon.ico', true);
$ico->clear();
$ico->destroy();

fwrite(STDOUT, "Generated favicon assets in {$publicPath}.\n");
