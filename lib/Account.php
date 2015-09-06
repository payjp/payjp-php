<?php

namespace Payjp;

class Account extends ApiResource
{
    public function instanceUrl()
    {
        if ($this['id'] === null) {
            return '/v1/accounts';
        } else {
            return parent::instanceUrl();
        }
    }
    
    /**
     * @param string|null $id
     * @param array|string|null $opts
     *
     * @return Account
     */
    public static function retrieve($id = null, $opts = null)
    {
        if (!$opts && is_string($id) && substr($id, 0, 3) === 'sk_') {
            $opts = $id;
            $id = null;
        }
        return self::_retrieve($id, $opts);
    }
}
