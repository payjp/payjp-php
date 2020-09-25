<?php

namespace Payjp;


class PayjpTest extends TestCase
{
    public function testLogger()
    {
        $msg = 'test';
        $mock = $this->getMockBuilder('\Payjp\Logger\LoggerInterface')->getMock();
        $mock->method('log')->with($msg);
        Payjp::setLogger($mock);
        Payjp::getLogger()->log($msg);
    }
}
