<?php

namespace Payjp;

class TenantTransferTest extends TestCase
{
    public function testUrl()
    {
        $this->assertSame(TenantTransfer::className(), 'tenant_transfer');
        $this->assertSame(TenantTransfer::classUrl(), '/v1/tenant_transfers');
    }
}
