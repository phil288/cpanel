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
        if ($json) $this->set(json_decode($json, true));
    }

    public function hasErrors()
    {
//        var_dump(count($this->errors));exit;
        if ($this->errors) {
            if (is_array($this->errors) && count($this->errors) > 0) return true;
        }
        return false;
    }
}