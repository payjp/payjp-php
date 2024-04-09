<?php

namespace Payjp;

class Term extends ApiResource
{
    /**
     * @param string $id The ID of the term to retrieve.
     * @param array|string|null $opts
     *
     * @return Term
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return array An array of Terms.
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }
}
