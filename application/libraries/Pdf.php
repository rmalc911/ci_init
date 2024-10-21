<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// reference the Dompdf namespace
use Dompdf\Dompdf;

class Pdf {
    public $dompdf;
    public function __construct() {
        // instantiate and use the dompdf class
        $this->dompdf = new Dompdf();
        // (Optional) Setup the paper size and orientation
        $this->dompdf->setPaper('A4');
    }

    public function loadHtml($html) {
        $this->dompdf->loadHtml($html);
    }

    // Output the generated PDF to Browser
    public function stream($stream = 'file.pdf', $download = false) {
        $this->dompdf->render();
        $this->dompdf->stream($stream, ['Attachment' => $download]);
    }

    // Return the generated PDF as string
    public function output() {
        $this->dompdf->render();
        return $this->dompdf->output();
    }
}
