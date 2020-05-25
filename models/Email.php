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
//        var_dump($response);exit;
        if (!$response->hasErrors()) {
            //remove the data that do not contain errors
            /** @var Email $model */
            foreach ($response->data as $key => $model) {
                if (!$model) continue;
                if (!$model->containsValidEmail()) {
                    unset($response->data->{$key});
                }
            }
        }
        return $response;
    }

    public function listEmailAddressesWithDisk($domain)
    {
        $url = '/execute/Email/list_pops_with_disk?domain=' . $domain;
        $response = $this->call($url, $this->config);
//        var_dump($response);exit;
        if (!$response->hasErrors()) {
            //remove the data that do not contain errors
            /** @var Email $model */
            foreach ($response->data as $key => $model) {
                if (!$model) continue;
                if (!$model->containsValidEmail()) {
                    unset($response->data->{$key});
                }
            }
        }
        return $response;
    }

    public function containsValidEmail()
    {
        return isset($this->email) && $this->email && filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    public function emailExists($needle)
    {
        $list = $this->listEmailAddresses();
        if (!$list->hasErrors()) {
            /** @var Email $email */
            foreach ($list->data as $email) {
                if ($email->email == $needle) return true;
            }
        }
        return false;
    }

    public function addAccount($email, $password, $quota = 100)
    {
        $url = '/execute/Email/add_pop?email=' . $email . '&password=' . $password . '&quota=' . $quota;
        $response = $this->call($url, $this->config);
        return $response;
    }

    public function deleteAccount($email)
    {
        $url = '/execute/Email/delete_pop?email=' . $email;
        $response = $this->call($url, $this->config);
        return $response;
    }

    public function getQuota($email)
    {
        $tmp = explode('@', $email);
        $domain = '';
        if (isset($tmp[1])) {
            $domain = $tmp[1];
        }
        $url = '/execute/Email/get_pop_quota?email=' . $tmp[0] . '&domain=' . $domain;
        $response = $this->call($url, $this->config);
        return $response;
    }

    public function setQuota($email, $quota)
    {
        $tmp = explode('@', $email);
        $domain = '';
        if (isset($tmp[1])) {
            $domain = $tmp[1];
        }
        $url = '/execute/Email/edit_pop_quota?email=' . $tmp[0] . '&domain=' . $domain . '&quota=' . $quota;
//        echo $url;
//        exit;
        $response = $this->call($url, $this->config);
        return $response;
    }
}