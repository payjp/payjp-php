<?php

namespace Payjp;

class Customer extends ApiResource
{
    private static function _validateParams($params = null)
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to Payjp API "
                . "method calls.  (HINT: an example call to create a charge "
                . "would be: \"Payjp\\Charge::create(array('amount' => 100, "
                . "'currency' => 'usd', 'card' => array('number' => "
                . "4242424242424242, 'exp_month' => 5, 'exp_year' => 2015)))\")";
                    throw new Error\Api($message);
        }
    }
    /**
     * @param string $id The ID of the customer to retrieve.
     * @param array|string|null $opts
     *
     * @return Customer
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return array An array of Customers.
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Customer The created customer.
     */
    public static function create($params = null, $opts = null)
    {
        return self::_create($params, $opts);
    }

    /**
     * @param array|string|null $opts
     *
     * @return Customer The saved customer.
     */
    public function save($opts = null)
    {
        return $this->_save($opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Customer The deleted customer.
     */
    public function delete($params = null, $opts = null)
    {
        return $this->_delete($params, $opts);
    }

    /**
     * @param array|null $params
     *
     * @return array An array of the customer's Charges.
     */
    public function charges($params = null)
    {
        if (!$params) {
            $params = array();
        }
        $params['customer'] = $this->id;
        $charges = Charge::all($params, $this->_opts);
        return $charges;
    }
}
