<?php

namespace Payjp;

/**
 * Class Card
 * @package Payjp
 */
class Card
{
    /**
     * @var string $resource
     */
    private $resource = 'cards';

    /**
     * @var Client $client
     */
    private $client;

    /**
     * Charge constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->payjp = $client;
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
        return $this->payjp->request('GET', sprintf('customers/%s/%s', $customer_id, $this->resource), $query);
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
    public function create($customer_id, array $query = []) {
        return $this->payjp->request('POST', sprintf('customers/%s/%s', $customer_id, $this->resource), $query);
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
        return $this->payjp->request('GET', sprintf('customers/%s/%s/%s', $customer_id, $this->resource, $id));
    }

    /**
     * @param $customer_id
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
    public function update($customer_id, $id, array $query = []) {
        return $this->payjp->request('POST', sprintf('customers/%s/%s/%s', $customer_id, $this->resource, $id), $query);
    }

    /**
     * @param $customer_id
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
    public function delete($customer_id, $id, array $query = []) {
        return $this->payjp->request('DELETE', sprintf('customers/%s/%s/%s', $customer_id, $this->resource, $id), $query);
    }

}
