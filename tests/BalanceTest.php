<?php

namespace Payjp;

class BalanceTest extends TestCase
{
    private function balanceResource($id)
    {
        return [
            'created' => 1438354800,
            'id' => $id,
            'livemode' => false,
            'net' => 1000,
            'object' => 'balance',
            'type' => 'collecting',
            'statements' => [
                'count' => 2,
                'data' => [
                    'created' => 1695892351,
                    'id' => 'st_178fd25dc7ab7b75906f5d4c4b0e6',
                    'items' => [
                        [
                            'amount' => 25,
                            'name' => 'チャージバックによる手数料返還',
                            'subject' => 'chargeback_fee_offset',
                            'tax_rate' => '0.00'
                        ], [
                            'amount' => -775,
                            'name' => 'チャージバック',
                            'subject' => 'chargeback',
                            'tax_rate' => '0.00'
                        ], [
                            'amount' => 36,
                            'name' => '返金による手数料返還',
                            'subject' => 'refund_fee_offset',
                            'tax_rate' => '0.00'
                        ], [
                            'amount' => -1800,
                            'name' => '返金',
                            'subject' => 'gross_refund',
                            'tax_rate' => '0.00'
                        ], [
                            'amount' => -75,
                            'name' => '決済手数料',
                            'subject' => 'fee',
                            'tax_rate' => '0.00'
                        ], [
                            'amount' => 3125,
                            'name' => '売上',
                            'subject' => 'gross_sales',
                            'tax_rate' => '0.00'
                        ]
                    ],
                    'object' => 'statement',
                    'livemode' => true,
                    'title' => null,
                    'updated' => 1695892351
                ], [
                    'created' => 1695892350,
                    'id' => 'st_b4a569b0122a7d08b298f198cf263',
                    'items' => [
                        [
                            'amount' => -10000,
                            'name' => 'プロプラン利用料',
                            'subject' => 'proplan',
                            'tax_rate' => '10.00'
                        ]
                    ],
                    'object' => 'statement',
                    'livemode' => true,
                    'title' => 'プロプラン月額料金',
                    'updated' => 1695892350
                ],
                'has_more' => false,
                'object' => 'list',
                'url' => '/v1/statements'
            ],
            'closed' => false,
            'due_date' => null,
            'bank_info' => [
                'bank_code' => '0000',
                'bank_branch_code' => '123',
                'bank_account_type' => '普通',
                'bank_account_number' => '1234567',
                'bank_account_holder_name' => 'ペイ　タロウ',
                'bank_account_status' => 'pending'
            ]
        ];
    }

    private function balancesResource($ids = [])
    {
        return [
            'count' => count($ids),
            'data' => array_map(function ($id) {
                return $this->balanceResource($id);
            }, $ids),
            'has_more' => false,
            'object' => 'list',
            'url' => '/v1/terms',
        ];
    }

    public function testRetrieve()
    {
        $expectedBalanceId = 'ba_sample1';
        $expectedBalanceResource = $this->balanceResource($expectedBalanceId);
        $this->mockRequest('GET', "/v1/balances/$expectedBalanceId", [], $expectedBalanceResource);
        $balance = Balance::retrieve($expectedBalanceId);
        $this->assertSame($expectedBalanceId, $balance->id);
        $this->assertSame($expectedBalanceResource['created'], $balance->created);
        $this->assertSame($expectedBalanceResource['livemode'], $balance->livemode);
        $this->assertSame($expectedBalanceResource['net'], $balance->net);
        $this->assertSame($expectedBalanceResource['object'], $balance->object);
        $this->assertSame($expectedBalanceResource['type'], $balance->type);
        $this->assertSame($expectedBalanceResource['closed'], $balance->closed);
        $this->assertSame($expectedBalanceResource['due_date'], $balance->due_date);

        $this->assertInstanceOf(PayjpObject::class, $balance->bank_info);
        $this->assertSame($expectedBalanceResource['bank_info']['bank_code'], $balance->bank_info->bank_code);
        $this->assertSame($expectedBalanceResource['bank_info']['bank_branch_code'], $balance->bank_info->bank_branch_code);
        $this->assertSame($expectedBalanceResource['bank_info']['bank_account_type'], $balance->bank_info->bank_account_type);
        $this->assertSame($expectedBalanceResource['bank_info']['bank_account_number'], $balance->bank_info->bank_account_number);
        $this->assertSame($expectedBalanceResource['bank_info']['bank_account_holder_name'], $balance->bank_info->bank_account_holder_name);
        $this->assertSame($expectedBalanceResource['bank_info']['bank_account_status'], $balance->bank_info->bank_account_status);

        $this->assertInstanceOf(Collection::class, $balance->statements);
        $this->assertSame($expectedBalanceResource['statements']['count'], $balance->statements->count);
        $this->assertSame($expectedBalanceResource['statements']['has_more'], $balance->statements->has_more);
        $this->assertSame($expectedBalanceResource['statements']['object'], $balance->statements->object);
        $this->assertSame($expectedBalanceResource['statements']['url'], $balance->statements->url);

        $this->assertInstanceOf(Statement::class, $balance->statements->data);
    }

    public function testAll()
    {
        $expectedBalanceIds = ['ba_sample1', 'ba_sample2'];
        $this->mockRequest('GET', '/v1/balances', [], $this->balancesResource($expectedBalanceIds));
        $balances = Balance::all();
        $this->assertSame(count($expectedBalanceIds), $balances['count']);
        $this->assertCount(count($expectedBalanceIds), $balances['data']);
        foreach ($balances['data'] as $index => $balance) {
            $this->assertInstanceOf(Balance::class, $balance);
            $this->assertSame($expectedBalanceIds[$index], $balance->id);
        }
    }
}
