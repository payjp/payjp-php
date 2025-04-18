<?php

namespace Payjp;

class PlanTest extends TestCase
{
    private function mockPlanData($id, $attributes = [])
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

    public function testCreateRetrieveAll()
    {
        $this->setUpMockRequest();
        $planId1 = 'plan_test_1';
        $planId2 = 'plan_test_2';

        $createParams1 = [
            'amount'   => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan 1',
            'id'       => $planId1
        ];
        $createParams2 = [
            'amount'   => 3000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Plan 2',
            'id'       => $planId2
        ];

        $mockPlan1Data = $this->mockPlanData($planId1, $createParams1);
        $mockPlan2Data = $this->mockPlanData($planId2, $createParams2);

        $mockPlanListResponse = [
            'object' => 'list',
            'data' => [$mockPlan2Data, $mockPlan1Data],
            'has_more' => false,
            'url' => '/v1/plans'
        ];

        // Mock the requests
        $this->mockRequest('POST', '/v1/plans', $createParams1, $mockPlan1Data);
        $plan1 = Plan::create($createParams1);
        $this->assertInstanceOf('Payjp\Plan', $plan1);
        $this->assertSame($planId1, $plan1->id);
        $this->assertSame($createParams1['amount'], $plan1->amount);

        $this->mockRequest('GET', '/v1/plans/' . $planId1, [], $mockPlan1Data);
        $plan_retrieve = Plan::retrieve($planId1);
        $this->assertInstanceOf('Payjp\Plan', $plan_retrieve);
        $this->assertSame($planId1, $plan_retrieve->id);

        $this->mockRequest('POST', '/v1/plans', $createParams2, $mockPlan2Data);
        $plan2 = Plan::create($createParams2);
        $this->assertInstanceOf('Payjp\Plan', $plan2);
        $this->assertSame($planId2, $plan2->id);

        $this->mockRequest('GET', '/v1/plans', ['limit' => 2, 'offset' => 0], $mockPlanListResponse);
        $plans = Plan::all(['limit' => 2, 'offset' => 0]);
        $this->assertInstanceOf('Payjp\Collection', $plans);
        $this->assertTrue(is_array($plans->data));
        $this->assertCount(2, $plans->data);
        $this->assertInstanceOf('Payjp\Plan', $plans->data[0]);
        $this->assertSame($planId2, $plans->data[0]->id);
        $this->assertInstanceOf('Payjp\Plan', $plans->data[1]);
        $this->assertSame($planId1, $plans->data[1]->id);
    }

    public function testDeletion()
    {
        $this->setUpMockRequest();
        $planId = 'plan_to_delete';
        $createParams = [
            'amount' => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name' => 'PlanToDelete',
            'id' => $planId
        ];
        $mockPlanData = $this->mockPlanData($planId, $createParams);
        $mockDeletedPlanData = array_merge($mockPlanData, ['deleted' => true]);

        $this->mockRequest('POST', '/v1/plans', $createParams, $mockPlanData);
        $p = Plan::create($createParams);
        $this->assertInstanceOf('Payjp\Plan', $p);
        $this->assertSame($planId, $p->id);

        $this->mockRequest('DELETE', '/v1/plans/' . $planId, [], $mockDeletedPlanData);
        $deletedPlan = $p->delete();

        $this->assertInstanceOf('Payjp\Plan', $deletedPlan);
        $this->assertTrue($deletedPlan->deleted);
        $this->assertSame($planId, $deletedPlan->id);
    }

    public function testFalseyId()
    {
        $this->setUpMockRequest();
        $planId = '0';
        $mockErrorResponse = [
            'error' => [
                'message' => 'No such plan: ' . $planId,
                'param' => 'id',
                'status' => 404,
                'type' => 'invalid_request_error'
            ]
        ];

        $this->mock->expects($this->once())
            ->method('request')
            ->with(
                'get',
                'https://api.pay.jp/v1/plans/' . $planId,
                $this->anything(),
                [],
                false
            )
            ->willReturn([json_encode($mockErrorResponse), 404]);

        try {
            Plan::retrieve($planId);
            $this->fail("Expected Payjp\Error\InvalidRequest exception was not thrown.");
        } catch (Error\InvalidRequest $e) {
            $this->assertSame(404, $e->getHttpStatus());
            $this->assertNotFalse(strpos($e->getMessage(), 'No such plan'));
        }
    }

    public function testSave()
    {
        $this->setUpMockRequest();
        $planId = 'plan_to_save';
        $createParams = [
            'amount'   => 2000,
            'interval' => 'month',
            'currency' => self::CURRENCY,
            'name'     => 'Original Name',
            'id'       => $planId
        ];
        $mockPlanData = $this->mockPlanData($planId, $createParams);
        $updatedName = 'A new plan name';
        $saveParams = ['name' => $updatedName];
        $mockUpdatedPlanData = array_merge($mockPlanData, $saveParams);

        $this->mockRequest('POST', '/v1/plans', $createParams, $mockPlanData);
        $p = Plan::create($createParams);
        $this->assertInstanceOf('Payjp\Plan', $p);
        $this->assertSame($createParams['name'], $p->name);

        $this->mockRequest('POST', '/v1/plans/' . $planId, $saveParams, $mockUpdatedPlanData);
        $p->name = $updatedName;
        $savedPlan = $p->save();

        $this->assertInstanceOf('Payjp\Plan', $savedPlan);
        $this->assertSame($updatedName, $savedPlan->name);
        $this->assertSame($updatedName, $p->name);

        $this->mockRequest('GET', '/v1/plans/' . $planId, [], $mockUpdatedPlanData);
        $retrievedPlan = Plan::retrieve($planId);
        $this->assertInstanceOf('Payjp\Plan', $retrievedPlan);
        $this->assertSame($updatedName, $retrievedPlan->name);
    }
}
