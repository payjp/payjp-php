<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/lib',
        __DIR__ . '/tests',
    ])
    ->append([
        __FILE__,
        __DIR__ . '/init.php',
        __DIR__ . '/build.php',
    ]);

$config = new PhpCsFixer\Config();
return $config;
