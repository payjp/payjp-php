# PAY.JP for PHP

[![Build Status](https://travis-ci.org/payjp/payjp-php.svg?branch=master)](https://travis-ci.org/payjp/payjp-php)

## Requirements

PHP 5.6 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Add this to your `composer.json`:

    {
      "require": {
        "payjp/payjp-php": "~2.0"
      }
    }

Then install via:

    composer install

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

    require __DIR__ . '/vendor/autoload.php';

## Getting Started

Simple usage looks like:

    use Payjp\Client;

    $payjp = new Client('sk_test_c62fade9d045b54cd76d7036');

    $token = $payjp->tokens->create([
        'card' => [
            'number' => 4242424242424242,
            'exp_month' => 12,
            'exp_year' => 2020
        ]
    ]);
    echo $token . PHP_EOL;

## Documentation

- Please see our official [documentation](https://pay.jp/docs/started).

## Tests

In order to run tests first install [PHPUnit](http://packagist.org/packages/phpunit/phpunit) via [Composer](http://getcomposer.org/):

    composer update --dev

To run the test suite:

    ./vendor/bin/phpunit
