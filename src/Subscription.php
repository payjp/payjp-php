<?php

namespace Payjp;

/**
 * Class Subscription
 * @package Payjp
 */
class Subscription
{
    /**
     * @var string $resource
     */
    private $resource = 'subscriptions';

    /**
     * @var Client $client
     */
    private $client;

    /**
     * Plan constructor.
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

    /**
     * @param $id
     * @param array $query
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update($id, array $query = [])
    {
        return $this->client->request('POST', sprintf('%s/%s', $this->resource, $id), $query);
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
    public function pause($id)
    {
        return $this->client->request('POST', sprintf('%s/%s/pause', $this->resource, $id));
    }

    /**
     * @param $id
     * @param array $query
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resume($id, array $query = [])
    {
        return $this->client->request('POST', sprintf('%s/%s/resume', $this->resource, $id), $query);
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
    public function cancel($id)
    {
        return $this->client->request('POST', sprintf('%s/%s/cancel', $this->resource, $id));
    }

    /**
     * @param $id
     * @param array $query
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($id, array $query = [])
    {
        return $this->client->request('DELETE', sprintf('%s/%s', $this->resource, $id), $query);
    }
}
