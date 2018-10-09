# PAY.JP for PHP

[![Build Status](https://travis-ci.org/payjp/payjp-php.svg?branch=master)](https://travis-ci.org/payjp/payjp-php)

## Requirements

PHP 5.6 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Add this to your `composer.json`:

    {
      "require": {
        "payjp/payjp-php": "~1.0"
      }
    }

Then install via:

    composer install

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

    require_once 'vendor/autoload.php';

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/payjp/payjp-php/releases). Then, to use the bindings, include the `init.php` file.

    require_once '/path/to/payjp-php/init.php';

## Getting Started

Simple usage looks like:

    \Payjp\Payjp::setApiKey('sk_test_c62fade9d045b54cd76d7036');
    $myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2020);
    $charge = \Payjp\Charge::create(array('card' => $myCard, 'amount' => 2000, 'currency' => 'jpy'));
    echo $charge;

## Documentation

- [Request Example](https://github.com/payjp/payjp-php/blob/master/RequestExample.md)
- Please see our official [documentation](https://pay.jp/docs/started).

## Tests

In order to run tests first install [PHPUnit](http://packagist.org/packages/phpunit/phpunit) via [Composer](http://getcomposer.org/):

    composer update --dev

To run the test suite:

    ./vendor/bin/phpunit
