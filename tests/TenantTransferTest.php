<?php

namespace Payjp;

class TenantTransferTest extends TestCase
{
    private function managedTenantTransferResource($tenantId, $tenantTransferId)
    {
        return array(
            'amount' => 0,
            'carried_balance' => null,
            'charges' => array(
                'count' => 0,
                'data' => [],
                'has_more' => false,
                'object' => 'list',
                'url' => 'https://api.pay.jp/v1/transfers/' . $tenantId . '/tenant_transfers/' . $tenantTransferId . '/charges',
            ),
            'created' => time(),
            'currency' => 'ja',
            'id' => $tenantTransferId,
            'livemode' => false,
            'object' => 'tenant_transfer',
            'scheduled_date' => '2015-09-16',
            'status' => 'pending',
            'summary' => array(
                'charge_count' => 0,
                'charge_fee' => 0,
                'total_platform_fee' => 0,
                'charge_gross' => 0,
                'net' => 0,
                'refund_amount' => 0,
                'refund_count' => 0,
                'dispute_amount' => 0,
                'dispute_count' => 0,
            ),
            'tenant_id' => $tenantId,
            'term_end' => 1439650800,
            'term_start' => 1438354800,
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

    public function testUrl()
    {
        $this->assertSame(TenantTransfer::className(), 'tenant_transfer');
        $this->assertSame(TenantTransfer::classUrl(), '/v1/tenant_transfers');
    }

    public function testStatementUrls()
    {
        $expectedTenantId = 'tr_8f0c0fe2c9f8a47f9d18f03959bxx';
        $expectedTenantTransferId = 'ten_tr_23748b8c95c79edff22a8b7b795xx';
        $this->mockRequest('GET', '/v1/tenant_transfers/' . $expectedTenantTransferId, array(), $this->managedTenantTransferResource($expectedTenantId, $expectedTenantTransferId));
        $this->mockRequest('POST', '/v1/tenant_transfers/' . $expectedTenantTransferId . '/statement_urls', array(), $this->managedDownloadUrlResource());
        $statementUrls = TenantTransfer::retrieve($expectedTenantTransferId)->statementUrls->create();
        $this->assertSame('statement_url', $statementUrls->object);
        $this->assertTrue($statementUrls->expires > 0);
        $this->assertNotEmpty($statementUrls->url);
    }
}
