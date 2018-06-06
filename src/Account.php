<?php

namespace Payjp;

/**
 * Class Account
 * @package Payjp
 */
class Account
{
    /**
     * @var string $resource
     */
    private $resource = 'accounts';

    /**
     * @var Client $client
     */
    private $client;

    /**
     * Account constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve()
    {
        return $this->client->request('GET', $this->resource);
    }
}
