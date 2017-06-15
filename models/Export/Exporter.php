<?php

class Export_Exporter extends Omeka_Record_AbstractRecord
{
    public $name;
    public $writer_name;
    public $writer_config;

    protected $writer;

    public function getWriter()
    {
        if (!isset($this->writer)) {
            $writerManager = Zend_Registry::get('export_writer_manager');
            $this->writer = $writerManager->get($this->writer_name);
            if ($this->writer instanceof Export_Configurable) {
                $this->writer->setConfig($this->getWriterConfig());
            }
        }

        return $this->writer;
    }

    public function setWriterConfig($config)
    {
        if (isset($config)) {
            $this->writer_config = serialize($config);
        } else {
            $this->writer_config = null;
        }
    }

    public function getWriterConfig()
    {
        if (isset($this->writer_config)) {
            return unserialize($this->writer_config);
        }
    }
}
