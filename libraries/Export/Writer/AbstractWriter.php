<?php

abstract class Export_Writer_AbstractWriter implements Export_Writer
{
    protected $logger;

    public function setLogger(Zend_Log $logger)
    {
        $this->logger = $logger;
    }
}
