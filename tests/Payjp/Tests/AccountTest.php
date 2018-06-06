<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class AccountTest
 * @package Payjp\Tests
 */
class AccountTest extends \Payjp\Tests\PayjpTestCase {

    public function testAccountRetrieve()
    {
        $res = $this->payjp->accounts->retrieve();
        Phake::verify($this->payjp)->request('GET', 'accounts');
    }
}
