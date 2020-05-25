<?php


namespace ctblue\cpanel\models;


class Response extends BaseModel
{
    public $messages;
    public $status;
    public $data = [];
    public $errors = false;
    public $metadata;
    public $warnings;


    public function __construct($json = false, $dataClass = '')
    {
        $this->dataClass = $dataClass;
        if ($json) $this->set($json);
    }

    public function hasErrors()
    {
//        var_dump($this->errors);exit;
        if ($this->errors) {
            if (is_array($this->errors) && count($this->errors) > 0 || is_object($this->errors)) return true;
        }
        return false;
    }
}