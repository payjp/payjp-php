<?php

namespace Payjp;

class AccountTest extends TestCase
{
    private function managedAccountResponse($id)
    {
        return array(
            'accounts_enabled' => array('merchant', 'customer'),
            'created' => 1432965397,
                'customer' => array(
                        'cards' => array(
                                'count' => 1,
                                'data' => array(
                                        array(
                                                'address_city' => '\u8d64\u5742',
                                                'address_line1' => '7-4',
                                                'address_line2' => '203',
                                                'address_state' => '\u6e2f\u533a',
                                                'address_zip' => '1070050',
                                                'address_zip_check' => 'passed',
                                                'brand' => 'Visa',
                                                'country' => self::COUNTRY,
                                                'created' => 1432965397,
                                                'cvc_check' => 'passed',
                                                'exp_month' => 12,
                                                'exp_year' => 2016,
                                                'fingerprint' => 'e1d8225886e3a7211127df751c86787f',
                                                'id' => 'car_99abf74cb5527ff68233a8b836dd',
                                                'last4' => '4242',
                                                'livemode' => true,
                                                'name' => 'Test Hodler',
                                                'object' => 'card'
                                        )
                                ),
                                'object' => 'list',
                                'url' => '/v1/accounts/cards'
                        ),
                        'created' => 1432965397,
                        'default_card' => null,
                        'description' => 'user customer',
                        'email' => null,
                        'id' => 'acct_cus_38153121efdb7964dd1e147',
                        'object' => 'customer'
                ),
                'email' => 'firstuser@mail.com',
                'id' => $id,
                'merchant' => array(
                        'bank_enabled' => false,
                        'brands_accepted' => array(
                                        'Visa',
                                        'American Express',
                                        'MasterCard',
                                        'JCB',
                                        'Diners Club'
                        ),
                        'business_type' => 'personal',
                        'charge_type' => null,
                        'contact_phone' => null,
                        'country' => 'JP',
                        'created' => 1432965397,
                        'currencies_supported' => array(
                                        self::CURRENCY
                        ),
                        'default_currency' => self::CURRENCY,
                        'details_submitted' => false,
                        'id' => 'acct_mch_002418151ef82e49f6edee1',
                        'livemode_activated_at' => 1432965401,
                        'livemode_enabled' => true,
                        'object' => 'merchant',
                        'product_detail' => null,
                        'product_name' => null,
                        'product_type' => null,
                        'site_published' => false,
                        'url' => null
                ),
                'object' => 'account'
                        
        );
    }

    public function testBasicRetrieve()
    {
        $this->mockRequest('GET', '/v1/accounts', array(), $this->managedAccountResponse('acct_ABC'));
        $account = Account::retrieve();
        
        $this->assertSame('acct_ABC', $account->id);
        $this->assertSame('acct_cus_38153121efdb7964dd1e147', $account->customer->id);
        $this->assertSame('acct_mch_002418151ef82e49f6edee1', $account->merchant->id);
    }
    
    public function testRetrieve()
    {
        $account = Account::retrieve();
    }
}
