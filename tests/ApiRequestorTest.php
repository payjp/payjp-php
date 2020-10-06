<?php

namespace Payjp;

class ApiRequestorTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        # MaxRetryを戻してinitialDelayを短くする
        Payjp::setMaxRetry(0);
        Payjp::setRetryInitialDelay(0.1);
        Payjp::setRetryMaxDelay(32);
    }

    protected function tearDown()
    {
        parent::tearDown();
        Payjp::setMaxRetry(0);
        $this->setMaxRetryForCi();
        Payjp::setRetryInitialDelay(2);
        Payjp::setRetryMaxDelay(32);
    }

    private $errorResponse = array('rcode'=>500, 'rbody'=>array('error' => array(
        "code" => "payjp_wrong",
        "message" => "An unexpected error occurred.",
        "status" => 500,
        "type" => "server_error",
    )));
    private $rateLimitResponse = array('rcode'=>429, 'rbody'=>array('error' => array(
        "code" => "over_capacity",
        "message" => "The service is over capacity. Please try again later.",
        "status" => 429,
        "type" => "client_error",
    )));
    private $successResponse = array('rcode'=>200, 'rbody'=>array('data'=>array()));

    private function setUpResponses($responses)
    {
        $mock = $this->setUpMockRequest();
        for ($i = 0; $i < count($responses); $i++) {
            $mock->expects($this->at($i))
            ->method('request')
            ->willReturn(array(json_encode($responses[$i]['rbody']), $responses[$i]['rcode']));
        }
    }

    // _encodeObjects

    public function testEncodeObjects()
    {
        $reflector = new \ReflectionClass('Payjp\\ApiRequestor');
        $method = $reflector->getMethod('_encodeObjects');
        $method->setAccessible(true);

        $a = array('customer' => new Customer('abcd'));
        $enc = $method->invoke(null, $a);
        $this->assertSame($enc, array('customer' => 'abcd'));

        // Preserves UTF-8
        $v = array('customer' => "☃");
        $enc = $method->invoke(null, $v);
        $this->assertSame($enc, $v);

        // Encodes latin-1 -> UTF-8
        $v = array('customer' => "\xe9");
        $enc = $method->invoke(null, $v);
        $this->assertSame($enc, array('customer' => "\xc3\xa9"));
    }

    // request retry

    public function testRetryDisabled()
    {
        $this->setUpResponses(array($this->rateLimitResponse));
        $requestor = new ApiRequestor(self::API_KEY);

        try {
            $response = $requestor->request('GET', '/v1/accounts');
        } catch (Error\Api $e) {
            $this->assertSame($e->getMessage(), $this->rateLimitResponse['rbody']['error']['message']);
            $this->assertSame($e->getHttpStatus(), $this->rateLimitResponse['rcode']);
        }
    }

    public function testNoRetry()
    {
        $this->setUpResponses(array($this->errorResponse));
        $requestor = new ApiRequestor(self::API_KEY);

        try {
            $response = $requestor->request('GET', '/v1/accounts');
        } catch (Error\Api $e) {
            $this->assertSame($e->getMessage(), $this->errorResponse['rbody']['error']['message']);
            $this->assertSame($e->getHttpStatus(), $this->errorResponse['rcode']);
        }
    }

    public function testFullRetryAndSuccess()
    {
        Payjp::setMaxRetry(2);
        $this->setUpResponses(array($this->rateLimitResponse, $this->rateLimitResponse, $this->successResponse));
        $requestor = new ApiRequestor(self::API_KEY);

        $response = $requestor->request('GET', '/v1/accounts');

        $this->assertSame($response[0], $this->successResponse['rbody']);
    }

    public function testFullRetryAndFailed()
    {
        Payjp::setMaxRetry(2);
        $this->setUpResponses(array($this->rateLimitResponse, $this->rateLimitResponse, $this->rateLimitResponse));
        $requestor = new ApiRequestor(self::API_KEY);

        try {
            $response = $requestor->request('GET', '/v1/accounts');
        } catch (Error\Api $e) {
            $this->assertSame($e->getMessage(), $this->rateLimitResponse['rbody']['error']['message']);
            $this->assertSame($e->getHttpStatus(), $this->rateLimitResponse['rcode']);
        }
    }

    public function testGetRetryDelay()
    {
        Payjp::setRetryInitialDelay(2);
        $reflector = new \ReflectionClass('Payjp\\ApiRequestor');
        $method = $reflector->getMethod('getRetryDelay');
        $method->setAccessible(true);
        $r = new ApiRequestor(self::API_KEY);

        $this->assertTrue(1 <= $method->invoke($r, 0) && $method->invoke($r, 0) <= 2);
        $this->assertTrue(2 <= $method->invoke($r, 1) && $method->invoke($r, 1) <= 4);
        $this->assertTrue(4 <= $method->invoke($r, 2) && $method->invoke($r, 2) <= 8);
        # not over retryMaxDelay
        $this->assertTrue(16 <= $method->invoke($r, 4) && $method->invoke($r, 4) <= 32);
        $this->assertTrue(16 <= $method->invoke($r, 10) && $method->invoke($r, 10) <= 32);
    }
}
