<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class SubscriptionTest
 * @package Payjp\Tests
 */
class SubscriptionTestTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testSubscriptionRetrieve()
    {
        $res = $this->payjp->subscriptions->retrieve('sub_xxx');
        Phake::verify($this->payjp)->request('GET', 'subscriptions/sub_xxx');
    }

    public function testSubscriptionAll()
    {
        $res = $this->payjp->subscriptions->all();
        Phake::verify($this->payjp)->request('GET', 'subscriptions', []);
    }

    public function testSubscriptionCreate()
    {
        $params = [
            'customer' => 'cus_123',
            'plan' => 'pln_123',
        ];
        $res = $this->payjp->subscriptions->create($params);
        Phake::verify($this->payjp)->request('POST', 'subscriptions', $params);
    }

    public function testSubscriptionUpdate()
    {
        $params = [
            'plan' => 'pln_456',
        ];
        $res = $this->payjp->subscriptions->update('sub_xxx', $params);
        Phake::verify($this->payjp)->request('POST', 'subscriptions/sub_xxx', $params);
    }

    public function testSubscriptionResume()
    {
        $params = [
            'prorate' => true,
        ];
        $res = $this->payjp->subscriptions->resume('sub_xxx', $params);
        Phake::verify($this->payjp)->request('POST', 'subscriptions/sub_xxx/resume', $params);
    }

    public function testSubscriptionCancel()
    {
        $res = $this->payjp->subscriptions->cancel('sub_xxx');
        Phake::verify($this->payjp)->request('POST', 'subscriptions/sub_xxx/cancel');
    }

    public function testSubscriptionDelete()
    {
        $params = [
            'prorate' => true,
        ];
        $res = $this->payjp->subscriptions->delete('sub_xxx', $params);
        Phake::verify($this->payjp)->request('DELETE', 'subscriptions/sub_xxx', $params);
    }

    public function testCustomerSubscriptionList()
    {
        $res = $this->payjp->customers->subscriptions->all('cus_123');
        Phake::verify($this->payjp)->request('GET', 'customers/cus_123/subscriptions', []);
    }

    public function testCustomerSubscriptionRetrieve()
    {
        $res = $this->payjp->customers->subscriptions->retrieve('cus_123', 'sub_456');
        Phake::verify($this->payjp)->request('GET', 'customers/cus_123/subscriptions/sub_456');
    }
}
