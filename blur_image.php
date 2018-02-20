<?php

//header('Content-type: image/png');
//
//$image = new Imagick('test.png');
////
//$image->blurImage(10,1000000);
//echo $image;

//phpinfo();

$imagePath = 'images/test_copy.png';
$im = imagecreatefrompng($imagePath);
//$im = imagecreatefromjpeg($imagePath);

if ($im) {
    for ($x=1; $x<=200; $x++) {
        imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
//        imagefilter($im, IMG_FILTER_GRAYSCALE);
        echo $x . PHP_EOL;
    }

    imagepng($im, 'images/filtered.png');
//    imagejpeg($im, 'images/filtered.jpeg');

    imagedestroy($im);
}