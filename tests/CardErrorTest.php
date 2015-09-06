<?php

namespace Payjp;

class CardErrorTest extends TestCase
{
    public function testExpiredCard()
    {
        self::authorizeFromEnv();

        $card = array(
            'number' => '4242424242424242',
            'exp_month' => '3',
            'exp_year' => '2010'
        );

        $charge = array(
            'amount' => 100,
            'currency' => 'usd',
            'card' => $card
        );

        try {
            Charge::create($charge);
        } catch (Error\Card $e) {
            $this->assertSame(402, $e->getHttpStatus());
        }
    }
}
