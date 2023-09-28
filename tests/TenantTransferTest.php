<?php

namespace Payjp;

class TenantTransferTest extends TestCase
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

    private function managedDownloadUrlResource()
    {
        return array(
            'object' => 'statement_download_url',
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
        $expectedTenantTransferId = 'ten_0d08780a33ab77f1c911a1b7286bd';
        $expectedStatementId = 'st_0d08780a33ab77f1c911a1b7286bd';
        $this->mockRequest('GET', '/v1/tenant_transfers/' . $expectedTenantTransferId, array(), $this->managedStatementResource($expectedStatementId));
        $this->mockRequest('POST', '/v1/tenant_transfers/' . $expectedTenantTransferId . '/statement_urls', array(), $this->managedDownloadUrlResource());
        $statementUrls = TenantTransfer::retrieve($expectedTenantTransferId)->statementUrls->create();
        $this->assertSame('statement_download_url', $statementUrls->object);
        $this->assertTrue($statementUrls->expires > 0);
        $this->assertNotEmpty($statementUrls->url);
    }
}
