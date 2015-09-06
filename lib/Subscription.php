<?php

namespace Payjp;

class Subscription extends ApiResource
{
    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Subscription The created subscription.
     */
    public static function create($params = null, $options = null)
    {
        return self::_create($params, $options);
    }
    
    /**
     * @param string $id The ID of the subscription to retrieve.
     * @param array|string|null $options
     *
     * @return subscription
     */
    public static function retrieve($id, $options = null)
    {
        return self::_retrieve($id, $options);
    }
    
    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return array An array of Subscriptions.
     */
    public static function all($params = null, $options = null)
    {
        return self::_all($params, $options);
    }
    
    /**
     * @param array|null $params
     *
     * @return Subscription The deleted subscription.
     */
    
    public function delete($params = null, $opts = null)
    {
        return $this->_delete($params, $opts);
    }

    /**
     * @param array|string|null $opts
     *
     * @return Subscription The saved subscription.
     */
    public function save($opts = null)
    {
        return $this->_save($opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Subscription The Paused subscription.
     */
    public function pause($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/pause';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }
    
    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Subscription The Resumed subscription.
     */
    public function resume($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/resume';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }
    
    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Subscription The Canceled subscription.
     */
    public function cancel($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/cancel';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }
}
