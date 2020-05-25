<?php

namespace ctblue\cpanel\models;

use ctblue\cpanel\Config;

class BaseModel
{
    public $errors = [];
    protected $dataClass = '';
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
//        var_dump($jsonData);exit;
        $jsonData = json_decode($jsonData, true);
        $response = new Response($jsonData, $this->dataClass);
//        var_dump($response->data);
//        exit;
        return $response;
    }

    public function set($data)
    {
        foreach ($data AS $key => $value) {
            if ($key == 'data' && is_array($value)) {
                if ($this->dataClass) {
                    for ($i = 0; $i < count($value); $i++) {
                        $v = $value[$i];
                        $sub = new $this->dataClass;
                        $sub->set($v);
                        unset($sub->dataClass);
                        unset($sub->config);
                        $value[$i] = $sub;
                    }
                }
            }
            $this->{$key} = $value;
        }
    }
}