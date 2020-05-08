<?php

namespace Payjp;

class ApplicationUrl extends ApiResource
{
    public $_url = '';
    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $options
     *
     * @return ApplicationUrl
     */
    public function create($params = null, $options = null)
    {
        list($response, $opts) = $this->_request('post', $this->_url, $params, $options);
        $applicationUrl = Util\Util::convertToPayjpObject($response, $opts);
        $applicationUrl->_url = $this->_url;
        return $applicationUrl;
    }
}
