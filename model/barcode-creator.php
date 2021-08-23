<?php

    declare(strict_types=1);

    if (php_sapi_name() != "cli") {
        chdir("../");
    }

    spl_autoload_register(function ($class_name) {
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
        include $filename;
    });

    use PDF417\PDF417;
    use PDF417\pColor;

    // Encode the data, returns a BarcodeData object
    $pdf417 = new PDF417();
    $pdf417->encode($text);

    // Create a PNG image
    $pdf417->toFile(trim(__DIR__,"model").'/invitees-barcodes/'.$text.'.png');

    // Create an SVG representation
    $pdf417->config(['color' => new pColor(0)]);
    $pdf417->toFile(trim(__DIR__,"model")."/invitees-barcodes/".$text.".svg");

?>