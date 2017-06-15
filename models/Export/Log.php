<?php

class Export_Log extends Omeka_Record_AbstractRecord
{
    public $export_id;
    public $severity;
    public $message;
    public $params;
    public $added;

    public function getMessage()
    {
        $params = $this->params ? unserialize($this->params) : array();
        array_unshift($params, $this->message);

        return call_user_func_array('__', $params);
    }
}
