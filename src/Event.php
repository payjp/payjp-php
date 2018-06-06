<?php

namespace Payjp;

/**
 * Class Event
 * @package Payjp
 */
class Event
{
    /**
     * @var string $resource
     */
    private $resource = 'events';

    /**
     * @var Client $client
     */
    private $client;

    /**
     * Event constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $query
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function all(array $query = [])
    {
        return $this->client->request('GET', $this->resource, $query);
    }

    /**
     * @param $id
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve($id)
    {
        return $this->client->request('GET', sprintf('%s/%s', $this->resource, $id));
    }
}
