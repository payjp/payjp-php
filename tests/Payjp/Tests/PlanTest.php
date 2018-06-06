<?php

namespace Payjp\Tests;

use Phake;

/**
 * Class PlanTest
 * @package Payjp\Tests
 */
class PlanTest extends \Payjp\Tests\PayjpTestCase {

    public function setUp()
    {
        $this->payjp = Phake::partialMock('Payjp\Client', 'sk_xxx');
        Phake::when($this->payjp)->request(Phake::anyParameters())->thenReturn([]);
    }

    public function testPlanRetrieve()
    {
        $res = $this->payjp->plans->retrieve('pln_xxx');
        Phake::verify($this->payjp)->request('GET', 'plans/pln_xxx');
    }

    public function testPlanAll()
    {
        $res = $this->payjp->plans->all();
        Phake::verify($this->payjp)->request('GET', 'plans', []);
    }

    public function testPlanCreate()
    {
        $params = [
            'name' => 'plan A',
            'amount' => 500,
        ];
        $res = $this->payjp->plans->create($params);
        Phake::verify($this->payjp)->request('POST', 'plans', $params);
    }

    public function testPlanUpdate()
    {
        $params = [
            'name' => 'plan A',
        ];
        $res = $this->payjp->plans->update('pln_xxx', $params);
        Phake::verify($this->payjp)->request('POST', 'plans/pln_xxx', $params);
    }

    public function testPlanDelete()
    {
        $res = $this->payjp->plans->delete('pln_xxx');
        Phake::verify($this->payjp)->request('DELETE', 'plans/pln_xxx');
    }
}
