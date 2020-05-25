<?php

namespace ctblue\cpanel\models;

use ctblue\cpanel\Config;

class BaseModel
{
    public $errors = [];
    public $dataClass = '';
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
     * @return Response
     */
    public function call($url)
    {
        $url = $this->config->apiUrl . $url;
        $ch = curl_init($url);
        $headr = array();
        $headr[] = 'Authorization: cpanel ' . $this->config->cpanelUsername . ':' . $this->config->apiHash;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $jsonData = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            if ($error_msg) {
                $response = new Response();
                $response->errors = [];
                $response->errors[] = $error_msg;
                return $response;
            }
        }
        curl_close($ch);
//        $jsonData = json_decode($jsonData);
//        echo $this->dataClass;exit;
        $response = new Response($jsonData, $this->dataClass);
        return $response;
    }

    public function set($data)
    {
        foreach ($data AS $key => $value) {
            if (is_array($value)) {
//                echo $this->dataClass;exit;
                if ($this->dataClass) {
                    $sub = new $this->dataClass;
                    $sub->set($value);
                    $value = $sub;
                }
            }
            $this->{$key} = $value;
        }
    }
}