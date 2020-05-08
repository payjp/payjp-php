## How-to-exec

On repository root,

```$sh
$PAYJP_API_KEY=<your_sk_key> \
CUSTOMER_ID=<your_customer_id> \
php scenario/TenantForPlatform.php

$PAYJP_API_KEY=<your_sk_key> \
TENANT_ID=<your_tenant_id> \
php scenario/ChargeForPlatform.php

$PAYJP_API_KEY=<your_sk_key> \
TENANT_ID=<your_tenant_id> \
php scenario/TenantTransfer.php
```

## Environment variables

|key|value|available scenario|
|---|---|---|
|AUTOLOAD|free value to use composer autoload|all|
|ASSERT_OPTIONS|free value to exit whenever asserts fail|all|
|PAYJP_API_KEY|Payjp API key (sk_xxx)|全て|
|CUSTOMER_ID|customer id that has available cards|ChargeForPlatform|
|TENANT_ID|tenant id|ChargeForPlatform, TenantTransfer|
