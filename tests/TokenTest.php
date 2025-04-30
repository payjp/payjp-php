<?php

namespace Payjp;

class TokenTest extends TestCase
{
    /**
     * Generate mock token data.
     */
    private function mockTokenData($id = 'tok_test_1', $attributes = [])
    {
        $base = [
            'id' => $id,
            'object' => 'token',
            'livemode' => false,
            'used' => false,
            'card' => [
                'id' => 'car_test_card_for_token',
                'object' => 'card',
                'brand' => 'Visa',
                'last4' => '4242',
                'exp_month' => 6,
                'exp_year' => date('Y') + 3,
                'fingerprint' => 'e1d8225886e3a7211127579a1f6f7881',
                'name' => null,
                'country' => null,
                'address_state' => null,
                'address_city' => null,
                'address_zip' => null,
                'address_line1' => null,
                'address_line2' => null,
                'address_zip_check' => 'unchecked',
                'cvc_check' => 'passed',
                'created' => time(),
                'customer' => null,
            ],
            'created' => time(),
        ];
        return array_merge($base, $attributes);
    }

    public function testUrls()
    {
        $this->assertSame(Token::classUrl(), '/v1/tokens');
        $tokenSimple = new Token('tok_test_1');
        $this->assertSame($tokenSimple->instanceUrl(), '/v1/tokens/tok_test_1');
        $tokenComplex = new Token('tok_abcd/efgh');
        $this->assertSame($tokenComplex->instanceUrl(), '/v1/tokens/tok_abcd%2Fefgh');
    }

    public function testCreate()
    {
        $params =  [
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 6,
                'exp_year' => date('Y') + 3,
                'cvc' => '314'
            ]
        ];
        $mockTokenResponse = $this->mockTokenData('tok_created_token');
        $mockTokenResponse['card']['exp_month'] = $params['card']['exp_month'];
        $mockTokenResponse['card']['exp_year'] = $params['card']['exp_year'];

        $this->mockRequest('POST', '/v1/tokens', $params, $mockTokenResponse, 200, ['payjp_direct_token_generate' => 'true']);

        $opts = new \Payjp\Util\RequestOptions(null, ['payjp_direct_token_generate' => 'true']);
        $token = Token::create($params, $opts);

        $this->assertInstanceOf('Payjp\Token', $token);
        $this->assertSame('tok_created_token', $token->id);
        $this->assertFalse($token->livemode);
        $this->assertFalse($token->used);
        $this->assertInstanceOf('Payjp\Card', $token->card);
        $this->assertSame('Visa', $token->card->brand);
        $this->assertSame('4242', $token->card->last4);
        $this->assertSame($params['card']['exp_month'], $token->card->exp_month);
        $this->assertSame($params['card']['exp_year'], $token->card->exp_year);
        $this->assertSame('passed', $token->card->cvc_check);
    }

    public function testRetrieve()
    {
        $tokenId = 'tok_test_retrieved';
        $mockTokenResponse = $this->mockTokenData($tokenId);

        $this->mockRequest('GET', '/v1/tokens/' . $tokenId, [], $mockTokenResponse);

        $token_retrieve = Token::retrieve($tokenId);

        $this->assertInstanceOf('Payjp\Token', $token_retrieve);
        $this->assertSame($tokenId, $token_retrieve->id);
        $this->assertInstanceOf('Payjp\Card', $token_retrieve->card);
        $this->assertSame('4242', $token_retrieve->card->last4);
    }
}
