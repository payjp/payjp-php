<?php

namespace Payjp;

class Tenant extends ApiResource
{
    // todo should have `application_urls` property(include `id`) in response of PF account.
    public $application_urls;
    public function __construct($id = null, $opts = null)
    {
        parent::__construct($id, $opts);
        $this->application_urls = new ApplicationUrl(null, $opts);
        $this->application_urls->_url = $this->instanceUrl() . '/application_urls';
    }

    /**
     * @param string $id The ID of the tenant to retrieve.
     * @param RequestOptions|array|string|null $opts
     *
     * @return Tenant
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $opts
     *
     * @return Tenant[]
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }

    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $opts
     *
     * @return Tenant
     */
    public static function create($params = null, $opts = null)
    {
        return self::_create($params, $opts);
    }

    /**
     * @param RequestOptions|array|string|null $opts
     *
     * @return Tenant
     */
    public function save($opts = null)
    {
        return $this->_save($opts);
    }

    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $opts
     *
     * @return Tenant
     */
    public function delete($params = null, $opts = null)
    {
        return $this->_delete($params, $opts);
    }
}
