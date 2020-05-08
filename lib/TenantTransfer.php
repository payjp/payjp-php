<?php

namespace Payjp;

class TenantTransfer extends ApiResource
{
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
