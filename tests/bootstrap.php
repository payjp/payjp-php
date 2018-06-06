<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Payjp\Tests', __DIR__);

Phake::setClient(Phake::CLIENT_PHPUNIT);
