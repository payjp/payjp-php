<?php

namespace Payjp\Util;

use Payjp\PayjpObject;

abstract class Util
{
    // todo wanna use 'private const' (only PHP >= v7.1.0)
    private static $types = array(
        'application_url' => 'Payjp\\ApplicationUrl',
        'card' => 'Payjp\\Card',
        'charge' => 'Payjp\\Charge',
        'customer' => 'Payjp\\Customer',
        'event' => 'Payjp\\Event',
        'list' => 'Payjp\\Collection',
        'plan' => 'Payjp\\Plan',
        'subscription' => 'Payjp\\Subscription',
        'tenant' => 'Payjp\\Tenant',
        'token' => 'Payjp\\Token',
        'tenant_transfer' => 'Payjp\\TenantTransfer',
        'transfer' => 'Payjp\\Transfer',
    );

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     *
     * @param array|mixed $array
     * @return boolean True if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

      // TODO: generally incorrect, but it's correct given Payjp's response
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively converts the PHP Payjp object to an array.
     *
     * @param array $values The PHP Payjp object to convert.
     * @return array
     */
    public static function convertPayjpObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if (is_string($k) && $k[0] == '_') {
                continue;
            }
            if ($v instanceof PayjpObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertPayjpObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the Payjp API to the corresponding PHP object.
     *
     * @param array $resp The response from the Payjp API.
     * @param array $opts
     * @return Object|array
     */
    public static function convertToPayjpObject($resp, $opts)
    {
        if (self::isList($resp)) {
            $mapped = array();
            foreach ($resp as $i) {
                array_push($mapped, self::convertToPayjpObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset(self::$types[$resp['object']])) {
                $class = self::$types[$resp['object']];
            } else {
                $class = 'Payjp\\PayjpObject';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (is_string($value) && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }
}
