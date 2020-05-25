<?php

namespace ctblue\cpanel\models;


use ctblue\cpanel\Config;
use ctblue\cpanel\models\BaseModel;

class Email extends BaseModel
{
    public $login;
    public $suspended_incoming;
    public $suspended_login;
    public $email;

    /**
     * Email constructor.
     * @param $config Config|null
     */
    function __construct($config = null)
    {
        $this->dataClass = 'ctblue\\cpanel\\models\\Email';
        if ($config) {
            parent::__construct($config);
        }
    }

    public function listEmailAddresses()
    {
        $url = '/execute/Email/list_pops';
        $response = $this->call($url, $this->config);
        if (!$response->hasErrors()) {
            //remove the data that do not contain errors
            /** @var Email $model */
            foreach ($response->data as $key => $model) {
                $valid = false;
                if ($model->email && filter_var($model->email, FILTER_VALIDATE_EMAIL)) {
                    $valid = true;
                }
                if (!$valid) unset($response->data->{$key});
            }
        }
        return $response;
    }
}