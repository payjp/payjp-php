<?php

namespace Payjp;

use Payjp\HttpClient\CurlClient;

class CurlClientTest extends TestCase
{
    public function testEncode()
    {
        $a = [
            'my' => 'value',
            'that' => ['your' => 'example'],
            'bar' => 1,
            'baz' => null
        ];

        $enc = CurlClient::encode($a);
        $this->assertSame('my=value&that%5Byour%5D=example&bar=1', $enc);

        $a = ['that' => ['your' => 'example', 'foo' => null]];
        $enc = CurlClient::encode($a);
        $this->assertSame('that%5Byour%5D=example', $enc);

        $a = ['that' => 'example', 'foo' => ['bar', 'baz']];
        $enc = CurlClient::encode($a);
        $this->assertSame('that=example&foo%5B%5D=bar&foo%5B%5D=baz', $enc);

        $a = [
            'my' => 'value',
            'that' => ['your' => ['cheese', 'whiz', null]],
            'bar' => 1,
            'baz' => null
        ];

        $enc = CurlClient::encode($a);
        $expected = 'my=value&that%5Byour%5D%5B%5D=cheese'
              . '&that%5Byour%5D%5B%5D=whiz&bar=1';
        $this->assertSame($expected, $enc);

        // Ignores an empty array
        $enc = CurlClient::encode(['foo' => [], 'bar' => 'baz']);
        $expected = 'bar=baz';
        $this->assertSame($expected, $enc);
    }
}
