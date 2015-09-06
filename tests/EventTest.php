<?php

namespace Payjp;

class EventTest extends TestCase
{
    public function testAllRetrieve()
    {
        self::authorizeFromEnv();
                 
        $events = Event::all(
            array(
                'limit' => 3,
                'offset' => 0
            )
        );
        
        if (count($events['data'])) {
            $event = Event::retrieve($events['data'][0]->id);
            $this->assertSame($events['data'][0]->id, $event->id);
        }
    }
}
