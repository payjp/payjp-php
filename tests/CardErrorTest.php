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
        $mock = $this->setUpMockRequest();
        $mock->expects($this->once())
            ->method('request')
            ->willThrowException(new Error\Card('Expired card', null, 'expired_card', 402, null, null));
        try {
            Token::create($params, ['payjp_direct_token_generate' => 'true']);
        } catch (Error\Card $e) {
            $this->assertSame(402, $e->getHttpStatus());
        }
    }
}
