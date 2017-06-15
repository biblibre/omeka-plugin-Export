<?php

class Export_Logger extends Zend_Log
{
    protected $export;

    public function __construct (Zend_Log_Writer_Abstract $writer = null)
    {
        if (!isset($writer)) {
            $db = get_db();
            $dbAdapter = $db->getAdapter();
            $writer = new Zend_Log_Writer_Db($dbAdapter, $db->Export_Log, array(
                'severity' => 'priority',
                'message' => 'message',
                'export_id' => 'export_id',
                'params' => 'params',
            ));
        }

        parent::__construct($writer);
    }

    public function setExport(Export_Export $export)
    {
        $this->export = $export;
    }

    public function log($message, $severity, $extras = null)
    {
        $extras = array(
            'export_id' => $this->export->id,
            'params' => isset($extras) ? serialize($extras) : null,
        );
        parent::log($message, $severity, $extras);
    }
}
