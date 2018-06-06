<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class CustomerTest
 * @package Payjp\Tests
 */
class CustomerTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testCustomerRetrieve()
    {
        $res = $this->payjp->customers->retrieve('cus_xxx');
        Phake::verify($this->payjp)->request('GET', 'customers/cus_xxx');
    }

    public function testCustomerAll()
    {
        $res = $this->payjp->customers->all();
        Phake::verify($this->payjp)->request('GET', 'customers', []);
    }

    public function testCustomerCreate()
    {
        $params = [
            'email' => 'example@example.com',
        ];
        $res = $this->payjp->customers->create($params);
        Phake::verify($this->payjp)->request('POST', 'customers', $params);
    }

    public function testCustomerUpdate()
    {
        $params = [
            'email' => 'example@example.com',
        ];
        $res = $this->payjp->customers->update('cus_xxx', $params);
        Phake::verify($this->payjp)->request('POST', 'customers/cus_xxx', $params);
    }

    public function testCustomerDelete()
    {
        $res = $this->payjp->customers->delete('cus_xxx');
        Phake::verify($this->payjp)->request('DELETE', 'customers/cus_xxx');
    }
}
