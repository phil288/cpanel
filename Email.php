<?php

namespace ctblue\cpanel;


use ctblue\cpanel\models\BaseModel;

class Email extends BaseModel
{

    /**
     * Email constructor.
     * @param $config Config
     */
    function __construct($config)
    {
        parent::__construct($config);
    }

    public function listEmailAddresses()
    {
        $url = '/execute/Email/list_pops';
        $this->call($url, $this->config);
    }
}