<?php

namespace Payjp;

class Statement extends ApiResource
{
    public $downloadUrls;

    public function __construct($id = null, $opts = null)
    {
        parent::__construct($id, $opts);
        $this->downloadUrls = new StatementDownloadUrl(null, $opts);
        $this->downloadUrls->_url = $this->instanceUrl() . '/download_urls';
    }

    /**
     * @param string $id The ID of the statement to retrieve.
     * @param array|string|null $opts
     *
     * @return Statement
     */
    public static function retrieve($id, $opts = null)
    {
        return self::_retrieve($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return array An array of Statements.
     */
    public static function all($params = null, $opts = null)
    {
        return self::_all($params, $opts);
    }
}
