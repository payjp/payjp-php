<?php

namespace Payjp;

class ErrorTest extends TestCase
{
    public function testCreation()
    {
        try {
            throw new Error\Api(
                "hello",
                500,
                "{'foo':'bar'}",
                ['foo' => 'bar']
            );
            $this->fail("Did not raise error");
        } catch (Error\Api $e) {
            $this->assertSame("hello", $e->getMessage());
            $this->assertSame(500, $e->getHttpStatus());
            $this->assertSame("{'foo':'bar'}", $e->getHttpBody());
            $this->assertSame(['foo' => 'bar'], $e->getJsonBody());
        }
    }
}
