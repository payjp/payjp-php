<?php

namespace Payjp\Tests;

use PHPUnit\Framework\TestCase;
use Phake;

/**
 * Class PayjpTestCase
 * @package Payjp\Tests
 */
class PayjpTestCase extends TestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }
}
