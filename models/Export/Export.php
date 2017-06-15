<?php

class Export_Export extends Omeka_Record_AbstractRecord
{
    public $exporter_id;
    public $writer_params;
    public $status;
    public $started;
    public $ended;
    public $filename;

    public function getExporter()
    {
        return $this->getTable('Export_Exporter')->find($this->exporter_id);
    }

    public function setWriterParams($params)
    {
        $this->writer_params = isset($params) ? serialize($params) : null;
    }

    public function getWriterParams()
    {
        if (isset($this->writer_params)) {
            return unserialize($this->writer_params);
        }
    }

    protected function afterDelete()
    {
        $filepath = FILES_DIR . '/exports/' . $this->filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
