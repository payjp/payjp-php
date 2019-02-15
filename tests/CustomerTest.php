<?php

namespace Payjp;

class CustomerTest extends TestCase
{
    // POST/customers
    public function testCreate()
    {
        $attribute = array(
                        'email' => 'gdb@pay.jp',
                        'description' => 'foo bar'
        );
        
        $customer = self::createTestCustomer($attribute);
        $this->assertSame($attribute['email'], $customer->email);
        $this->assertSame($attribute['description'], $customer->description);
    }
        
    // GET/customers/:id
    public function testRetrieve()
    {
        $customer = self::createTestCustomer();
        $retrieve_customer = Customer::retrieve($customer->id);
        
        $this->assertSame($customer->id, $retrieve_customer->id);
    }
    
    // GET/customers/
    public function testAll()
    {
        $customers = Customer::all(
            array(
                'limit' => 3,
                'offset' => 10
            )
        );
    }
    
    // DELETE/customers/:id
    public function testDeletion()
    {
        $customer = self::createTestCustomer();
        $id = $customer->id;
        
        $delete_customer = $customer->delete();

        $this->assertSame($id, $customer->id);
        $this->assertFalse($customer->livemode);
        $this->assertTrue($customer->deleted);
        
        $this->assertNull($customer['active_card']);
        
        $this->assertSame($id, $delete_customer->id);
        $this->assertFalse($delete_customer->livemode);
        $this->assertTrue($delete_customer->deleted);
        
        $this->assertNull($delete_customer['active_card']);
    }
        
    //POST /customers/:id
    public function testSave()
    {
        $customer = self::createTestCustomer();

        $customer->email = 'gdb@pay.jp';
        $customer->save();
        $this->assertSame($customer->email, 'gdb@pay.jp');

        $payjpCustomer = Customer::retrieve($customer->id);
        $this->assertSame($customer->email, $payjpCustomer->email);
        $this->assertSame('gdb@pay.jp', $customer->email);

        Payjp::setApiKey(null);
        $customer = Customer::create(null, self::API_KEY);
        $customer->email = 'gdb@pay.jp';
        $customer->save();

        self::authorizeFromEnv();
        $updatedCustomer = Customer::retrieve($customer->id);
        $this->assertSame($updatedCustomer->email, 'gdb@pay.jp');
        $this->assertSame('gdb@pay.jp', $customer->email);
    }

    /**
     * @expectedException Payjp\Error\InvalidRequest
     */
    public function testBogusAttribute()
    {
        $customer = self::createTestCustomer();
        $customer->bogus = 'bogus';
        $customer->save();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateDescriptionEmpty()
    {
        $customer = self::createTestCustomer();
        $customer->description = '';
        
        $customer->save();
        
        $updatedCustomer = Customer::retrieve($customer->id);
        $this->assertSame('123', $updatedCustomer->description);
    }
        
    public function testUpdateDescriptionNull()
    {
        $customer = self::createTestCustomer(array('description' => 'foo bar'));
        $customer->description = null;
    
        $customer->save();
    
        $updatedCustomer = Customer::retrieve($customer->id);
        $this->assertSame('', $updatedCustomer->description);
        $this->assertSame('', $customer->description);
    }
    
    //POST /customers/:id/cards
    //GET /customers/:id/cards
    public function testCustomerAddCard()
    {
        $customer = $this->createTestCustomer();
        
        $defaultCard = $customer->cards->data[0];

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 12,
            "exp_year" => date('Y') + 3,
            "cvc" => "314"
            ]
        ];

        $token = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);
         
        $params2 =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 7,
            "exp_year" => date('Y') + 3,
            "cvc" => "314"
            ]
        ];

        $card = Token::create($params2, $options = ['payjp_direct_token_generate' => 'true']);
        
        $createdCard = $customer->cards->create(array("card" => $token->id));
        $createdCard_2 = $customer->cards->create(array("card" => $card->id));
        
        $updatedCustomer = Customer::retrieve($customer->id);
        $cardList = $updatedCustomer->cards->all();
        $this->assertSame(count($cardList["data"]), 3);

        $this->assertSame($createdCard_2->id, $cardList["data"][0]->id);
        $this->assertSame($createdCard->id, $cardList["data"][1]->id);
        $this->assertSame($defaultCard->id, $cardList["data"][2]->id);
         
        $card = $customer->cards->retrieve($cardList["data"][1]->id);
        $this->assertSame($card->id, $cardList["data"][1]->id);
        
        $updatedCustomer = Customer::retrieve($customer->id);
        $cardList = $updatedCustomer->cards->all(
            array(
                'limit' => 1,
                'offset' => 1
            )
        );
        $this->assertSame(count($cardList["data"]), 1);
    }
    
    //POST /customers/:id/cards/:card_id
    public function testCustomerUpdateCard()
    {
        $customer = $this->createTestCustomer();
    
        $cards = $customer->cards->all();
        $this->assertSame(count($cards["data"]), 1);
    
        $card = $cards['data'][0];
        $card->name = "Littleorc";
        $card->save();
    
        $updatedCustomer = Customer::retrieve($customer->id);
        $cardList = $updatedCustomer->cards->all();
        $this->assertSame($cardList["data"][0]->name, "Littleorc");
    }
    
    public function testCustomerDeleteCard()
    {
        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 7,
            "exp_year" => date('Y') + 3,
            "cvc" => "314"
            ]
        ];

        $token = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);
    
        $customer = $this->createTestCustomer();
        $createdCard = $customer->cards->create(array("card" => $token->id));
    
        $updatedCustomer = Customer::retrieve($customer->id);
        $cardList = $updatedCustomer->cards->all();
        $this->assertSame(count($cardList["data"]), 2);
    
        $deleteStatus = $updatedCustomer->cards->retrieve($createdCard->id)->delete();
        $this->assertTrue($deleteStatus->deleted);
    
        $postDeleteCustomer = Customer::retrieve($customer->id);
        $postDeleteCards = $postDeleteCustomer->cards->all();
        $this->assertSame(count($postDeleteCards["data"]), 1);
        
        $cardList = $updatedCustomer->cards->all();
        $this->assertSame(count($cardList["data"]), 1);
    }
    
    public function testCustomerSubscriptionAllRetrieve()
    {
        $planID = 'gold-' . self::randomString();
        self::retrieveOrCreatePlan($planID);
        
        $customer = self::createTestCustomer();
        
        $subscription = Subscription::create(
            array(
                'customer' => $customer->id,
                'plan' => $planID
            )
        );
        
        $planID_2 = 'gold-2-' . self::randomString();
        self::retrieveOrCreatePlan($planID_2);

        $subscription_2 = Subscription::create(
            array(
                'customer' => $customer->id,
                'plan' => $planID_2
            )
        );
        
        $customerRetrive = Customer::retrieve($customer->id);
        $subscriptions = $customerRetrive->subscriptions->all();

        $this->assertSame($subscription_2->id, $subscriptions['data'][0]->id);
        $this->assertSame($subscription->id, $subscriptions['data'][1]->id);
         
        $this->assertSame(2, count($subscriptions['data']));
        $this->assertSame($customer->id, $subscriptions['data'][0]->customer);
        $this->assertSame($planID_2, $subscriptions['data'][0]->plan->id);
        
        $subscriptionRetrieve = $customerRetrive->subscriptions->retrieve($subscription->id);
        $this->assertSame($subscription->id, $subscriptionRetrieve->id);
        $this->assertSame($planID, $subscriptionRetrieve->plan->id);
    }
    
    public function testCustomerChargeAll()
    {
        $planID = 'gold-' . self::randomString();
        self::retrieveOrCreatePlan($planID);
      
        $customer = self::createTestCustomer();
              
        $charge = Charge::create(
            array(
                'amount' => 1000,
                'currency' => self::CURRENCY,
                'customer' => $customer->id
            )
        );
            
        $charges = $customer->charges();
            
        $this->assertSame(1, count($charges['data']));
        $this->assertSame($charge->id, $charges['data'][0]->id);

        $charge_2 = Charge::create(
            array(
                'amount' => 1500,
                'currency' => self::CURRENCY,
                 'customer' => $customer->id
            )
        );
    
        $charges_2 = $customer->charges();
                
        $this->assertSame(2, count($charges_2['data']));
        $this->assertSame($charge_2->id, $charges_2['data'][0]->id);
        $this->assertSame($charge->id, $charges_2['data'][1]->id);
    }
}
