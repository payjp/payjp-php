<?php

namespace Payjp;

class TokenTest extends TestCase
{
    public function testUrls()
    {
        $this->assertSame(Token::classUrl(), '/v1/tokens');
        $token = new Token('abcd/efgh');
        $this->assertSame($token->instanceUrl(), '/v1/tokens/abcd%2Fefgh');
    }
    
    public function testCreate()
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
        
        $token = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);
    }
    
    public function testRetrieve()
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
        
        $token = Token::create($params, $options = ['payjp_direct_token_generate' => 'true']);
         
        $token_retrieve = Token::retrieve($token->id);
        
        $this->assertSame($token->id, $token_retrieve->id);
    }
}
