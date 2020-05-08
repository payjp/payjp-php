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
    global $tenantIds;
    foreach ($tenantIds as $id) {
        try {
            \Payjp\Tenant::retrieve($id)->delete();
        } catch (\Payjp\Error\InvalidRequest $e) {
        }
    }
}

$tenantIds = ['test1', 'test2'];
$tenantParams = array(
    "id" => null,
    "name" => "test",
    "platform_fee_rate" => "10.00",
    "minimum_transfer_amount" => 1000,
    "bank_account_holder_name" => "ヤマダ タロウ",
    "bank_code" => "0001",
    "bank_branch_code" => "001",
    "bank_account_type" => "普通",
    "bank_account_number" => "0001000",
);
$invalidMinimumTransferAmount = array(
    "value" => 500,
    "code" => 'invalid_numerical_value',
);

cleanup();

// create
$tenantParams['id'] = $tenantIds[0];
$te = \Payjp\Tenant::create($tenantParams);
assert($te->object === 'tenant', 'Can create');
assert($te->id === $tenantIds[0], 'Can create');
$tenantParams['id'] = $tenantIds[1];
$te = $te->create($tenantParams);
assert($te->object === 'tenant', 'Can create');
assert($te->id === $tenantIds[1], 'Can create');

// retrieve
$te = \Payjp\Tenant::retrieve($tenantParams['id']);
foreach ($tenantParams as $key => $value) {
    assert($te[$key] === $value, 'Can retrieve');
}
assert(gettype($te->created) === 'integer', 'Can retrieve');
assert(gettype($te->livemode) === 'boolean', 'Can retrieve');
assert(gettype($te->metadata) === 'array' || gettype($te->metadata) === 'NULL', 'Can retrieve');
assert($te->object === 'tenant', 'Can retrieve');

// update
$updateMinimumTransferAmount = 2000;
$te->minimum_transfer_amount = $updateMinimumTransferAmount;
$te->save();
assert($te->minimum_transfer_amount === $updateMinimumTransferAmount, 'Can update');

// invalid upadte
$te->minimum_transfer_amount = $invalidMinimumTransferAmount['value'];
try {
    $te->save();
    assert(false, 'Cannot update');
} catch (\Payjp\Error\InvalidRequest $e) {
    assert($e->getJsonBody()['error']['code'] === $invalidMinimumTransferAmount['code'], 'Cannot update');
}

// application_urls
if (strpos(\Payjp\Payjp::getApiKey(), 'sk_live_') === 0) {
    $applicationUrls = $te->application_urls->create();
    $url = $applicationUrls->url;
    assert($applicationUrls->object === 'application_url', 'Can get application_urls');
    assert(gettype($applicationUrls->expires) === 'integer', 'Can get application_urls');
    assert(preg_match('@^http(s)?://[^/]+/_/applications/start/\w+@', $url) === 1, 'Can get application_urls');
    $reApplicationUrls = $applicationUrls->create();
    assert($reApplicationUrls->object === 'application_url', 'Can re-get application_urls');
    assert(gettype($reApplicationUrls->expires) === 'integer', 'Can re-get application_urls');
    assert(preg_match('@^http(s)?://[^/]+/_/applications/start/\w+@', $reApplicationUrls->url) === 1, 'Can re-get application_urls');
    assert($reApplicationUrls->url !== $url, 'Can re-get application_urls');
}

// all
foreach (\Payjp\Tenant::all(array('limit' => count($tenantIds)))->data as $tenant) {
    // delete and invalid retrieve
    $id = $tenant->id;
    $tenant->delete();
    try {
        \Payjp\Tenant::retrieve($id);
        assert(false, 'Can delete');
    } catch (\Payjp\Error\InvalidRequest $e) {
        assert($e->getJsonBody()['error']['code'] === 'invalid_id', 'Can delete');
    }
}

// invalid create
$tenantParams['minimum_transfer_amount'] = $invalidMinimumTransferAmount['value'];
try {
    \Payjp\Tenant::create($tenantParams);
    assert(false, 'Cannot create');
} catch (\Payjp\Error\InvalidRequest $e) {
    assert($e->getJsonBody()['error']['code'] === $invalidMinimumTransferAmount['code'], 'Cannot create');
}

cleanup();
