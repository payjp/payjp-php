<?php

namespace Payjp;

/**
 * Class Token
 * @package Payjp
 */
class Token
{
    /**
     * @var string $resource
     */
    private $resource = 'tokens';

    /**
     * @var Client
     */
    private $client;

    /**
     * Token constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
    public function create(array $query = [])
    {
        return $this->client->request('POST', $this->resource, $query);
    }
}
