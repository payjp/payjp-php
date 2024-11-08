<?php

namespace Payjp;

class TransferTest extends TestCase
{
    public function testAllRetrieve()
    {
        self::authorizeFromEnv();

        $transfers = Transfer::all([
            'limit' => 3,
            'offset' => 0
        ]);

        if (count($transfers['data'])) {
            $transfer = Transfer::retrieve($transfers['data'][0]->id);
            $this->assertSame($transfers['data'][0]->id, $transfer->id);
        }
    }

    public function testAllCharge()
    {
        self::authorizeFromEnv();

        $transfers = Transfer::all([
            'limit' => 3,
            'offset' => 0
        ]);

        if (count($transfers['data'])) {
            $transfer = Transfer::retrieve($transfers['data'][0]->id);
            $charges = $transfer->charges->all([
                'limit' => 3,
                'offset' => 0
            ]);
            $this->markTestSkipped('Should be changed to mock.');
            // $this->assertTrue(count($charges['data']) > 0);
        }
    }
}
