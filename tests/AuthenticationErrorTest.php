<?php

namespace Payjp;

class AuthenticationErrorTest extends TestCase
{
    public function testInvalidCredentials()
    {
        Payjp::setApiKey('invalid');
        try {
            Customer::create();
        } catch (Error\Authentication $e) {
            $this->assertSame(401, $e->getHttpStatus());
        }
    }
}
