<?php

use chillerlan\QRCode;
use chillerlan\QRCode\Output;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class QR {
    /** @var QRCode */ private $qrCode;
    public function __construct() {
    }

    public function generate($data) {
        $options = new QRCode\QROptions();
        $this->qrCode = new QRCode\QRCode($options);
        // $options->version      = 7;
        // $options->outputBase64 = false;
        $options->addQuietzone = false;
        $options->outputType = Output\QROutputInterface::GDIMAGE_PNG;
        return $this->qrCode->render($data);
    }

    public function getImage($data) {
        $options = new QRCode\QROptions();
        $options->outputType = Output\QROutputInterface::GDIMAGE_PNG;
        $options->returnResource = true;
        $options->addQuietzone = false;
        $options->scale = 6;
        $this->qrCode = new QRCode\QRCode($options);
        $gdImage = $this->qrCode->render($data);

        header('Content-type: image/png');
        imagejpeg($gdImage);
        imagedestroy($gdImage);
    }
}
