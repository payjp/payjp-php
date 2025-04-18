<?php

namespace Payjp;

class TransferTest extends TestCase
{
    /**
     * Generate mock transfer data.
     */
    private function mockTransferData($id = 'tr_test_1', $attributes = [])
    {
        $base = [
            'amount' => 1000,
            'carried_balance' => null,
            'charges' => [
                'count' => 1,
                'data' => [
                    [
                        'amount' => 1000,
                        'amount_refunded' => 0,
                        'captured' => true,
                        'captured_at' => 1441706750,
                        'card' => [
                            'address_city' => null,
                            'address_line1' => null,
                            'address_line2' => null,
                            'address_state' => null,
                            'address_zip' => null,
                            'address_zip_check' => 'unchecked',
                            'brand' => 'Visa',
                            'country' => null,
                            'created' => 1441706750,
                            'customer' => null,
                            'cvc_check' => 'unchecked',
                            'exp_month' => 5,
                            'exp_year' => 2099,
                            'fingerprint' => 'e1d8225886e3a7211127df751c86787f',
                            'id' => 'car_93e59e9a9714134ef639865e2b9e',
                            'last4' => '4242',
                            'name' => null,
                            'object' => 'card',
                            'three_d_secure_status' => null
                        ],
                        'created' => 1441706750,
                        'currency' => 'jpy',
                        'customer' => 'cus_b92b879e60f62b532d6756ae12af',
                        'description' => null,
                        'expired_at' => null,
                        'failure_code' => null,
                        'failure_message' => null,
                        'fee_rate' => '3.00',
                        'id' => 'ch_60baaf2dc8f3e35684ebe2031a6e0',
                        'object' => 'charge',
                        'paid' => true,
                        'refund_reason' => null,
                        'refunded' => false,
                        'subscription' => null
                    ]
                ],
                'has_more' => false,
                'object' => 'list',
                'url' => "/v1/transfers/{$id}/charges"
            ],
            'created' => 1438354800,
            'currency' => 'jpy',
            'description' => null,
            'id' => $id,
            'livemode' => false,
            'object' => 'transfer',
            'scheduled_date' => '2015-09-16',
            'status' => 'pending',
            'summary' => [
                'charge_count' => 1,
                'charge_fee' => 0,
                'charge_gross' => 1000,
                'net' => 1000,
                'refund_amount' => 0,
                'refund_count' => 0,
                'dispute_amount' => 0,
                'dispute_count' => 0
            ],
            'term_end' => 1439650800,
            'term_start' => 1438354800,
            'transfer_amount' => null,
            'transfer_date' => null
        ];
        return array_merge($base, $attributes);
    }

    /**
     * Generate mock charge data specifically for transfer context.
     */
    private function mockChargeDataForTransfer($id = 'ch_test_for_tr', $transferId = 'tr_test_1', $attributes = [])
    {
        $base = [
            'amount' => 1000,
            'amount_refunded' => 0,
            'captured' => true,
            'captured_at' => 1583375140,
            'card' => [
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
                'id' => 'car_' . substr($id, 3), // Create a card ID based on charge ID
                'last4' => '4242',
                'name' => 'PAY TARO',
                'object' => 'card',
                'three_d_secure_status' => null
            ],
            'created' => 1583375140,
            'currency' => 'jpy',
            'customer' => 'cus_test_customer',
            'description' => null,
            'expired_at' => null,
            'failure_code' => null,
            'failure_message' => null,
            'fee_rate' => '3.00',
            'id' => $id,
            'livemode' => false,
            'object' => 'charge',
            'paid' => true,
            'refund_reason' => null,
            'refunded' => false,
            'subscription' => null,
            'transfer' => $transferId // Include reference to the transfer
        ];
        return array_merge($base, $attributes);
    }

    public function testAllRetrieve()
    {
        $transferId1 = 'tr_test_1';
        $transferId2 = 'tr_test_2';
        $mockTransfer1 = $this->mockTransferData($transferId1);
        $mockTransfer2 = $this->mockTransferData($transferId2, ['amount' => 15000]);

        $mockListResponse = [
            'object' => 'list',
            'data' => [$mockTransfer1, $mockTransfer2],
            'has_more' => false,
            'url' => '/v1/transfers'
        ];

        // Mock Transfer::all()
        $this->mockRequest('GET', '/v1/transfers', ['limit' => 3, 'offset' => 0], $mockListResponse);

        $transfers = Transfer::all([
            'limit' => 3,
            'offset' => 0
        ]);

        $this->assertInstanceOf('Payjp\Collection', $transfers);
        $this->assertCount(2, $transfers->data);
        $this->assertInstanceOf('Payjp\Transfer', $transfers->data[0]);
        $this->assertSame($transferId1, $transfers->data[0]->id);

        // Mock Transfer::retrieve() for the first transfer
        $this->mockRequest('GET', '/v1/transfers/' . $transferId1, [], $mockTransfer1);
        $transfer = Transfer::retrieve($transfers->data[0]->id);

        $this->assertInstanceOf('Payjp\Transfer', $transfer);
        $this->assertSame($transferId1, $transfer->id);
        $this->assertSame(1000, $transfer->amount); // Check amount from mock
    }

    public function testAllCharge()
    {
        $transferId = 'tr_test_charges';
        $mockTransfer = $this->mockTransferData($transferId);

        $chargeId1 = 'ch_tr_1';
        $chargeId2 = 'ch_tr_2';
        $mockCharge1 = $this->mockChargeDataForTransfer($chargeId1, $transferId);
        $mockCharge2 = $this->mockChargeDataForTransfer($chargeId2, $transferId, ['amount' => 3000]);

        $mockChargeListResponse = [
            'object' => 'list',
            'data' => [$mockCharge1, $mockCharge2],
            'has_more' => false,
            'url' => "/v1/transfers/{$transferId}/charges"
        ];

        $this->mockRequest('GET', '/v1/transfers/' . $transferId, [], $mockTransfer);
        $transfer = Transfer::retrieve($transferId);
        $this->assertInstanceOf('Payjp\Transfer', $transfer);

        $this->mockRequest('GET', "/v1/transfers/{$transferId}/charges", ['limit' => 3, 'offset' => 0], $mockChargeListResponse);
        $charges = $transfer->charges->all([
            'limit' => 3,
            'offset' => 0
        ]);

        $this->assertInstanceOf('Payjp\Collection', $charges);
        $this->assertCount(2, $charges->data);
        $this->assertInstanceOf('Payjp\Charge', $charges->data[0]);
        $this->assertSame($chargeId1, $charges->data[0]->id);
        $this->assertSame($transferId, $charges->data[0]->transfer); // Verify charge is linked
        $this->assertInstanceOf('Payjp\Charge', $charges->data[1]);
        $this->assertSame($chargeId2, $charges->data[1]->id);
        $this->assertSame(3000, $charges->data[1]->amount);
    }
}
