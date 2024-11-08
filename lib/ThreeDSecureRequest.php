<?php

namespace Payjp;

class ThreeDSecureRequest extends ApiResource
{
    public static function className()
    {
        return 'three_d_secure_request';
    }

    /**
     * @param string $id The ID of the three d secure to retrieve.
     * @param array|string|null $opts
     *
     * @return ThreeDSecureRequest
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }
}
