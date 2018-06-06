<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class TokenTest
 * @package Payjp\Tests
 */
class TokenTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testTokenRetrieve()
    {
        $res = $this->payjp->tokens->retrieve('tok_xxx');
        Phake::verify($this->payjp)->request('GET', 'tokens/tok_xxx');
    }

    public function testTokenCreate()
    {
        $params = [
            'card' => [
                'number' => 4242424242424242,
                'exp_month' => 12,
                'exp_year' => 2020,
            ]
        ];
        $res = $this->payjp->tokens->create($params);
        Phake::verify($this->payjp)->request('POST', 'tokens', $params);
    }
}
