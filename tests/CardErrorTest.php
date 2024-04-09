<?php

namespace Payjp;

class CardErrorTest extends TestCase
{
    public function testExpiredCard()
    {
        self::authorizeFromEnv();

        $params =  [
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 6,
                'exp_year' => date('Y') - 1,
                'cvc' => '314'
            ]
        ];

        try {
            Token::create($params, ['payjp_direct_token_generate' => 'true']);
        } catch (Error\Card $e) {
            $this->assertSame(402, $e->getHttpStatus());
        }
    }
}
