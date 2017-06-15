<?php

interface Export_Writer
{
    public function getLabel();
    public function getFilenameExt();
    public function setLogger(Zend_Log $logger);

    public function write($fh);
}
