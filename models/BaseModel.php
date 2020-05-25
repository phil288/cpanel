<?php

namespace ctblue\cpanel\models;

use ctblue\cpanel\Config;

class BaseModel
{
    public $errors = [];
    /**
     * @var Config|null
     */
    public $config = null;

    function __construct($config)
    {
        $this->config = $config;
        if (!$this->config->apiUrl) {
            $this->errors[] = 'Api Url is not defined';
            return false;
        }
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function call($url)
    {
        $url = $this->config->apiUrl . $url;
        $ch = curl_init($url);
        $headr = array();
        $headr[] = 'Authorization: cpanel ' . $this->config->username . ':' . $this->config->apiHash;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        curl_close($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        $return = json_decode($return);
        return $return;
    }
}