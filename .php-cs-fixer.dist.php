<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->path('lib')
    ->path('init.php')
    ->path('build.php')
    ->path('tests')
    ->path('.php-cs-fixer.php');

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        // Don't force public keyword for const (for compatibility with PHP < 7.1)
        'visibility_required' => ['elements' => ['property', 'method']],
    ])
    ->setFinder($finder);