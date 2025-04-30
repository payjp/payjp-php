<?php

namespace Payjp;

class ChargeTest extends TestCase
{
    private function mockCardData($id = 'car_test', $attributes = [])
    {
        $base = [
            'address_city' => null,
            'address_line1' => null,
            'address_line2' => null,
            'address_state' => null,
            'address_zip' => null,
            'address_zip_check' => 'unchecked',
            'brand' => 'Visa',
            'country' => null,
            'created' => 1583375140,
            'customer' => null,
            'cvc_check' => 'passed',
            'exp_month' => 2,
            'exp_year' => 2099,
            'fingerprint' => 'e1d8225886e3a7211127df751c86787f',
            'id' => $id,
            'last4' => '4242',
            'livemode' => false,
            'metadata' => [],
            'name' => 'PAY TARO',
            'object' => 'card',
            'three_d_secure_status' => null
        ];
        return array_merge($base, $attributes);
    }

    private function mockTokenData($id = 'tok_test', $cardAttributes = [])
    {
        $cardData = $this->mockCardData('car_' . $id, $cardAttributes);
        return [
            'id' => $id,
            'object' => 'token',
            'livemode' => false,
            'used' => false,
            'card' => $cardData,
            'created' => time(),
        ];
    }

    private function mockChargeData($id = 'ch_test', $attributes = [], $cardAttributes = [])
    {
        $cardData = $this->mockCardData('car_6845da1a8651f889bc432362dfcb', $cardAttributes);
        $base = [
            'amount' => 3500,
            'amount_refunded' => 0,
            'captured' => true,
            'captured_at' => 1433127983,
            'card' => $cardData,
            'created' => 1433127983,
            'currency' => 'jpy',
            'customer' => null,
            'description' => null,
            'expired_at' => null,
            'failure_code' => null,
            'failure_message' => null,
            'fee_rate' => '3.00',
            'id' => $id,
            'livemode' => false,
            'metadata' => null,
            'object' => 'charge',
            'paid' => true,
            'platform_fee' => null,
            'platform_fee_rate' => '10.00',
            'refund_reason' => null,
            'refunded' => false,
            'subscription' => null,
            'tenant' => 'ten_121673955bd7aa144de5a8f6c262',
            'total_platform_fee' => 350,
            'three_d_secure_status' => null
        ];
        return array_merge($base, $attributes);
    }

    private function mockListResponse($data, $url)
    {
        return [
            'object' => 'list',
            'data' => $data,
            'has_more' => false,
            'url' => $url,
            'count' => count($data),
        ];
    }

    public function testUrls()
    {
        $this->assertSame(Charge::classUrl(), '/v1/charges');
        $charge = Charge::constructFrom(['id' => 'ch_abcd/efgh'], new Util\RequestOptions());
        $this->assertSame($charge->instanceUrl(), '/v1/charges/ch_abcd%2Fefgh');
    }

    public function testCreate()
    {
        $this->setUpMockRequest();
        $tokenId = 'tok_create_test';
        $chargeId = 'ch_create_test';
        $createAmount = 100;

        $mockToken = $this->mockTokenData($tokenId);
        $mockCharge = $this->mockChargeData($chargeId, ['amount' => $createAmount, 'card' => $mockToken['card']]);

        $chargeParams = [
            'amount' => $createAmount,
            'currency' => self::CURRENCY,
            'card' => $tokenId,
        ];
        $this->mockRequest('POST', '/v1/charges', $chargeParams, $mockCharge);

        $c = Charge::create($chargeParams);

        $this->assertInstanceOf(Charge::class, $c);
        $this->assertSame($chargeId, $c->id);
        $this->assertTrue($c->paid);
        $this->assertFalse($c->refunded);
        $this->assertSame($createAmount, $c->amount);
        $this->assertInstanceOf(Card::class, $c->card);
        $this->assertSame($mockToken['card']['id'], $c->card->id);
    }

    public function testIdempotentCreate()
    {
        $this->setUpMockRequest();
        $tokenId = 'tok_idempotent_test';
        $chargeId = 'ch_idempotent_test';
        $idempotencyKey = self::generateRandomString();

        $mockToken = $this->mockTokenData($tokenId);
        $mockCharge = $this->mockChargeData($chargeId, ['card' => $mockToken['card']]);

        $chargeParams = [
            'amount' => 100,
            'currency' => self::CURRENCY,
            'card' => $tokenId
        ];
        $this->mockRequest('POST', '/v1/charges', $chargeParams, $mockCharge);

        $c = Charge::create($chargeParams, ['idempotency_key' => $idempotencyKey]);

        $this->assertInstanceOf(Charge::class, $c);
        $this->assertSame($chargeId, $c->id);
        $this->assertTrue($c->paid);
    }

    public function testRetrieve()
    {
        $this->setUpMockRequest();
        $chargeId = 'ch_retrieve_test';
        $mockCharge = $this->mockChargeData($chargeId);

        $this->mockRequest('GET', '/v1/charges/' . $chargeId, [], $mockCharge);

        $d = Charge::retrieve($chargeId);

        $this->assertInstanceOf(Charge::class, $d);
        $this->assertSame($chargeId, $d->id);
        $this->assertSame($mockCharge['amount'], $d->amount);
        $this->assertInstanceOf(Card::class, $d->card);
        $this->assertSame($mockCharge['card']['id'], $d->card->id);
    }

    public function testAll()
    {
        $this->setUpMockRequest();
        $chargeId1 = 'ch_all_1';
        $chargeId2 = 'ch_all_2';
        $customerId = 'cus_all_test';

        $mockCharge1 = $this->mockChargeData($chargeId1, ['customer' => $customerId]);
        $mockCharge2 = $this->mockChargeData($chargeId2, ['customer' => $customerId, 'amount' => 1500]);

        $mockList1 = $this->mockListResponse([], '/v1/charges');
        $this->mockRequest('GET', '/v1/charges', ['limit' => 3, 'offset' => 10], $mockList1);
        $charges = Charge::all(['limit' => 3, 'offset' => 10]);
        $this->assertInstanceOf(Collection::class, $charges);
        $this->assertCount(0, $charges->data);

        $mockList2 = $this->mockListResponse([$mockCharge1], '/v1/charges');
        $this->mockRequest('GET', '/v1/charges', ['customer' => $customerId], $mockList2);
        $charges_2 = Charge::all(['customer' => $customerId]);
        $this->assertInstanceOf(Collection::class, $charges_2);
        $this->assertCount(1, $charges_2->data);
        $this->assertInstanceOf(Charge::class, $charges_2->data[0]);
        $this->assertSame($chargeId1, $charges_2->data[0]->id);

        $mockList3 = $this->mockListResponse([$mockCharge2, $mockCharge1], '/v1/charges');
        $this->mockRequest('GET', '/v1/charges', ['limit' => 2, 'offset' => 0, 'customer' => $customerId], $mockList3);
        $charges_3 = Charge::all(['limit' => 2, 'offset' => 0, 'customer' => $customerId]);
        $this->assertInstanceOf(Collection::class, $charges_3);
        $this->assertCount(2, $charges_3->data);
        $this->assertInstanceOf(Charge::class, $charges_3->data[0]);
        $this->assertSame($chargeId2, $charges_3->data[0]->id);
        $this->assertInstanceOf(Charge::class, $charges_3->data[1]);
        $this->assertSame($chargeId1, $charges_3->data[1]->id);
    }

    public function testUpdateDescription()
    {
        $this->setUpMockRequest();
        $chargeId = 'ch_update_test';
        $originalDescription = 'Initial Desc';
        $updatedDescription = 'foo bar';

        $mockChargeOriginal = $this->mockChargeData($chargeId, ['description' => $originalDescription]);
        $mockChargeUpdated = $this->mockChargeData($chargeId, ['description' => $updatedDescription]);

        // Mock initial retrieve or creation (let's assume retrieve)
        $this->mockRequest('GET', '/v1/charges/' . $chargeId, [], $mockChargeOriginal);
        $charge = Charge::retrieve($chargeId);
        $this->assertSame($originalDescription, $charge->description);

        // Mock save (POST request)
        $saveParams = ['description' => $updatedDescription];
        $this->mockRequest('POST', '/v1/charges/' . $chargeId, $saveParams, $mockChargeUpdated);
        $charge->description = $updatedDescription;
        $savedCharge = $charge->save();

        $this->assertInstanceOf(Charge::class, $savedCharge);
        $this->assertSame($updatedDescription, $savedCharge->description);
        // Check original object was updated
        $this->assertSame($updatedDescription, $charge->description);

        // Mock subsequent retrieve to confirm persistence
        $this->mockRequest('GET', '/v1/charges/' . $chargeId, [], $mockChargeUpdated);
        $updatedCharge = Charge::retrieve($charge->id);
        $this->assertSame($updatedDescription, $updatedCharge->description);
    }

    public function testCaptureAll()
    {
        $this->setUpMockRequest();
        $chargeId = 'ch_capture_all_test';
        $mockChargeUncaptured = $this->mockChargeData($chargeId, ['captured' => false, 'paid' => false, 'paid_at' => null]);
        $mockChargeCaptured = $this->mockChargeData($chargeId, ['captured' => true, 'paid' => true, 'paid_at' => time()]);

        $this->mockRequest('GET', '/v1/charges/' . $chargeId, [], $mockChargeUncaptured);
        $charge = Charge::retrieve($chargeId);
        $this->assertFalse($charge->captured);

        $captureUrl = '/v1/charges/' . $chargeId . '/capture';
        $this->mockRequest('POST', $captureUrl, [], $mockChargeCaptured);
        $capturedCharge = $charge->capture();

        $this->assertInstanceOf(Charge::class, $capturedCharge);
        $this->assertTrue($capturedCharge->captured);
        $this->assertTrue($charge->captured);
    }

    public function testCapturePart()
    {
        $this->setUpMockRequest();
        $chargeId = 'ch_capture_part_test';
        $captureAmount = 50;
        $mockChargeUncaptured = $this->mockChargeData($chargeId, ['amount' => 100, 'captured' => false, 'paid' => false, 'paid_at' => null]);
        $mockChargeCaptured = $this->mockChargeData($chargeId, ['amount' => 100, 'captured' => true, 'paid' => true, 'paid_at' => time()]);

        $this->mockRequest('GET', '/v1/charges/' . $chargeId, [], $mockChargeUncaptured);
        $charge = Charge::retrieve($chargeId);
        $this->assertFalse($charge->captured);

        $captureUrl = '/v1/charges/' . $chargeId . '/capture';
        $captureParams = ['amount' => $captureAmount];
        $this->mockRequest('POST', $captureUrl, $captureParams, $mockChargeCaptured);
        $capturedCharge = $charge->capture($captureParams);

        $this->assertInstanceOf(Charge::class, $capturedCharge);
        $this->assertTrue($capturedCharge->captured);
        $this->assertTrue($charge->captured);
        $this->assertSame(100, $capturedCharge->amount);
    }

    public function testRefund()
    {
        $this->setUpMockRequest();
        $chargeId = 'ch_refund_test';
        $initialAmount = 100;
        $refundAmount1 = 50;
        $refundReason1 = 'foo bar 1';
        $refundReason2 = 'foo bar 2';

        $mockChargeInitial = $this->mockChargeData($chargeId, ['amount' => $initialAmount]);
        $mockChargeRefunded1 = $this->mockChargeData($chargeId, [
            'amount' => $initialAmount,
            'refunded' => true,
            'amount_refunded' => $refundAmount1,
            'refund_reason' => $refundReason1
        ]);
        $mockChargeRefunded2 = $this->mockChargeData($chargeId, [
            'amount' => $initialAmount,
            'refunded' => true,
            'amount_refunded' => $initialAmount,
            'refund_reason' => $refundReason2
        ]);

        $this->mockRequest('GET', '/v1/charges/' . $chargeId, [], $mockChargeInitial);
        $charge = Charge::retrieve($chargeId);
        $this->assertTrue($charge->captured);
        $this->assertFalse($charge->refunded);

        $refundUrl = '/v1/charges/' . $chargeId . '/refund';
        $refundParams1 = ['amount' => $refundAmount1, 'refund_reason' => $refundReason1];
        $this->mockRequest('POST', $refundUrl, $refundParams1, $mockChargeRefunded1);
        $refundedChargePart = $charge->refund($refundParams1);

        $this->assertInstanceOf(Charge::class, $refundedChargePart);
        $this->assertTrue($refundedChargePart->refunded);
        $this->assertSame($refundReason1, $refundedChargePart->refund_reason);
        $this->assertSame($refundAmount1, $refundedChargePart->amount_refunded);
        $this->assertTrue($charge->refunded);
        $this->assertSame($refundReason1, $charge->refund_reason);
        $this->assertSame($refundAmount1, $charge->amount_refunded);

        $refundParams2 = ['refund_reason' => $refundReason2];
        $this->mockRequest('POST', $refundUrl, $refundParams2, $mockChargeRefunded2);
        $refundedChargeAll = $charge->refund($refundParams2);

        $this->assertInstanceOf(Charge::class, $refundedChargeAll);
        $this->assertTrue($refundedChargeAll->refunded);
        $this->assertSame($refundReason2, $refundedChargeAll->refund_reason);
        $this->assertSame($initialAmount, $refundedChargeAll->amount_refunded);
        $this->assertTrue($charge->refunded);
        $this->assertSame($refundReason2, $charge->refund_reason);
        $this->assertSame($initialAmount, $charge->amount_refunded);
    }

    public function testInvalidCard()
    {
        $this->setUpMockRequest();
        $tokenId = 'tok_invalid_card';
        $chargeParams = ['amount' => 100, 'currency' => self::CURRENCY, 'card' => $tokenId];

        $mockErrorResponse = [
            'error' => [
                'code' => 'card_declined',
                'message' => 'The card was declined.',
                'param' => 'card',
                'status' => 402,
                'type' => 'card_error'
            ]
        ];
        $this->mock->expects($this->once())
            ->method('request')
            ->with('post', 'https://api.pay.jp/v1/charges', $this->anything(), $chargeParams, false)
            ->willReturn([json_encode($mockErrorResponse), 402]);

        try {
            Charge::create($chargeParams);
            $this->fail("Expected Payjp\Error\Card exception was not thrown.");
        } catch (Error\Card $e) {
            $this->assertSame(402, $e->getHttpStatus());
            $this->assertSame('card_declined', $e->getJsonBody()['error']['code']);
            $this->assertSame('card', $e->param);
            $this->assertNotFalse(strpos($e->getMessage(), 'The card was declined.'));
        }
    }

    public function testInvalidAddressZipTest()
    {
        $this->setUpMockRequest();
        $tokenId = 'tok_invalid_zip';
        $chargeId = 'ch_invalid_zip';
        $chargeParams = ['amount' => 100, 'currency' => self::CURRENCY, 'card' => $tokenId];
        $mockCharge = $this->mockChargeData(
            $chargeId,
            [],
            ['address_zip_check' => 'failed']
        );

        $this->mockRequest('POST', '/v1/charges', $chargeParams, $mockCharge);

        $ch = Charge::create($chargeParams);

        $this->assertInstanceOf(Charge::class, $ch);
        $this->assertInstanceOf(Card::class, $ch->card);
        $this->assertSame('failed', $ch->card->address_zip_check);
    }

    public function testInvalidCvcTest()
    {
        $this->setUpMockRequest();
        $tokenId = 'tok_invalid_cvc';
        $chargeId = 'ch_invalid_cvc';
        $chargeParams = ['amount' => 100, 'currency' => self::CURRENCY, 'card' => $tokenId];
        $mockCharge = $this->mockChargeData(
            $chargeId,
            [],
            ['cvc_check' => 'failed']
        );

        $this->mockRequest('POST', '/v1/charges', $chargeParams, $mockCharge);

        $ch = Charge::create($chargeParams);

        $this->assertInstanceOf(Charge::class, $ch);
        $this->assertInstanceOf(Card::class, $ch->card);
        $this->assertSame('failed', $ch->card->cvc_check);
    }

    public function testUnavailableCvcTest()
    {
        $this->setUpMockRequest();
        $tokenId = 'tok_unavail_cvc';
        $chargeId = 'ch_unavail_cvc';
        $chargeParams = ['amount' => 100, 'currency' => self::CURRENCY, 'card' => $tokenId];
        $mockCharge = $this->mockChargeData(
            $chargeId,
            [],
            ['cvc_check' => 'unavailable']
        );

        $this->mockRequest('POST', '/v1/charges', $chargeParams, $mockCharge);

        $ch = Charge::create($chargeParams);

        $this->assertInstanceOf(Charge::class, $ch);
        $this->assertInstanceOf(Card::class, $ch->card);
        $this->assertSame('unavailable', $ch->card->cvc_check);
    }
}
