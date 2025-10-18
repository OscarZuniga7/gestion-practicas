<?php
require __DIR__.'/vendor/autoload.php';
use Dompdf\Dompdf;
$d = new Dompdf();
$d->loadHtml('<h1>Hola Dompdf</h1>');
$d->setPaper('A4');
$d->render();
$d->stream('test.pdf', ['Attachment' => false]);
