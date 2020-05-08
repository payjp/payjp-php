<?php

namespace Payjp;

class Transfer extends ApiResource
{
    /**
     * @param string $id The ID of the transfer to retrieve.
     * @param RequestOptions|array|string|null $opts
     *
     * @return Transfer
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param RequestOptions|array|string|null $opts
     *
     * @return Transfer[]
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }
}
