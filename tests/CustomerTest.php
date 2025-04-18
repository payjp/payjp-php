<?php

namespace Payjp;

class CustomerTest extends TestCase
{
    private function mockCustomerData($id = 'cus_test_1', $attributes = [])
    {
        $base = [
            'id' => $id,
            'object' => 'customer',
            'email' => null,
            'description' => 'test',
            'livemode' => false,
            'default_card' => null,
            'cards' => [
                'object' => 'list',
                'data' => [],
                'count' => 0,
                'has_more' => false,
                'url' => "/v1/customers/{$id}/cards"
            ],
            'subscriptions' => [
                'object' => 'list',
                'data' => [],
                'count' => 0,
                'has_more' => false,
                'url' => "/v1/customers/{$id}/subscriptions"
            ],
            'created' => 1433127983,
            'metadata' => null,
        ];
        return array_merge($base, $attributes);
    }

    private function mockCardData($id = 'car_default', $attributes = [])
    {
        return array_merge([
            'id' => $id,
            'object' => 'card',
            'brand' => 'Visa',
            'last4' => '4242',
            'exp_month' => 12,
            'exp_year' => date('Y') + 3,
            'name' => 'Test Card',
            'livemode' => false,
        ], $attributes);
    }

    private function mockSubscriptionData($id = 'sub_test_1', $customerId = 'cus_test_1', $planId = 'plan_test_1', $attributes = [])
    {
        return array_merge([
            'id' => $id,
            'object' => 'subscription',
            'customer' => $customerId,
            'plan' => ['id' => $planId],
            'status' => 'active',
        ], $attributes);
    }

    private function mockChargeData($id = 'ch_test_1', $customerId = 'cus_test_1', $attributes = [])
    {
        return array_merge([
            'id' => $id,
            'object' => 'charge',
            'amount' => 1000,
            'currency' => self::CURRENCY,
            'customer' => $customerId,
        ], $attributes);
    }

    public function testCreate()
    {
        $attribute = [
            'email' => 'example@pay.jp',
            'description' => 'foo bar'
        ];
        $mockCustomerData = $this->mockCustomerData('cus_created', $attribute);
        $this->mockRequest('POST', '/v1/customers', $attribute, $mockCustomerData);
        $customer = Customer::create($attribute);
        $this->assertSame($attribute['email'], $customer->email);
        $this->assertSame($attribute['description'], $customer->description);
    }

    public function testRetrieve()
    {
        $mockCustomerData = $this->mockCustomerData();
        $this->mockRequest('GET', '/v1/customers/cus_test_1', [], $mockCustomerData);
        $customer = Customer::retrieve('cus_test_1');
        $this->assertSame('cus_test_1', $customer->id);
    }

    public function testAll()
    {
        $mockCustomerData1 = $this->mockCustomerData('cus_test_1');
        $mockCustomerData2 = $this->mockCustomerData('cus_test_2');
        $mockCustomerData3 = $this->mockCustomerData('cus_test_3');
        $mockCustomersResponse = [
            'object' => 'list',
            'data' => [
                $mockCustomerData1,
                $mockCustomerData2,
                $mockCustomerData3,
            ],
            'has_more' => false,
            'url' => '/v1/customers'
        ];
        $this->mockRequest('GET', '/v1/customers', ['limit' => 3, 'offset' => 10], $mockCustomersResponse);
        $customers = Customer::all([
            'limit' => 3,
            'offset' => 10
        ]);
        $this->assertCount(3, $customers['data']);
    }

    public function testDeletion()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $mockDeletedData = $this->mockCustomerData('cus_test_1', [
            'deleted' => true,
            'livemode' => false,
            'default_card' => null
        ]);
        $customer = Customer::constructFrom($mockCustomerData, new \Payjp\Util\RequestOptions(self::API_KEY));
        $this->mockRequest('DELETE', '/v1/customers/cus_test_1', [], $mockDeletedData);
        $delete_customer = $customer->delete();
        $this->assertSame($customer->id, $delete_customer->id);
        $this->assertFalse($delete_customer->livemode);
        $this->assertTrue($delete_customer->deleted);
        $this->assertNull($delete_customer['default_card']);
    }

    public function testSave()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $updatedData = $this->mockCustomerData('cus_test_1', ['email' => 'example@pay.jp']);
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    ['email' => 'example@pay.jp'],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($updatedData), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $customer->email = 'example@pay.jp';
        $customer->save();
        $this->assertSame('example@pay.jp', $customer->email);
    }

    public function testBogusAttribute()
    {
        $this->expectException('\Payjp\Error\InvalidRequest');
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    ['bogus' => 'bogus'],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode(['error' => ['message' => 'Invalid request']]), 400]
            );
        $customer = Customer::retrieve('cus_test_1');
        $customer->bogus = 'bogus';
        $customer->save();
    }

    public function testUpdateDescriptionEmpty()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1', ['description' => '123']);
        $mockCustomerData2 = $this->mockCustomerData('cus_test_1', ['description' => '']);
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    ['description' => ''],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($mockCustomerData2), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $customer->description = '';
        $customer->save();
        $this->assertSame('', $customer->description);
    }

    public function testUpdateDescriptionNull()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1', ['description' => 'foo bar']);
        $updatedData = $this->mockCustomerData('cus_test_1', ['description' => '']);
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    ['description' => null],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($updatedData), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $customer->description = null;
        $customer->save();
        $this->assertSame('', $customer->description);
    }

    public function testCustomerAddCard()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $newCard1Data = $this->mockCardData('car_1');
        $newCard2Data = $this->mockCardData('car_2');
        $mockCardsResponse = [
            'object' => 'list',
            'data' => [ $newCard2Data, $newCard1Data, $this->mockCardData('car_default') ],
            'has_more' => false,
            'url' => '/v1/customers/cus_test_1/cards'
        ];
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(4))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards',
                    $this->anything(),
                    ['card' => 'tok_1'],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards',
                    $this->anything(),
                    ['card' => 'tok_2'],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards',
                    $this->anything(),
                    [],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($newCard1Data), 200],
                [json_encode($newCard2Data), 200],
                [json_encode($mockCardsResponse), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $createdCard = $customer->cards->create(['card' => 'tok_1']);
        $createdCard_2 = $customer->cards->create(['card' => 'tok_2']);
        $cardList = $customer->cards->all();
        $this->assertInstanceOf('Payjp\Collection', $cardList);
        $this->assertTrue(is_array($cardList->data));
        $this->assertCount(3, $cardList->data);
        $this->assertInstanceOf('Payjp\Card', $cardList->data[0]);
        $this->assertSame($createdCard_2->id, $cardList->data[0]->id);
        $this->assertInstanceOf('Payjp\Card', $cardList->data[1]);
        $this->assertSame($createdCard->id, $cardList->data[1]->id);
    }

    public function testCustomerUpdateCard()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $cardData = $this->mockCardData('car_1', ['customer' => 'cus_test_1']);
        $updatedCardData = $this->mockCardData('car_1', ['name' => 'Littleorc', 'customer' => 'cus_test_1']);
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(3))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards/car_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards/car_1',
                    $this->anything(),
                    ['name' => 'Littleorc'],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($cardData), 200],
                [json_encode($updatedCardData), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $cardObj = $customer->cards->retrieve('car_1');
        $this->assertInstanceOf('Payjp\Card', $cardObj);
        $cardObj->name = 'Littleorc';
        $cardObj->save();
        $this->assertSame('Littleorc', $cardObj->name);
    }

    public function testCustomerDeleteCard()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $createdCardData = $this->mockCardData('car_1', ['customer' => 'cus_test_1']);
        $deletedCardData = $this->mockCardData('car_1', ['deleted' => true, 'customer' => 'cus_test_1']);
        $mockCardsResponse = [
            'object' => 'list',
            'data' => [ $this->mockCardData('car_default'), $createdCardData ],
            'has_more' => false,
            'url' => '/v1/customers/cus_test_1/cards'
        ];
        $mockCardsAfterResponse = [
            'object' => 'list',
            'data' => [ $this->mockCardData('car_default') ],
            'has_more' => false,
            'url' => '/v1/customers/cus_test_1/cards'
        ];
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(6))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'post',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards',
                    $this->anything(),
                    ['card' => 'tok_1'],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards/car_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'delete',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards/car_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1/cards',
                    $this->anything(),
                    [],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($createdCardData), 200],
                [json_encode($mockCardsResponse), 200],
                [json_encode($createdCardData), 200],
                [json_encode($deletedCardData), 200],
                [json_encode($mockCardsAfterResponse), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $createdCardObj = $customer->cards->create(['card' => 'tok_1']);
        $cardList = $customer->cards->all();
        $deletedCardResult = $customer->cards->retrieve('car_1')->delete();
        $this->assertInstanceOf('Payjp\Card', $deletedCardResult);
        $postDeleteCards = $customer->cards->all();
        $this->assertInstanceOf('Payjp\Collection', $postDeleteCards);
        $this->assertTrue(is_array($postDeleteCards->data));
        $this->assertCount(1, $postDeleteCards->data);
        $this->assertTrue($deletedCardResult->deleted);
    }

    public function testCustomerSubscriptionAllRetrieve()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $sub1Data = $this->mockSubscriptionData('sub_1', 'cus_test_1', 'plan_1');
        $sub2Data = $this->mockSubscriptionData('sub_2', 'cus_test_1', 'plan_2');
        $mockSubsResponse = [
            'object' => 'list',
            'data' => [ $sub2Data, $sub1Data ],
            'has_more' => false,
            'url' => '/v1/customers/cus_test_1/subscriptions'
        ];
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1/subscriptions',
                    $this->anything(),
                    [],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($mockSubsResponse), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $subscriptions = $customer->subscriptions->all();
        $this->assertInstanceOf('Payjp\Collection', $subscriptions);
        $this->assertTrue(is_array($subscriptions->data));
        $this->assertCount(2, $subscriptions->data);
        $this->assertInstanceOf('Payjp\Subscription', $subscriptions->data[0]);
        $this->assertSame($sub2Data['id'], $subscriptions->data[0]->id);
        $this->assertInstanceOf('Payjp\Subscription', $subscriptions->data[1]);
        $this->assertSame($sub1Data['id'], $subscriptions->data[1]->id);
        $this->assertSame('cus_test_1', $subscriptions->data[0]->customer);
        $this->assertTrue(is_object($subscriptions->data[0]->plan));
        $this->assertSame('plan_2', $subscriptions->data[0]->plan->id);
    }

    public function testCustomerChargeAll()
    {
        $mockCustomerData = $this->mockCustomerData('cus_test_1');
        $charge1Data = $this->mockChargeData('ch_1', 'cus_test_1');
        $charge2Data = $this->mockChargeData('ch_2', 'cus_test_1', ['amount' => 1500]);
        $mockChargesResponse = [
            'object' => 'list',
            'data' => [ $charge2Data, $charge1Data ],
            'has_more' => false,
            'url' => '/v1/charges'
        ];
        $mock = $this->setUpMockRequest();
        $mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/customers/cus_test_1',
                    $this->anything(),
                    [],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/charges',
                    $this->anything(),
                    ['customer' => 'cus_test_1'],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockCustomerData), 200],
                [json_encode($mockChargesResponse), 200]
            );
        $customer = Customer::retrieve('cus_test_1');
        $charges = Charge::all(['customer' => $customer->id]);
        $this->assertInstanceOf('Payjp\Collection', $charges);
        $this->assertTrue(is_array($charges->data));
        $this->assertCount(2, $charges->data);
        $this->assertInstanceOf('Payjp\Charge', $charges->data[0]);
        $this->assertSame($charge2Data['id'], $charges->data[0]->id);
        $this->assertInstanceOf('Payjp\Charge', $charges->data[1]);
        $this->assertSame($charge1Data['id'], $charges->data[1]->id);
    }
}
