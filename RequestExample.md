# Payjp-php Request Example


## 支払い(Charges)


### post支払いを作成

```php
\Payjp\Payjp::setApiKey('sk_test_c62fade9d045b54cd76d7036');
$charge = \Payjp\Charge::create(array(
  'card' => 'token_id_by_Checkout_or_payjp-js',
  'amount' => 2000,
  'currency' => 'jpy'
));
```


### get支払い情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Charge::retrieve("ch_fa990a4c10672a93053a774730b0a");

### post支払い情報を更新

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $ch = \Payjp\Charge::retrieve("ch_fa990a4c10672a93053a774730b0a");
    $ch->description = "Updated";
    $ch->save();

### post返金する

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $ch = \Payjp\Charge::retrieve("ch_fa990a4c10672a93053a774730b0a");
    $ch->refund();

### post支払い処理を確定する

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $ch = \Payjp\Charge::retrieve("ch_fa990a4c10672a93053a774730b0a");
    $ch->capture();

### get支払いリストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Charge::all(array("limit" => 3, "offset" => 10));


## 顧客 (CUSTOMERS)

### post顧客を作成

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Customer::create(array(
            "description" => "test"
    ));

### get顧客情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Customer::retrieve("cus_121673955bd7aa144de5a8f6c262");

### post顧客情報を更新

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_121673955bd7aa144de5a8f6c262");
    $cu->email = "added@email.com";
    $cu->save();

### delete顧客を削除

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_121673955bd7aa144de5a8f6c262");
    $cu->delete();

### get顧客リストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Customer::all(array("limit" => 3, "offset" => 10));

### post顧客のカードを作成

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517");

    $card = array(
            "number" => "4242424242424242",
            "exp_year" => "2020",
            "exp_month" => "02"
    );

    $cu->cards->create($card);

### get顧客のカード情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517");
    $cu->cards->retrieve("car_f7d9fa98594dc7c2e42bfcd641ff");

### post顧客のカードを更新

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517");
    $card = $cu->cards->retrieve("car_f7d9fa98594dc7c2e42bfcd641ff");
    $card->exp_year = "2026";
    $card->exp_month = "05";
    $card->save();

### delete顧客のカードを削除

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517");
    $card = $cu->cards->retrieve("car_f7d9fa98594dc7c2e42bfcd641ff");
    $card->delete();

### get顧客のカードリストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517")->cards->all(array("limit"=>3, "offset"=>1));

### get顧客の定期購入情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $cu = \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517");
    $cu->subscription->retrieve("sub_567a1e44562932ec1a7682d746e0");

### get顧客の定期購入リストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Customer::retrieve("cus_4df4b5ed720933f4fb9e28857517")->subscription->all(array("limit"=>3));


## プラン (PLANS)

### postプランを作成

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Plan::create(array(
            "amount" => 500,
            "currency" => "jpy",
            "interval" => "month",
            "trial_days" => 30,
    ));

### getプラン情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Plan::retrieve("pln_45dd3268a18b2837d52861716260");

### postプランを更新

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $p = \Payjp\Plan::retrieve("pln_45dd3268a18b2837d52861716260");
    $p->name = "NewPlan";
    $p->save();

### deleteプランを削除

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $p = \Payjp\Plan::retrieve("pln_45dd3268a18b2837d52861716260");
    $p->delete();

### getプランリストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Plan::all(array("limit" => 3));


## 定期購入 (SUBSCRIPTIONS)

### post定期購入を作成

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Subscription::create(
            array(
                    "customer" => "cus_4df4b5ed720933f4fb9e28857517",
                    "plan" => "pln_9589006d14aad86aafeceac06b60"
            )
    );

### get定期購入情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Subscription::retrieve("sub_567a1e44562932ec1a7682d746e0");

### post定期購入を更新

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $su = \Payjp\Subscription::retrieve("sub_567a1e44562932ec1a7682d746e0");
    $su->plan = "pln_68e6a67f582462c223ca693bc549";
    $su->save();

### post定期購入を停止

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $su = \Payjp\Subscription::retrieve("sub_567a1e44562932ec1a7682d746e0");
    $su->pause();

### post定期購入を再開

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $su = \Payjp\Subscription::retrieve("sub_567a1e44562932ec1a7682d746e0");
    $su->resume();

### post定期購入をキャンセル

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $su = \Payjp\Subscription::retrieve("sub_567a1e44562932ec1a7682d746e0");
    $su->cancel();

### delete定期購入を削除

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    $su = \Payjp\Subscription::retrieve("sub_567a1e44562932ec1a7682d746e0");
    $su->delete();

### get定期購入のリストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Subscription::all(array("limit" => 3));


## トークン (TOKENS)

### テストモードでTokenを発行する

```php
\Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

$params = [
    'card' => [
        "number" => "4242424242424242",
        "exp_month" => "12",
        "exp_year" => "2020",
    ]
];

\Payjp\Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);
```


### getトークン情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Token::retrieve("tok_eff34b780cbebd61e87f09ecc9c6");


## 入金 (TRANSFERS)

### get入金情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Transfer::retrieve("tr_8f0c0fe2c9f8a47f9d18f03959ba1");

### get入金リストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Transfer::all(array("limit" => 3));

### get入金の内訳を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Transfer::retrieve("tr_8f0c0fe2c9f8a47f9d18f03959ba1")->charges->all(array("limit"=>3));


## イベント (EVENTS)

### getイベント情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Event::retrieve("evnt_2f7436fe0017098bc8d22221d1e");

### getイベントリストを取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Event::all(array("limit"=>3, "offset"=>10));


## アカウント (ACCOUNTS)

### getアカウント情報を取得

    \Payjp\Payjp::setApiKey("sk_test_c62fade9d045b54cd76d7036");

    \Payjp\Account::retrieve();
