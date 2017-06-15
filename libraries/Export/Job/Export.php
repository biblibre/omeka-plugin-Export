<?php

class Export_Job_Export extends Omeka_Job_AbstractJob
{
    protected $export;
    protected $logger;

    public function perform()
    {
        $export = $this->getExport();

        $directory = FILES_DIR . '/exports';
        if (!is_dir($directory) && !mkdir($directory)) {
            $this->getLogger()->log('Failed to create directory %s',
                Zend_Log::ERR, array($directory));

            $export->status = 'error';
            $export->save();

            return;
        }

        if (!is_writable($directory)) {
            $this->getLogger()->log('Directory %s is not writable',
                Zend_Log::ERR, array($directory));

            $export->status = 'error';
            $export->save();

            return;
        }

        $exporter = $export->getExporter();
        $writer = $exporter->getWriter();
        $writer->setLogger($this->getLogger());
        if ($writer instanceof Export_Parametrizable) {
            $writer->setParams($export->getWriterParams());
        }

        try {
            $this->getLogger()->log('Export started', Zend_Log::NOTICE);
            $export->started = date('Y-m-d H:i:s');
            $export->status = 'in progress';

            $export->save();

            $tempfilepath = tempnam(sys_get_temp_dir(), 'omeka-export-');
            $fh = fopen($tempfilepath, 'w');
            $writer->write($fh);
            fclose($fh);

            $ext = $writer->getFilenameExt();
            $dt = new DateTime($export->started);
            $date = $dt->format(DateTime::ATOM);
            $filename = $exporter->name . '-' . $date . ".$ext";
            $filepath = $directory . '/' . $filename;

            copy($tempfilepath, $filepath);
            unlink($tempfilepath);

            $this->getLogger()->log('Export completed', Zend_Log::NOTICE);
            $export->filename = $filename;
            $export->status = 'completed';
            $export->ended = date('Y-m-d H:i:s');
            $export->save();
        } catch (Exception $e) {
            $this->getLogger()->log("$e", Zend_Log::ERR);

            $export->status = 'error';
            $export->save();
        }

    }

    protected function getLogger()
    {
        if (!isset($this->logger)) {
            $this->logger = new Export_Logger();
            $this->logger->setExport($this->getExport());
        }

        return $this->logger;
    }

    protected function getExport()
    {
        if (!isset($this->export)) {
            $exportId = $this->_options['exportId'];
            $this->export = $this->_db->getTable('Export_Export')->find($exportId);
        }

        return $this->export;
    }
}
