<?php

namespace Payjp;

class EventTest extends TestCase
{
    public function testAllRetrieve()
    {
        self::authorizeFromEnv();
        $this->setUpMockRequest();

        $mockEvent1Data = [
            'id' => 'evnt_test_1',
            'object' => 'event',
            'type' => 'charge.succeeded',
            'data' => ['object' => 'charge', 'id' => 'ch_test_charge'],
            'created' => time(),
            'livemode' => false,
            'pending_webhooks' => 0,
        ];
        $mockEvent2Data = [
            'id' => 'evnt_test_2',
            'object' => 'event',
            'type' => 'customer.created',
            'data' => ['object' => 'customer', 'id' => 'cus_test_customer'],
            'created' => time() - 100,
            'livemode' => false,
            'pending_webhooks' => 0,
        ];
        $mockEventsResponse = [
            'object' => 'list',
            'data' => [ $mockEvent1Data, $mockEvent2Data ],
            'has_more' => false,
            'url' => '/v1/events'
        ];
        $mockEventRetrieveData = $mockEvent1Data;

        $this->mock->expects($this->exactly(2))
            ->method('request')
            ->withConsecutive(
                [
                    'get',
                    'https://api.pay.jp/v1/events',
                    $this->anything(),
                    ['limit' => 3, 'offset' => 0],
                    false
                ],
                [
                    'get',
                    'https://api.pay.jp/v1/events/evnt_test_1',
                    $this->anything(),
                    [],
                    false
                ]
            )
            ->willReturnOnConsecutiveCalls(
                [json_encode($mockEventsResponse), 200],
                [json_encode($mockEventRetrieveData), 200]
            );

        $events = Event::all([
            'limit' => 3,
            'offset' => 0
        ]);

        $this->assertInstanceOf('Payjp\Collection', $events);
        $this->assertTrue(is_array($events->data));
        $this->assertCount(2, $events->data);
        $this->assertInstanceOf('Payjp\Event', $events->data[0]);
        $this->assertSame($mockEvent1Data['id'], $events->data[0]->id);

        $event = Event::retrieve($events->data[0]->id);
        $this->assertInstanceOf('Payjp\Event', $event);
        $this->assertSame($mockEvent1Data['id'], $event->id);
        $this->assertSame($mockEvent1Data['type'], $event->type);
    }
}
