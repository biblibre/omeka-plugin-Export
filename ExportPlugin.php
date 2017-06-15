<?php

class ExportPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'define_routes',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main',
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $db = $this->_db;

        $db->query("
            CREATE TABLE IF NOT EXISTS `{$db->prefix}export_exporters` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `writer_name` varchar(255) NOT NULL,
                `writer_config` text NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $db->query("
            CREATE TABLE IF NOT EXISTS `{$db->prefix}export_exports` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `exporter_id` int unsigned NOT NULL,
                `writer_params` text NULL DEFAULT NULL,
                `status` varchar(255) NULL DEFAULT NULL,
                `started` timestamp NULL DEFAULT NULL,
                `ended` timestamp NULL DEFAULT NULL,
                `filename` varchar(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY (`exporter_id`),
                CONSTRAINT `{$db->prefix}export_exports_fk_exporter_id`
                  FOREIGN KEY (`exporter_id`) REFERENCES `{$db->prefix}export_exporters` (`id`)
                  ON DELETE RESTRICT ON UPDATE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $db->query("
            CREATE TABLE IF NOT EXISTS `{$db->prefix}export_logs` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `export_id` int unsigned NOT NULL,
                `severity` int NOT NULL DEFAULT 0,
                `message` text NOT NULL,
                `params` text NULL DEFAULT NULL,
                `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY (`export_id`),
                CONSTRAINT {$db->prefix}export_logs_fk_export_id
                  FOREIGN KEY (`export_id`) REFERENCES `{$db->prefix}export_exports` (`id`)
                  ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;

        $db->query("DROP TABLE IF EXISTS `{$db->prefix}export_logs`");
        $db->query("DROP TABLE IF EXISTS `{$db->prefix}export_exports`");
        $db->query("DROP TABLE IF EXISTS `{$db->prefix}export_exporters`");
    }

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');

        Zend_Registry::set('export_writer_manager', new Export_Writer_Manager());

        $events = Zend_EventManager_StaticEventManager::getInstance();
        $events->attach(ExportPlugin::class, 'writers', array($this, 'getWriters'));
    }

    public function getWriters()
    {
        $writers = array(
            'csv' => 'Export_Writer_CsvWriter',
        );

        return $writers;
    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];

        $router->addRoute(
            'export-id',
            new Zend_Controller_Router_Route(
                'export/:controller/:id/:action',
                array(
                    'module' => 'export',
                )
            )
        );
    }

    /**
     * Add the Simple Pages link to the admin main navigation.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Export'),
            'uri' => url('export'),
        );
        return $nav;
    }
}
