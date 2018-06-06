<?php

namespace Payjp;

/**
 * Class Customer
 * @package Payjp
 */
class Customer
{
    /**
     * @var string $resource
     */
    private $resource = 'customers';

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var Card
     */
    public $cards;

    /**
     * @var CustomerSubscription
     */
    public $subscriptions;

    /**
     * Charge constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->cards = new Card($client);
        $this->subscriptions = new CustomerSubscription($client);
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
    public function all(array $query = []) {
        return $this->client->request('GET', $this->resource, $query);
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
    public function create(array $query = []) {
        return $this->client->request('POST', $this->resource, $query);
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
    public function retrieve($id) {
        return $this->client->request('GET', sprintf('%s/%s', $this->resource, $id));
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
    public function update($id, array $query = []) {
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
    public function delete($id)
    {
        return $this->client->request('DELETE', sprintf('%s/%s', $this->resource, $id));
    }

}

/**
 * Class CustomerSubscription
 * @package Payjp
 */
class CustomerSubscription
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
     * CustomerSubscription constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $customer_id
     * @param array $query
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function all($customer_id, array $query = []) {
        return $this->client->request('GET', sprintf('customers/%s/%s', $customer_id, $this->resource), $query);
    }

    /**
     * @param $customer_id
     * @param $id
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ApiConnectionException
     * @throws Exception\ApiException
     * @throws Exception\AuthenticationException
     * @throws Exception\CardException
     * @throws Exception\InvalidRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve($customer_id, $id) {
        return $this->client->request('GET', sprintf('customers/%s/%s/%s', $customer_id, $this->resource, $id));
    }
}