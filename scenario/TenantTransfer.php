<?php
// common setup
$autoloadPath = dirname(__FILE__) . '/../vendor/autoload.php';
$initPath = dirname(__FILE__) . '/../init.php';
require_once(getenv('AUTOLOAD') ? $autoloadPath : $initPath);

// setup
if (getenv('PAYJP_API_URL')) {
    \PAYJP\PAYJP::$apiBase = getenv('PAYJP_API_URL');
}
\Payjp\Payjp::setApiKey(getenv('PAYJP_API_KEY'));
if (getenv('ASSERT_OPTIONS')) {
    assert_options(ASSERT_BAIL, true);
    assert_options(ASSERT_CALLBACK, 'cleanup');
}

function cleanup()
{
}

$tenantId = getenv('TENANT_ID');
$tenantTransfers = \Payjp\TenantTransfer::all(array('tenant' => $tenantId));
assert($tenantTransfers->url === '/v1/tenant_transfers', 'Can get tenant_transfers');
assert($tenantTransfers->object === 'list', 'Can get tenant_transfers');

foreach ($tenantTransfers->data as $tt) {
    assert($tenantId === $tt->tenant_id, 'Can get tenant_transfer');
    assert($tt->object === 'tenant_transfer', 'Can get tenant_transfer');
    $tt_chs = $tt->charges;
    assert($tt_chs->object === 'list', 'Can get tenant_transfer charges');
    foreach ($tt_chs->data as $ch) {
        assert($ch->tenant === $tenantId, 'Can get tenant_transfer charges for platform');
        assert($ch->platform_fee === null || gettype($ch->platform_fee) === 'integer', 'Can get tenant_transfer charges for platform');
        assert($ch->platform_fee_rate === null || gettype($ch->platform_fee_rate) === 'string', 'Can get tenant_transfer charges for platform');
        assert(gettype($ch->total_platform_fee) === 'integer', 'Can get tenant_transfer charges for platform');
    }
    // todo not work since/until
    // $chs = \Payjp\Charge::all(array(
    //     "tenant" => $tenantId,
    //     "since" => $tenantTransfer->term_start, // 1548979200
    //     "until" => $tenantTransfer->term_end, // 1551312000
    // ));
    // assert($chs->count === $tt_chs->count, 'Can get tenant_transfer charges');
}

// Platformer Transfers
$transfers = \Payjp\Transfer::all();
foreach($transfers->data as $transfer) {
    $chs = $transfer->charges->all();
    assert(count($chs->data) === $chs->count, 'Can get transfer charges for platform');
    assert($chs->url === '/v1/charges', 'Can get transfer charges for platform');
    assert($chs->object === 'list', 'Can get transfer charges for platform');

    foreach($chs->data as $ch) {
        assert(gettype($ch->tenant) === 'string', 'Can get transfer charges for platform');
        assert($ch->platform_fee === null || gettype($ch->platform_fee) === 'integer', 'Can get transfer charges for platform');
        assert($ch->platform_fee_rate === null || gettype($ch->platform_fee_rate) === 'string', 'Can get transfer charges for platform');
        assert(gettype($ch->total_platform_fee) === 'integer', 'Can get transfer charges for platform');
    }

    $tts = \Payjp\TenantTransfer::all(array('transfer' => $transfer->id));
    foreach($tts->data as $tt) {
        assert($tt->object === 'tenant_transfer', 'Can get tenant_transfer');
        $ttCharges = $tt->charges->all();
        assert($ttCharges->url === "/v1/tenant_transfers/$tt->id/charges", 'Can get tenanttransfers by transfer');
        foreach($ttCharges->data as $ch) {
            assert($ch->tenant === $tt->tenant_id, 'Can get tenant_transfer charges for platform');
            assert($ch->platform_fee === null || gettype($ch->platform_fee) === 'integer', 'Can get tenant_transfer charges for platform');
            assert($ch->platform_fee_rate === null || gettype($ch->platform_fee_rate) === 'string', 'Can get tenant_transfer charges for platform');
            assert(gettype($ch->total_platform_fee) === 'integer', 'Can get tenant_transfer charges for platform');
        }
    }
}

cleanup();
