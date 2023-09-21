<?php

    include __DIR__.'/PDF417/PDF417.php';
    include __DIR__.'/PDF417/pColor.php';
    include __DIR__.'/PDF417/Encoder/Encoder.php';
    include __DIR__.'/PDF417/Encoder/EncoderNumber.php';
    include __DIR__.'/PDF417/Encoder/EncoderText.php';
    include __DIR__.'/PDF417/Encoder/EncoderByte.php';
    include __DIR__.'/PDF417/Encoder/ReedSolomon.php';
    include __DIR__.'/PDF417/Encoder/Codes.php';
    include __DIR__.'/PDF417/Renderer.php';

    use PDF417\PDF417;
    use PDF417\pColor;

    // Encode the data, returns a BarcodeData object
    $pdf417 = new PDF417();
    $pdf417->encode($text);

    $base64Encoded = $pdf417->forWeb("BASE64", $text);

?>