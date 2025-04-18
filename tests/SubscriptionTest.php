<?php

namespace Payjp;

class SubscriptionTest extends TestCase
{
    private function mockCustomerData($id = 'cus_test_sub', $attributes = [])
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

    private function mockPlanData($id = 'pln_test_1', $attributes = [])
    {
        $base = [
            'amount' => 500,
            'billing_day' => null,
            'created' => 1433127983,
            'currency' => 'jpy',
            'id' => $id,
            'interval' => 'month',
            'livemode' => false,
            'metadata' => null,
            'name' => null,
            'object' => 'plan',
            'trial_days' => 30
        ];
        return array_merge($base, $attributes);
    }

    private function mockSubscriptionData($id = 'sub_test_1', $customerId = 'cus_test_sub', $planId = 'pln_test_1', $attributes = [])
    {
        $base = [
            'id' => $id,
            'object' => 'subscription',
            'livemode' => false,
            'status' => 'active',
            'canceled_at' => null,
            'created' => 1433127983,
            'current_period_end' => 1435732422,
            'current_period_start' => 1433140422,
            'customer' => $customerId,
            'metadata' => null,
            'next_cycle_plan' => null,
            'paused_at' => null,
            'plan' => [
                'amount' => 1000,
                'billing_day' => null,
                'created' => 1432965397,
                'currency' => 'jpy',
                'id' => $planId,
                'livemode' => false,
                'metadata' => [],
                'interval' => 'month',
                'name' => 'test plan',
                'object' => 'plan',
                'trial_days' => 0
            ],
            'resumed_at' => null,
            'start' => 1433140422,
            'trial_end' => null,
            'trial_start' => null,
            'prorate' => false
        ];
        $merged = array_merge($base, $attributes);
        if (isset($attributes['plan']) && is_array($attributes['plan'])) {
            $merged['plan'] = array_merge($base['plan'], $attributes['plan']);
        }
        return $merged;
    }

    public function testCreateRetrieveUpdatePauseResumeCancelDelete()
    {
        $planID = 'pln_gold_test';
        $nextPlanID = 'pln_next_test';
        $customerID = 'cus_for_sub_test';
        $subID = 'sub_test_lifecycle';

        $mockCustomer = $this->mockCustomerData($customerID);
        $mockPlan = $this->mockPlanData($planID);
        $mockNextPlan = $this->mockPlanData($nextPlanID);

        $createParams = ['customer' => $customerID, 'plan' => $planID];
        $mockInitialSub = $this->mockSubscriptionData($subID, $customerID, $planID);
        $this->mockRequest('POST', '/v1/subscriptions', $createParams, $mockInitialSub);
        $sub = Subscription::create($createParams);

        $this->assertInstanceOf('Payjp\Subscription', $sub);
        $this->assertSame($subID, $sub->id);
        $this->assertSame('active', $sub->status);
        $this->assertSame($planID, $sub->plan->id);
        $this->assertNull($sub->next_cycle_plan);

        $updateParams1 = ['next_cycle_plan' => $nextPlanID];
        $mockUpdatedSub1 = $this->mockSubscriptionData($subID, $customerID, $planID, ['next_cycle_plan' => $mockNextPlan]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}", $updateParams1, $mockUpdatedSub1);
        $sub->next_cycle_plan = $nextPlanID;
        $sub->save();
        $this->assertInstanceOf('Payjp\Plan', $sub->next_cycle_plan);
        $this->assertSame($nextPlanID, $sub->next_cycle_plan->id);

        $updateParams2 = ['next_cycle_plan' => null];
        $mockUpdatedSub2 = $this->mockSubscriptionData($subID, $customerID, $planID, ['next_cycle_plan' => null]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}", $updateParams2, $mockUpdatedSub2);
        $sub->next_cycle_plan = null;
        $sub->save();
        $this->assertNull($sub->next_cycle_plan);

        $this->mockRequest('GET', "/v1/subscriptions/{$subID}", [], $mockUpdatedSub2);
        $sub_retrieve = Subscription::retrieve($sub->id);
        $this->assertSame($subID, $sub_retrieve->id);
        $this->assertSame('active', $sub_retrieve->status);

        $mockPausedSub = $this->mockSubscriptionData($subID, $customerID, $planID, ['status' => 'paused', 'paused_at' => time()]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}/pause", [], $mockPausedSub);
        $sub->pause();
        $this->assertSame('paused', $sub->status);
        $this->assertNotNull($sub->paused_at);

        $this->mockRequest('GET', "/v1/subscriptions/{$subID}", [], $mockPausedSub);
        $sub_pause_retrieved = Subscription::retrieve($sub->id);
        $this->assertSame('paused', $sub_pause_retrieved->status);

        $mockResumedSub1 = $this->mockSubscriptionData($subID, $customerID, $planID, ['status' => 'active', 'paused_at' => null, 'resumed_at' => time()]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}/resume", [], $mockResumedSub1);
        $sub->resume();
        $this->assertSame('active', $sub->status);
        $this->assertNull($sub->paused_at);
        $this->assertNotNull($sub->resumed_at);

        $mockPausedSub2 = $this->mockSubscriptionData($subID, $customerID, $planID, ['status' => 'paused', 'paused_at' => time() + 100, 'resumed_at' => $sub->resumed_at]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}/pause", [], $mockPausedSub2);
        $sub->pause();
        $this->assertSame('paused', $sub->status);

        $trial_end_ts = time() + 5000;
        $resumeParams = ['trial_end' => $trial_end_ts];
        $mockResumedTrialSub = $this->mockSubscriptionData($subID, $customerID, $planID, [
            'status' => 'trial',
            'paused_at' => null,
            'resumed_at' => time(),
            'trial_end' => $trial_end_ts,
            'trial_start' => time()
        ]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}/resume", $resumeParams, $mockResumedTrialSub);
        $sub->resume($resumeParams);
        $this->assertSame('trial', $sub->status);
        $this->assertSame($trial_end_ts, $sub->trial_end);
        $this->assertNotNull($sub->trial_start);

        $mockCanceledSub = $this->mockSubscriptionData($subID, $customerID, $planID, ['status' => 'canceled', 'canceled_at' => time(), 'paused_at' => null, 'trial_end' => $sub->trial_end]);
        $this->mockRequest('POST', "/v1/subscriptions/{$subID}/cancel", [], $mockCanceledSub);
        $sub->cancel();
        $this->assertSame('canceled', $sub->status);
        $this->assertNotNull($sub->canceled_at);

        $this->mockRequest('GET', "/v1/subscriptions/{$subID}", [], $mockCanceledSub);
        $sub_cancel_retrieved = Subscription::retrieve($sub->id);
        $this->assertSame('canceled', $sub_cancel_retrieved->status);

        $mockDeletedResponse = [
            'deleted' => true,
            'id' => $subID,
            'livemode' => false
        ];
        $this->mockRequest('DELETE', "/v1/subscriptions/{$subID}", [], $mockDeletedResponse);
        $sub->delete();
        $this->assertTrue($sub->deleted);
        $this->assertSame($subID, $sub->id);
    }


    public function testAll()
    {
        $planID1 = 'pln_all_1';
        $planID2 = 'pln_all_2';
        $customerID = 'cus_for_all_subs';
        $subID1 = 'sub_all_1';
        $subID2 = 'sub_all_2';

        $mockSub1 = $this->mockSubscriptionData($subID1, $customerID, $planID1);
        $mockSub2 = $this->mockSubscriptionData($subID2, $customerID, $planID2, ['status' => 'trial', 'trial_end' => time() + 86400]);

        $mockListResponse = [
            'object' => 'list',
            'data' => [$mockSub2, $mockSub1],
            'has_more' => false,
            'url' => '/v1/subscriptions'
        ];

        $listParams = ['limit' => 2, 'offset' => 0, 'customer' => $customerID];

        $this->mockRequest('GET', '/v1/subscriptions', $listParams, $mockListResponse);

        $subs = Subscription::all($listParams);

        $this->assertInstanceOf('Payjp\Collection', $subs);
        $this->assertCount(2, $subs->data);
        $this->assertInstanceOf('Payjp\Subscription', $subs->data[0]);
        $this->assertSame($subID2, $subs->data[0]->id);
        $this->assertSame('trial', $subs->data[0]->status);
        $this->assertInstanceOf('Payjp\Subscription', $subs->data[1]);
        $this->assertSame($subID1, $subs->data[1]->id);
        $this->assertSame('active', $subs->data[1]->status);
    }
}
