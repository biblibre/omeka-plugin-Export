<?php

class Export_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $db = $this->getHelper('db');

        $exporters = $db->getTable('Export_Exporter')->findAll();
        $exports = $db->getTable('Export_Export')->getLastExports();

        $this->view->exporters = $exporters;
        $this->view->exports = $exports;
    }
}
