<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class TransferTest
 * @package Payjp\Tests
 */
class TransferTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testTransferAll()
    {
        $res = $this->payjp->transfers->all();
        Phake::verify($this->payjp)->request('GET', 'transfers', []);
    }

    public function testTransferRetrieve()
    {
        $res = $this->payjp->transfers->retrieve('tra_xxx');
        Phake::verify($this->payjp)->request('GET', 'transfers/tra_xxx');
    }
}
