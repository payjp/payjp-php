<?php

namespace Payjp;

class TermTest extends TestCase
{
    /**
     * @return array
     */
    private function termResource($id)
    {
        return [
            'created' => 1438354800,
            'id' => $id,
            'livemode' => false,
            'object' => 'term',
            'charge_count' => 158,
            'refund_count' => 25,
            'dispute_count' => 2,
            'end_at' => 1439650800,
            'start_at' => 1438354800,
        ];
    }

    /**
     * @return array
     */
    private function termsResource($ids = [])
    {
        return [
            'count' => count($ids),
            'data' => array_map(function ($id) {
                return $this->termResource($id);
            }, $ids),
            'has_more' => false,
            'object' => 'list',
            'url' => '/v1/terms',
        ];
    }

    public function testRetrieve()
    {
        $expectedTermId = 'tm_sample1';
        $expectedTermResource = $this->termResource($expectedTermId);
        $this->mockRequest('GET', "/v1/terms/$expectedTermId", [], $expectedTermResource);
        $term = Term::retrieve($expectedTermId);
        $this->assertSame($expectedTermId, $term->id);
        $this->assertSame($expectedTermResource['created'], $term->created);
        $this->assertSame($expectedTermResource['livemode'], $term->livemode);
        $this->assertSame($expectedTermResource['object'], $term->object);
        $this->assertSame($expectedTermResource['charge_count'], $term->charge_count);
        $this->assertSame($expectedTermResource['refund_count'], $term->refund_count);
        $this->assertSame($expectedTermResource['dispute_count'], $term->dispute_count);
        $this->assertSame($expectedTermResource['end_at'], $term->end_at);
        $this->assertSame($expectedTermResource['start_at'], $term->start_at);
    }

    public function testAll()
    {
        $expectedTermIds = ['tm_sample1', 'tm_sample2'];
        $this->mockRequest('GET', '/v1/terms', [], $this->termsResource($expectedTermIds));
        $terms = Term::all();
        $this->assertSame(count($expectedTermIds), $terms['count']);
        $this->assertCount(count($expectedTermIds), $terms['data']);
        foreach ($terms['data'] as $index => $term) {
            $this->assertInstanceOf(Term::class, $term);
            $this->assertSame($expectedTermIds[$index], $term->id);
        }
    }
}
