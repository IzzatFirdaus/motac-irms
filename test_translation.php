<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->boot();

echo "Translation key 'menu.system_settings.title' returns: ";
echo '"' . __('menu.system_settings.title') . '"' . PHP_EOL;

echo "Current locale: " . app()->getLocale() . PHP_EOL;
