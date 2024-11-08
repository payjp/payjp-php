<?php

namespace Payjp;

class ThreeDSecureRequest extends ApiResource
{
    public static function className()
    {
        return 'three_d_secure_request';
    }

    /**
     * @param string $id The ID of the three d secure request to retrieve.
     * @param array|string|null $opts
     *
     * @return ThreeDSecureRequest
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return array An array of ThreeDSecureRequests.
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return ThreeDSecureRequest The created three d secure request.
     */
    public static function create($params = null, $opts = null)
    {
        return self::_create($params, $opts);
    }
}