<?php

namespace Payjp;

class Balance extends ApiResource
{
    /**
     * @param string $id The ID of the balance to retrieve.
     * @param array|string|null $opts
     *
     * @return Balance
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return array An array of Balances.
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }
}
