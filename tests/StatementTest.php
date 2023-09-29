<?php

namespace Payjp;

class StatementTest extends TestCase
{
    private function managedStatementResource($id)
    {
        return array(
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
            'object' => 'statement',
            'title' => null
        );
    }

    private function managedStatementResources($ids)
    {
        return array(
            'count' => count($ids),
            'data' => array_map(function ($id) {
                return $this->managedStatementResource($id);
            }, $ids),
            'has_more' => false,
            'object' => 'list',
            'url' => '/v1/statements'
        );
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
        $this->mockRequest('GET', '/v1/statements/' . $expectedStatementId, array(), $this->managedStatementResource($expectedStatementId));
        $statement = Statement::retrieve($expectedStatementId);
        $this->assertSame($expectedStatementId, $statement->id);
    }

    public function testAll()
    {
        $expectedStatementIds = array('st_6b7d642291873e7b97e9175d7d6b8', 'st_0d08780a33ab77f1c911a1b7286bd');
        $this->mockRequest('GET', '/v1/statements', array(), $this->managedStatementResources($expectedStatementIds));
        $statements = Statement::all();
        $this->assertSame(2, $statements['count']);
        $this->assertCount(2, $statements['data']);
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
