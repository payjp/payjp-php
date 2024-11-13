<?php

namespace Payjp;

class Token extends ApiResource
{
    /**
     * @param string $id The ID of the token to retrieve.
     * @param array|string|null $opts
     *
     * @return Token
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Token The created token.
     */
    public static function create($params = null, $opts = null)
    {
        return self::_create($params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Token The paid token.
     */
    public function tdsFinish($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/tds_finish';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }
}
