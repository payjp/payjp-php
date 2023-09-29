<?php

namespace Payjp;

class TenantTransfer extends ApiResource
{
    public $statementUrls;

    public function __construct($id = null, $opts = null)
    {
        parent::__construct($id, $opts);
        $this->statementUrls = new StatementUrl(null, $opts);
        $this->statementUrls->_url = $this->instanceUrl() . '/statement_urls';
    }

    // override
    public static function className()
    {
        return 'tenant_transfer';
    }
    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $options
     *
     * @return TenantTransfer[]
     */
    public static function all($params = null, $options = null)
    {
        return self::_all($params, $options);
    }
    /**
     * @param string $id The ID of the tenant_transfer to retrieve.
     * @param RequestOptions|array|string|null $options
     *
     * @return TenantTransfer
     */
    public static function retrieve($id, $options = null)
    {
        return self::_retrieve($id, $options);
    }
}
