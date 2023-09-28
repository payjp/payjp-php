<?php

namespace Payjp;

class StatementUrl extends ApiResource
{
    public $_url = '';

    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $options
     *
     * @return StatementUrl
     */
    public function create($params = null, $options = null)
    {
        list($response, $opts) = $this->_request('post', $this->_url, $params, $options);
        $statementUrl = Util\Util::convertToPayjpObject($response, $opts);
        $statementUrl->_url = $this->_url;
        return $statementUrl;
    }
}
