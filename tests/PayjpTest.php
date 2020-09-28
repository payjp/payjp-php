<?php

namespace Payjp;


class PayjpTest extends TestCase
{
    public function testLogger()
    {
        $msg1 = 'test1';
        $msg2 = 'test2';
        $mock = $this->getMockBuilder('\Payjp\Logger\LoggerInterface')->getMock();
        $mock->method('info')->with($msg1);
        $mock->method('error')->with($msg2);
        Payjp::setLogger($mock);
        Payjp::getLogger()->info($msg1);
        Payjp::getLogger()->error($msg2);
    }
}
