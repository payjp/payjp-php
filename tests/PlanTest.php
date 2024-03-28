<?php

namespace Payjp;

class PlanTest extends TestCase
{
    public function testCreateRetrieveAll()
    {
        self::authorizeFromEnv();
        $planId = 'gold-' . self::randomString();
        $p = Plan::create([
            'amount'   => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan',
            'id'       => $planId
        ]);

        $plan_retrieve = Plan::retrieve($planId);
        $this->assertSame($planId, $plan_retrieve->id);

        $planId_2 = 'foobar-2-' . self::randomString();
        $p_2 = Plan::create([
            'amount'   => 3000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan_2',
            'id'       => $planId_2
        ]);

        $plans = Plan::all([
            'limit' => 2,
            'offset' => 0
        ]);
        $this->assertSame(2, count($plans['data']));
    }

    public function testDeletion()
    {
        self::authorizeFromEnv();
        $planId = 'gold-' . self::randomString();
        $p = Plan::create([
            'amount' => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name' => 'Plan',
            'id' => $planId
        ]);
        $p->delete();
        $this->assertTrue($p->deleted);
        $this->assertSame($planId, $p->id);
    }

    public function testFalseyId()
    {
        try {
            Plan::retrieve('0');
        } catch (Error\InvalidRequest $e) {
            $this->assertSame(404, $e->httpStatus);
        }
    }

    public function testSave()
    {
        self::authorizeFromEnv();
        $planId = 'gold-' . self::randomString();
        $p = Plan::create([
            'amount'   => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan',
            'id'       => $planId
        ]);
        $p->name = 'A new plan name';
        $p->save();
        $this->assertSame($p->name, 'A new plan name');

        $payjpPlan = Plan::retrieve($planId);
        $this->assertSame($p->name, $payjpPlan->name);
    }
}
