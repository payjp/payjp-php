<?php

namespace Payjp;

class UtilTest extends TestCase
{
    public function testIsList()
    {
        $list = [5, 'nstaoush', []];
        $this->assertTrue(Util\Util::isList($list));

        $notlist = [5, 'nstaoush', [], 'bar' => 'baz'];
        $this->assertFalse(Util\Util::isList($notlist));
    }

    public function testThatPHPHasValueSemanticsForArrays()
    {
        $original = ['php-arrays' => 'value-semantics'];
        $derived = $original;
        $derived['php-arrays'] = 'reference-semantics';

        $this->assertSame('value-semantics', $original['php-arrays']);
    }

    public function testConvertPayjpObjectToArrayIncludesId()
    {
        $mock = $this->setUpMockRequest();
        $mock->expects($this->any())
            ->method('request')
            ->willReturn([json_encode(['id' => 'cus_mocked']), 200]);
        $customer = self::createTestCustomer();
        $this->assertTrue(array_key_exists("id", $customer->__toArray(true)));
    }

    public function testUtf8()
    {
        // UTF-8 string
        $x = "\xc3\xa9";
        $this->assertSame(Util\Util::utf8($x), $x);

        // Latin-1 string
        $x = "\xe9";
        $this->assertSame(Util\Util::utf8($x), "\xc3\xa9");

        // Not a string
        $x = true;
        $this->assertSame(Util\Util::utf8($x), $x);
    }
}
