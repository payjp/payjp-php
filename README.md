# PAY.JP for PHP

[![Build Status](https://travis-ci.org/payjp/payjp-php.svg?branch=master)](https://travis-ci.org/payjp/payjp-php)

## Requirements

PHP 5.6 and later.

> Even if it is not a corresponding version, it may work, but it does not support it.
  Due to the PHP [END OF LIFE](http://php.net/supported-versions.php) cycle. 

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

In advance, you need to get a token by [Checkout](https://pay.jp/docs/checkout) or [payjp.js](https://pay.jp/docs/payjs).

```php
\Payjp\Payjp::setApiKey('sk_test_c62fade9d045b54cd76d7036');
$charge = \Payjp\Charge::create(array(
  'card' => 'token_id_by_Checkout_or_payjp-js',
  'amount' => 2000,
  'currency' => 'jpy'
));
echo $charge->amount; // 2000
```

## Documentation

- [Request Example](https://github.com/payjp/payjp-php/blob/master/RequestExample.md)
- Please see our official [documentation](https://pay.jp/docs/started).

## Tests

In order to run tests first install [PHPUnit](http://packagist.org/packages/phpunit/phpunit) via [Composer](http://getcomposer.org/):

    composer update --dev

To run the test suite:

    ./vendor/bin/phpunit
