<?php
// common setup
$autoloadPath = dirname(__FILE__) . '/../vendor/autoload.php';
$initPath = dirname(__FILE__) . '/../init.php';
require_once(getenv('AUTOLOAD') ? $autoloadPath : $initPath);

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
    if (getenv('TENANT_ID')) {
        return;
    }
    try {
        \Payjp\Tenant::retrieve('test')->delete();
    } catch (\Payjp\Error\InvalidRequest $e) {
    }
}

cleanup();
$tenantId = getenv('TENANT_ID');
// Tenant create
if ($tenantId === false) {
    $tenantParams = array(
        "id" => "test",
        "name" => "test",
        "platform_fee_rate" => "10.00",
        "minimum_transfer_amount" => 1000,
        "bank_account_holder_name" => "ヤマダ タロウ",
        "bank_code" => "0001",
        "bank_branch_code" => "001",
        "bank_account_type" => "普通",
        "bank_account_number" => "0001000",
    );
    $te = \Payjp\Tenant::create($tenantParams);
    $tenantId = $te->id;
}

// Charge
$chargeParams = array(
    "customer" => getenv('CUSTOMER_ID'),
    "amount" => 1000,
    "currency" => "jpy",
    "platform_fee" => null,
    "tenant" => $tenantId
);
$ch = \Payjp\Charge::create($chargeParams);
try {
    assert($ch->total_platform_fee > 0, 'Can charge');
} catch (Exception $e) {
    $ch->refund();
    assert(false, 'Can charge');
}
assert($ch->refund()->refunded, 'Can refund');

$chargeParams['platform_fee'] = 0;
$ch = \Payjp\Charge::create($chargeParams);
try {
    assert($ch->total_platform_fee === $chargeParams['platform_fee'], 'Can charge');
} catch (Exception $e) {
    $ch->refund();
    assert(false, 'Can charge');
}
assert($ch->refund()->refunded, 'Can refund');

$ch = \Payjp\Charge::all(array("tenant" => $ch->tenant));
assert(count($ch->data) >= 2, 'Can charge'); // todo 事前にcountをみて正確なassertに

cleanup();
