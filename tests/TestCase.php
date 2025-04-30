<?php

namespace Payjp;

/**
 * Base class for Payjp test cases, provides some utility methods for creating
 * objects.
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    const API_KEY = 'sk_test_c62fade9d045b54cd76d7036';// public api key for test
    const CURRENCY = 'jpy';
    const COUNTRY = 'JP';

    protected $mock;

    protected static function authorizeFromEnv()
    {
        $apiKey = getenv('PAYJP_API_KEY');
        if (!$apiKey) {
            $apiKey = self::API_KEY;
        }

        Payjp::setApiKey($apiKey);
    }

    protected static function setMaxRetryForCi()
    {
        $maxRetry = getenv('MAX_RETRY');
        if ($maxRetry) {
            Payjp::setMaxRetry($maxRetry);
        }
    }

    /**
     * @before
     */
    protected function setUpTestCase()
    {
        $this->setMaxRetryForCi();
        ApiRequestor::setHttpClient(HttpClient\CurlClient::instance());
        $this->mock = null;
    }

    protected function mockRequest($method, $path, $params = [], $return = ['id' => 'myId'])
    {
        $mock = $this->setUpMockRequest();
        $mock->expects($this->once())
            ->method('request')
            ->with(
                strtolower($method),
                'https://api.pay.jp' . $path,
                $this->anything(),
                $params,
                false
            )
            ->willReturn([json_encode($return), 200]);
    }

    protected function setUpMockRequest()
    {
        self::authorizeFromEnv();
        $this->mock = $this->createMock('\Payjp\HttpClient\ClientInterface');
        ApiRequestor::setHttpClient($this->mock);
        return $this->mock;
    }

    /**
     * Create a valid test customer.
     */
    protected static function createTestCustomer(array $attributes = [])
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 6,
            "exp_year" => date('Y') + 3,
            "cvc" => "314"
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        return Customer::create(
            $attributes + [
                'card' => $card
            ]
        );
    }

    /**
     * Generate a random 8-character string. Useful for ensuring
     * multiple test suite runs don't conflict
     */
    protected static function randomString()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $str = '';
        for ($i = 0; $i < 10; $i++) {
            $str .= $chars[rand(0, strlen($chars) - 1)];
        }

        return $str;
    }

    /**
     * Verify that a plan with a given ID exists, or create a new one if it does
     * not.
     */
    protected static function retrieveOrCreatePlan($id)
    {
        self::authorizeFromEnv();

        try {
            $plan = Plan::retrieve($id);
        } catch (Error\InvalidRequest $exception) {
            $plan = Plan::create(
                [
                    'id' => $id,
                    'amount' => 500,
                    'currency' => self::CURRENCY,
                    'interval' => 'month',
                    'name' => 'Gold Test Plan',
                ]
            );
        }
        return $plan;
    }

    /**
     * Genereate a semi-random string
     */
    protected static function generateRandomString($length = 24)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTU';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
