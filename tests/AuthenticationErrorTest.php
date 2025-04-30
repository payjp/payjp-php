<?php

namespace Payjp;

class AuthenticationErrorTest extends TestCase
{
    public function testInvalidCredentials()
    {
        Payjp::setApiKey('invalid');
        $mock = $this->setUpMockRequest();
        $mock->expects($this->once())
            ->method('request')
            ->willThrowException(new Error\Authentication('Invalid API Key', 401));
        try {
            Customer::create();
        } catch (Error\Authentication $e) {
            $this->assertSame(401, $e->getHttpStatus());
        }
    }
}
