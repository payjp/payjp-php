<?php

namespace Payjp;

class ChargeTest extends TestCase
{
    public function testUrls()
    {
        $this->assertSame(Charge::classUrl(), '/v1/charges');
        $charge = new Charge('abcd/efgh');
        $this->assertSame($charge->instanceUrl(), '/v1/charges/abcd%2Fefgh');
    }

    //POST /charges
    public function testCreate()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => "05",
            'exp_year' => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $c = Charge::create(
            array(
                'amount' => 100,
                'currency' => self::CURRENCY,
                'card' => $card->id
            )
        );
        $this->assertTrue($c->paid);
        $this->assertFalse($c->refunded);
    }

    //POST /charges
    public function testIdempotentCreate()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 5,
            'exp_year' => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $c = Charge::create(
            array(
                'amount' => 100,
                'currency' => self::CURRENCY,
                'card' => $card->id
            ),
            array(
                'idempotency_key' => self::generateRandomString(),
            )
        );

        $this->assertTrue($c->paid);
        $this->assertFalse($c->refunded);
    }

    //GET /charges/:id
    public function testRetrieve()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 5,
            "exp_year" => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $c = Charge::create(
            array(
                'amount' => 100,
                'currency' => self::CURRENCY,
                'card' => $card->id
            )
        );
        $d = Charge::retrieve($c->id);
        $this->assertSame($d->id, $c->id);
    }

    //GET /charges/
    public function testAll()
    {
        self::authorizeFromEnv();

        $charges = Charge::all(
            array(
                    'limit' => 3,
                    'offset' => 10
            )
        );

        $planID = 'gold-' . self::randomString();
        self::retrieveOrCreatePlan($planID);

        $customer = self::createTestCustomer();

        $charge = Charge::create(
            array(
                    'amount' => 1000,
                    'currency' => self::CURRENCY,
                    'customer' => $customer->id
            )
        );

        $charges_2 = Charge::all(
            array(
                    'customer' => $customer->id
            )
        );

        $this->assertSame(1, count($charges_2['data']));
        $this->assertSame($charge->id, $charges_2['data'][0]->id);

        $charge_2 = Charge::create(
            array(
                    'amount' => 1500,
                    'currency' => self::CURRENCY,
                    'customer' => $customer->id
            )
        );

        $charges_3 = Charge::all(
            array(
                    'limit' => 2,
                    'offset' => 0,
                    'customer' => $customer->id
            )
        );

        $this->assertSame(2, count($charges_3['data']));
    }

    //POST /charges/:id
    public function testUpdateDescription()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 5,
            "exp_year" => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $charge = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id
            )
        );

        $charge->description = 'foo bar';
        $charge->save();

        $updatedCharge = Charge::retrieve($charge->id);
        $this->assertSame('foo bar', $updatedCharge->description);
        $this->assertSame('foo bar', $charge->description);
    }

    //POST /charges/:id/capture
    public function testCaptureAll()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 5,
            "exp_year" => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $charge = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id,
                    'capture' => false
            )
        );

        $this->assertFalse($charge->captured);

        $capturedCharge = $charge->capture();
        $this->assertTrue($charge->captured);
        $this->assertTrue($capturedCharge->captured);
    }

    //POST /charges/:id/capture
    public function testCapturePart()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 5,
            "exp_year" => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $charge = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id,
                    'capture' => false
            )
        );

        $this->assertFalse($charge->captured);

        $capturedCharge = $charge->capture(array('amount'=>50));
        $this->assertTrue($capturedCharge->captured);
        $this->assertTrue($charge->captured);
    }

    //POST /charges/:id/refund
    public function testRefund()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 5,
            "exp_year" => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $charge = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id,
            )
        );

        $this->assertTrue($charge->captured);

        $redundChargePart = $charge->refund(
            array(
                    'amount' => 50,
                    'refund_reason' => 'foo bar 1'
            )
        );

        $this->assertTrue($redundChargePart->refunded);
        $this->assertSame('foo bar 1', $redundChargePart->refund_reason);
        $this->assertSame(50, $redundChargePart->amount_refunded);
        $this->assertTrue($charge->refunded);
        $this->assertSame('foo bar 1', $charge->refund_reason);
        $this->assertSame(50, $charge->amount_refunded);

        $refundChargeAll = $charge->refund(
            array(
                    'refund_reason' => 'foo bar 2'
            )
        );

        $this->assertTrue($refundChargeAll->refunded);
        $this->assertSame('foo bar 2', $refundChargeAll->refund_reason);
        $this->assertSame(100, $refundChargeAll->amount_refunded);
        $this->assertTrue($charge->refunded);
        $this->assertSame('foo bar 2', $charge->refund_reason);
        $this->assertSame(100, $charge->amount_refunded);
    }

    /**
     * @expectedException Payjp\Error\Card
     */
    public function testInvalidCard()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424241",
            "exp_month" => 5,
            "exp_year" => date('Y') + 1
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id
            )
        );
    }


    public function testInvalidAddressZipTest()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4000000000000070",
            "exp_month" => '05',
            'exp_year' => (date('Y') + 1)
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $ch = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id
            )
        );

        $this->assertSame("failed", $ch->card->address_zip_check);
    }

    public function testInvalidCvcTest()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4000000000000100",
            "exp_month" => '05',
            'exp_year' => (date('Y') + 1)
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $ch = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id
            )
        );

        $this->assertSame("failed", $ch->card->cvc_check);
    }

    public function testUnavailableCvcTest()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4000000000000150",
            "exp_month" => '05',
            'exp_year' => (date('Y') + 1)
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $ch = Charge::create(
            array(
                    'amount' => 100,
                    'currency' => self::CURRENCY,
                    'card' => $card->id
            )
        );

        $this->assertSame("unavailable", $ch->card->cvc_check);
    }
}
