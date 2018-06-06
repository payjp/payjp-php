<?php

namespace Payjp\Tests;

use Phake;

class EventTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testEventAll()
    {
        $res = $this->payjp->events->all();
        Phake::verify($this->payjp)->request('GET', 'events', []);
    }

    public function testEventRetrieve()
    {
        $res = $this->payjp->events->retrieve('evn_xxx');
        Phake::verify($this->payjp)->request('GET', 'events/evn_xxx');
    }
}
