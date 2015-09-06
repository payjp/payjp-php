<?php

namespace Payjp;

class PlanTest extends TestCase
{
    public function testCreateRetrieveAll()
    {
        self::authorizeFromEnv();
        $planID = 'gold-' . self::randomString();
        $p = Plan::create(array(
            'amount'   => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan',
            'id'       => $planID
        ));
        
        $plan_retrieve = Plan::retrieve($planID);
        $this->assertSame($planID, $plan_retrieve->id);
                
        $planID_2 = 'foobar-2-' . self::randomString();
        $p_2 = Plan::create(
            array(
                'amount'   => 3000,
                'interval' => 'month',
                'currency' => self::CURRENCY,
                'name'     => 'Plan_2',
                'id'       => $planID_2
            )
        );

        $plans = Plan::all(
            array(
                'limit' => 2,
                'offset' => 0
            )
        );
        $this->assertSame(2, count($plans['data']));
    }

    public function testDeletion()
    {
        self::authorizeFromEnv();
        $plan_ID = 'gold-' . self::randomString();
        $p = Plan::create(array(
            'amount' => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name' => 'Plan',
            'id' => $plan_ID
        ));
        $p->delete();
        $this->assertTrue($p->deleted);
        $this->assertSame($plan_ID, $p->id);
    }

    public function testFalseyId()
    {
        try {
            $retrievedPlan = Plan::retrieve('0');
        } catch (Error\InvalidRequest $e) {
            // Can either succeed or 404, all other errors are bad
            if ($e->httpStatus !== 404) {
                $this->fail();
            }
        }
    }

    public function testSave()
    {
        self::authorizeFromEnv();
        $planID = 'gold-' . self::randomString();
        $p = Plan::create(array(
            'amount'   => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan',
            'id'       => $planID
        ));
        $p->name = 'A new plan name';
        $p->save();
        $this->assertSame($p->name, 'A new plan name');

        $payjpPlan = Plan::retrieve($planID);
        $this->assertSame($p->name, $payjpPlan->name);
    }
}
