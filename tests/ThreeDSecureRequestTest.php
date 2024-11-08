<?php

namespace Payjp;

class ThreeDSecureRequestTest extends TestCase
{

    private function managedThreeDSecureRequestResource($id)
    {
        return [
            'created' => 1730084767,
            'expired_at' => null,
            'finished_at' => null,
            'id' => $id,
            'livemode' => true,
            'object' => 'three_d_secure_request',
            'resource_id' => 'car_xxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'result_received_at' => null,
            'started_at' => null,
            'state' => 'created',
            'tenant_id' => null,
            'three_d_secure_status' => 'unverified'
        ];
    }

    private function managedThreeDSecureRequestResources($ids)
    {
        return [
            'count' => count($ids),
            'data' => array_map(function ($id) {
                return $this->managedThreeDSecureRequestResource($id);
            }, $ids),
            'has_more' => false,
            'object' => 'list',
            'url' => '/v1/three_d_secure_requests'
        ];
    }

    public function testRetrieve()
    {
        $expectedThreeDSecureRequestId = 'tdsr_125192559c91c4011c1ff56f50a';
        $expectedThreeDSecureRequestResource = $this->managedThreeDSecureRequestResource($expectedThreeDSecureRequestId);
        $this->mockRequest('GET', '/v1/three_d_secure_requests/' . $expectedThreeDSecureRequestId, [], $expectedThreeDSecureRequestResource);
        $threeDSecureRequest = ThreeDSecureRequest::retrieve($expectedThreeDSecureRequestId);
        $this->assertSame($expectedThreeDSecureRequestResource['created'], $threeDSecureRequest->created);
        $this->assertSame($expectedThreeDSecureRequestResource['expired_at'], $threeDSecureRequest->expired_at);
        $this->assertSame($expectedThreeDSecureRequestResource['finished_at'], $threeDSecureRequest->finished_at);
        $this->assertSame($expectedThreeDSecureRequestResource['id'], $threeDSecureRequest->id);
        $this->assertSame($expectedThreeDSecureRequestResource['livemode'], $threeDSecureRequest->livemode);
        $this->assertSame($expectedThreeDSecureRequestResource['object'], $threeDSecureRequest->object);
        $this->assertSame($expectedThreeDSecureRequestResource['resource_id'], $threeDSecureRequest->resource_id);
        $this->assertSame($expectedThreeDSecureRequestResource['result_received_at'], $threeDSecureRequest->result_received_at);
        $this->assertSame($expectedThreeDSecureRequestResource['started_at'], $threeDSecureRequest->started_at);
        $this->assertSame($expectedThreeDSecureRequestResource['state'], $threeDSecureRequest->state);
        $this->assertSame($expectedThreeDSecureRequestResource['tenant_id'], $threeDSecureRequest->tenant_id);
        $this->assertSame($expectedThreeDSecureRequestResource['three_d_secure_status'], $threeDSecureRequest->three_d_secure_status);
    }

    public function testAll()
    {
        $expectedThreeDSecureRequestIds = array('tdsr_125192559c91c4011c1ff56f50a', 'tdsr_125192559c91c4011c1ff56f50b');
        $this->mockRequest('GET', '/v1/three_d_secure_requests', array(), $this->managedThreeDSecureRequestResources($expectedThreeDSecureRequestIds));
        $threeDSecureRequests = ThreeDSecureRequest::all();
        $this->assertSame(count($expectedThreeDSecureRequestIds), $threeDSecureRequests['count']);
        $this->assertCount(count($expectedThreeDSecureRequestIds), $threeDSecureRequests['data']);
        $this->assertSame($expectedThreeDSecureRequestIds[0], $threeDSecureRequests['data'][0]->id);
        $this->assertSame($expectedThreeDSecureRequestIds[1], $threeDSecureRequests['data'][1]->id);
    }

    public function testCreate()
    {
        $expectedThreeDSecureRequestId = 'tdsr_125192559c91c4011c1ff56f50a';
        $expectedCustomerCardId = 'car_xxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $params = [
            'resource_id' => $expectedCustomerCardId
        ];
        $this->mockRequest('POST', '/v1/three_d_secure_requests', $params, $this->managedThreeDSecureRequestResource($expectedThreeDSecureRequestId));
        $threeDSecureRequest = ThreeDSecureRequest::create($params);
        $this->assertSame($expectedThreeDSecureRequestId, $threeDSecureRequest->id);
        $this->assertSame($expectedCustomerCardId, $threeDSecureRequest->resource_id);
    }
}
