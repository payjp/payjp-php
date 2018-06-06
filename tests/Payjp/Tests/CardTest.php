<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class CardTest
 * @package Payjp\Tests
 */
class CardTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testCardRetrieve()
    {
        $res = $this->payjp->customers->cards->retrieve('cus_123', 'car_456');
        Phake::verify($this->payjp)->request('GET', 'customers/cus_123/cards/car_456');
    }

    public function testCardAll()
    {
        $res = $this->payjp->customers->cards->all('cus_123');
        Phake::verify($this->payjp)->request('GET', 'customers/cus_123/cards', []);
    }

    public function testCardCreate()
    {
        $params = [
            'card' => 'tok_xxx'
        ];
        $res = $this->payjp->customers->cards->create('cus_123', $params);
        Phake::verify($this->payjp)->request('POST', 'customers/cus_123/cards', $params);
    }

    public function testCardUpdate()
    {
        $params = [
            'name' => 'updated name',
        ];
        $res = $this->payjp->customers->cards->update('cus_123', 'car_456', $params);
        Phake::verify($this->payjp)->request('POST', 'customers/cus_123/cards/car_456', $params);
    }

    public function testCardDelete()
    {
        $params = [
            'prorate' => true,
        ];
        $res = $this->payjp->customers->cards->delete('cus_123', 'car_456', $params);
        Phake::verify($this->payjp)->request('DELETE', 'customers/cus_123/cards/car_456', $params);
    }

}
