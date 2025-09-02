<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->boot();

function show($label, $value)
{
    echo str_pad($label.':', 25).$value.PHP_EOL;
}

foreach (['ms', 'en'] as $locale) {
    app()->setLocale($locale);
    echo "=== Locale: $locale ===".PHP_EOL;
    show("__('Memproses...')", __('Memproses...'));
    show("__('Memuatkan...')", __('Memuatkan...'));
    show("__('Menyimpan...')", __('Menyimpan...'));
    show("__('Memadam...')", __('Memadam...'));
    show("__('Cari...')", __('Cari...'));
    show("__('Carian')", __('Carian'));
    show("__('Carian...')", __('Carian...'));
    show("__('Carian Umum')", __('Carian Umum'));
    show("__('Choose...')", __('Choose...'));
    echo PHP_EOL;
}
