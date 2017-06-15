<?php

class Export_ExportsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $db = $this->getHelper('Db');

        $exportTable = $db->getTable('Export_Export');

        $currentPage = $this->getParam('page', 1);
        $recordsPerPage = 20;
        $totalRecords = $exportTable->count();

        $exports = $exportTable->findBy(array(
            'sort_field' => 'id',
            'sort_dir' => 'd',
        ), $recordsPerPage, $currentPage);

        Zend_Registry::set('pagination', array(
            'page' => $currentPage,
            'per_page' => $recordsPerPage,
            'total_results' => $totalRecords,
        ));

        $this->view->exports = $exports;
    }

    public function logsAction()
    {
        $db = get_db();
        $exportLogTable = $db->getTable('Export_Log');

        $severity = (int) $this->getParam('severity', Zend_Log::NOTICE);

        $select = $exportLogTable->getSelect();
        $select->where('export_id = ?', $this->getParam('id'));
        $select->where('severity <= ?', $severity);
        $exportLogTable->applySorting($select, 'added', 'DESC');

        $currentPage = $this->getParam('page', 1);
        $recordsPerPage = 20;
        $selectForCount = clone $select;
        $selectForCount->reset(Zend_Db_Select::COLUMNS);
        $alias = $exportLogTable->getTableAlias();
        $selectForCount->from(array(), "COUNT(DISTINCT($alias.id))");
        $totalRecords = $db->fetchOne($selectForCount);

        $select->limitPage($currentPage, $recordsPerPage);
        $logs = $exportLogTable->fetchObjects($select);

        Zend_Registry::set('pagination', array(
            'page' => $currentPage,
            'per_page' => $recordsPerPage,
            'total_results' => $totalRecords,
        ));

        $this->view->logs = $logs;
        $this->view->severity = $severity;
    }

    public function deleteAction()
    {
        $db = get_db();

        $export = $db->getTable('Export_Export')->find($this->getParam('id'));
        if ($export) {
            $export->delete();
        }

        $referer = $this->getRequest()->getHeader('Referer');
        $url = $this->getParam('redirect', $referer);
        $url = $url ?: '/';
        $this->getHelper('Redirector')->setPrependBase(false)->goToUrl($url);
    }
}
