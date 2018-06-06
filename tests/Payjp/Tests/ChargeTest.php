<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class ChargeTest
 * @package Payjp\Tests
 */
class ChargeTest extends \Payjp\Tests\PayjpTestCase {

    private $requestor;

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testChargeRetrieve()
    {
        $res = $this->payjp->charges->retrieve('ch_xxx');
        Phake::verify($this->payjp)->request('GET', 'charges/ch_xxx');
    }

    public function testChargeAll()
    {
        $res = $this->payjp->charges->all();
        Phake::verify($this->payjp)->request('GET', 'charges', []);
    }

    public function testChargeCreate()
    {
        $params = [
            'amount' => 500,
            'currency' => 'jpy',
            'card' => [
                'number' => 4242424242424242,
                'exp_month' => 12,
                'exp_year' => 202
            ],
        ];
        $res = $this->payjp->charges->create($params);
        Phake::verify($this->payjp)->request('POST', 'charges', $params);
    }

    public function testChargeUpdate()
    {
        $params = [
            'description' => 'aaaaa',
        ];
        $res = $this->payjp->charges->update('ch_xxx', $params);
        Phake::verify($this->payjp)->request('POST', 'charges/ch_xxx', $params);
    }

    public function testChargeRefund()
    {
        $res = $this->payjp->charges->refund('ch_xxx');
        Phake::verify($this->payjp)->request('POST', 'charges/ch_xxx/refund', []);
    }

    public function testChargeCapture()
    {
        $res = $this->payjp->charges->capture('ch_xxx');
        Phake::verify($this->payjp)->request('POST', 'charges/ch_xxx/capture', []);
    }

    public function testChargeReauth()
    {
        $params = [
            'expiry_days' => 30,
        ];
        $res = $this->payjp->charges->reauth('ch_xxx', $params);
        Phake::verify($this->payjp)->request('POST', 'charges/ch_xxx/reauth', $params);
    }

}
