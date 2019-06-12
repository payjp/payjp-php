<?php

namespace Payjp;

class SubscriptionTest extends TestCase
{

    public function testCreateRetrieveUpdatePauseResumeCancelDelete()
    {
        $planID = 'gold-' . self::randomString();
        self::retrieveOrCreatePlan($planID);
        $nextPlanID = 'next-plan-for-sdk-test';
        self::retrieveOrCreatePlan($nextPlanID);

        $customer = self::createTestCustomer();

        $sub = Subscription::create(
            array(
                'customer' => $customer->id,
                'plan' => $planID
            )
        );

        $this->assertSame($sub->status, 'active');
        $this->assertSame($sub->next_cycle_plan, null);
        $this->assertSame($sub->trial_end, null);
        $this->assertSame($sub->plan->id, $planID);

        $sub->next_cycle_plan = $nextPlanID;
        $sub->save();
        $this->assertSame($sub->next_cycle_plan->id, $nextPlanID);
        $sub->next_cycle_plan = null;
        $sub->save();
        $this->assertSame($sub->next_cycle_plan, null);

        $sub_retrieve = Subscription::retrieve($sub->id);

        $sub->pause();

        $sub_pause = Subscription::retrieve($sub->id);
        $this->assertSame($sub_pause->status, 'paused');

        $sub->resume();
        $sub_resume = Subscription::retrieve($sub->id);
        $this->assertSame($sub_resume->status, 'active');

        $resuumed_at = $sub->created + 10000;

        $sub->pause();

        $sub_pause = Subscription::retrieve($sub->id);
        $this->assertSame($sub_pause->status, 'paused');
        $this->assertSame(null, $sub->resumed_at);

        $trial_end = $sub->created + 5000;

        $sub->resume(
            array(
                'trial_end' => $trial_end
            )
        );

        $sub_resume = Subscription::retrieve($sub->id);
        $this->assertSame($sub_resume->status, 'trial');
        $this->assertSame(500, $sub->plan->amount);
        $this->assertSame($trial_end, $sub->trial_end);

        try {
            $sub->cancel(
                array(
                    'foo' => "bar"
                )
            );
        } catch (Error\InvalidRequest $e) {
            $actual = $e->getJsonBody();

            $this->assertSame('Invalid param key to subscription', $actual['error']['message']);
        }

        $sub->cancel();

        $sub_cancel = Subscription::retrieve($sub->id);
        $this->assertSame($sub_cancel->status, 'canceled');

        $sub_id = $sub->id;
        $sub->delete();
        $this->assertSame($sub_id, $sub->id);
        $this->assertTrue($sub->deleted);
    }

    public function testAll()
    {
        $planID = 'gold-' . self::randomString();
        self::retrieveOrCreatePlan($planID);

        $customer = self::createTestCustomer();

        $sub = Subscription::create(
            array(
                'customer' => $customer->id,
                'plan' => $planID
            )
        );

        $planID_2 = 'gold-2-' . self::randomString();
        self::retrieveOrCreatePlan($planID_2);

        $sub_2 = Subscription::create(
            array(
                'customer' => $customer->id,
                'plan' => $planID_2
            )
        );

        $subs = Subscription::all(
            array(
                'limit' => 2,
                'offset' => 0
            )
        );

        $this->assertSame(2, count($subs['data']));
    }
}
