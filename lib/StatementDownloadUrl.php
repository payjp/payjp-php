<?php

namespace Payjp;

class StatementDownloadUrl extends ApiResource
{
    public $_url = '';

    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $options
     *
     * @return StatementDownloadUrl
     */
    public function create($params = null, $options = null)
    {
        list($response, $opts) = $this->_request('post', $this->_url, $params, $options);
        $statementDownloadUrl = Util\Util::convertToPayjpObject($response, $opts);
        $statementDownloadUrl->_url = $this->_url;
        return $statementDownloadUrl;
    }
}
