<?php

namespace Payjp;

class InvalidRequestErrorTest extends TestCase
{
    public function testInvalidObject()
    {
        self::authorizeFromEnv();
        $mock = $this->setUpMockRequest();
        $mock->expects($this->once())
            ->method('request')
            ->willThrowException(new Error\InvalidRequest('Invalid object', null, 404));
        try {
            Customer::retrieve('invalid');
        } catch (Error\InvalidRequest $e) {
            $this->assertSame(404, $e->getHttpStatus());
        }
    }

    public function testBadData()
    {
        self::authorizeFromEnv();
        $mock = $this->setUpMockRequest();
        $mock->expects($this->once())
            ->method('request')
            ->willThrowException(new Error\InvalidRequest('Bad data', null, 400));
        try {
            Charge::create();
        } catch (Error\InvalidRequest $e) {
            $this->assertSame(400, $e->getHttpStatus());
        }
    }
}
