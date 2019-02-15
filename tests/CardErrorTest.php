<?php

namespace Payjp;

class CardErrorTest extends TestCase
{
    public function testExpiredCard()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
            "number" => "4242424242424242",
            "exp_month" => 6,
            "exp_year" => date('Y') + 3,
            "cvc" => "314"
            ]
        ];

        $card = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);

        $charge = array(
            'amount' => 100,
            'currency' => self::CURRENCY,
            'card' => $card->id
        );

        try {
            Charge::create($charge);
        } catch (Error\Card $e) {
            $this->assertSame(402, $e->getHttpStatus());
        }
    }
}
