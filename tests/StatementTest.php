<?php

namespace Payjp;

class StatementTest extends TestCase
{
    private function termResource($id)
    {
        return [
            'created' => 1438354800,
            'id' => $id,
            'livemode' => false,
            'object' => 'term',
            'charge_count' => 158,
            'refund_count' => 25,
            'dispute_count' => 2,
            'end_at' => 1439650800,
            'start_at' => 1438354800,
        ];
    }

    private function managedStatementResource($id)
    {
        return [
            'balance_id' => 'ba_sample',
            'created' => time(),
            'id' => $id,
            'items' => [
                [
                    'amount' => 282358654,
                    'name' => '売上',
                    'subject' => 'gross_sales',
                    'tax_rate' => '0.00'
                ],
                [
                    'amount' => -65699624,
                    'name' => '返金',
                    'subject' => 'gross_refund',
                    'tax_rate' => '0.00'
                ],
                [
                    'amount' => -7054912,
                    'name' => '決済手数料',
                    'subject' => 'fee',
                    'tax_rate' => '0.10'
                ],
                [
                    'amount' => 1644315,
                    'name' => '返金による手数料返還',
                    'subject' => 'refund_fee_offset',
                    'tax_rate' => '0.10'
                ]
            ],
            'livemode' => false,
            'net' => 211248433,
            'object' => 'statement',
            'tenant_id' => 'ten_sample',
            'type' => 'sales',
            'term' => $this->termResource('tm_sample'),
            'title' => null,
            'updated' => 1695892351,
        ];
    }

    private function managedStatementResources($ids)
    {
        return [
            'count' => count($ids),
            'data' => array_map(function ($id) {
                return $this->managedStatementResource($id);
            }, $ids),
            'has_more' => false,
            'object' => 'list',
            'url' => '/v1/statements'
        ];
    }

    private function managedDownloadUrlResource()
    {
        return array(
            'object' => 'statement_url',
            'url' => 'https://pay.jp/_/statements/a845383731564192xxxxxxxxxxxxxxxx',
            'expires' => 1695796301,
        );
    }

    public function testRetrieve()
    {
        $expectedStatementId = 'st_0d08780a33ab77f1c911a1b7286bd';
        $expectedStatementResource = $this->managedStatementResource($expectedStatementId);
        $this->mockRequest('GET', '/v1/statements/' . $expectedStatementId, [], $expectedStatementResource);
        $statement = Statement::retrieve($expectedStatementId);
        $this->assertSame($expectedStatementResource['balance_id'], $statement->balance_id);
        $this->assertSame($expectedStatementResource['created'], $statement->created);
        $this->assertSame($expectedStatementId, $statement->id);
        $this->assertSame($expectedStatementResource['livemode'], $statement->livemode);
        $this->assertSame($expectedStatementResource['net'], $statement->net);
        $this->assertSame($expectedStatementResource['object'], $statement->object);
        $this->assertSame($expectedStatementResource['tenant_id'], $statement->tenant_id);
        $this->assertSame($expectedStatementResource['title'], $statement->title);
        $this->assertSame($expectedStatementResource['type'], $statement->type);
        $this->assertSame($expectedStatementResource['updated'], $statement->updated);
        $this->assertInstanceOf(Term::class, $statement->term);
        foreach ($statement->items as $item) {
            $this->assertArrayHasKey('amount', $item);
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('subject', $item);
            $this->assertArrayHasKey('tax_rate', $item);
        }
    }

    public function testAll()
    {
        $expectedStatementIds = array('st_6b7d642291873e7b97e9175d7d6b8', 'st_0d08780a33ab77f1c911a1b7286bd');
        $this->mockRequest('GET', '/v1/statements', array(), $this->managedStatementResources($expectedStatementIds));
        $statements = Statement::all();
        $this->assertSame(count($expectedStatementIds), $statements['count']);
        $this->assertCount(count($expectedStatementIds), $statements['data']);
        $this->assertSame($expectedStatementIds[0], $statements['data'][0]->id);
        $this->assertSame($expectedStatementIds[1], $statements['data'][1]->id);
    }

    public function testStatementUrls()
    {
        $expectedStatementId = 'st_0d08780a33ab77f1c911a1b7286bd';
        $this->mockRequest('GET', '/v1/statements/' . $expectedStatementId, array(), $this->managedStatementResource($expectedStatementId));
        $this->mockRequest('POST', '/v1/statements/' . $expectedStatementId . '/statement_urls', array(), $this->managedDownloadUrlResource());
        $statementUrls = Statement::retrieve($expectedStatementId)->statementUrls->create();
        $this->assertSame('statement_url', $statementUrls->object);
        $this->assertTrue($statementUrls->expires > 0);
        $this->assertNotEmpty($statementUrls->url);
    }
}
