<?php

require __DIR__ . '/vendor/autoload.php';

use Payjp\Client;

$payjp = new Client('sk_test_c62fade9d045b54cd76d7036');

try {
    $token = $payjp->tokens->create([
        'card' => [
            'number' => 4242424242424242,
            'exp_month' => 12,
            'exp_year' => 2020
        ]
    ]);
    $res = $payjp->tokens->retrieve($token['id']);
    $events = $payjp->events->all();
    $payjp->events->retrieve($events['data'][0]['id']);

    echo 'ok' . PHP_EOL;
} catch (\Payjp\Exception\ApiConnectionException $e) {
    echo $e->getMessage() . PHP_EOL;
    throw new $e;
} catch (\Payjp\Exception\CardException $e) {
    var_dump($e->getJsonBody());
}
