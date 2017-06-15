<?php

class Export_Form_CsvWriterConfigForm extends Omeka_Form
{
    protected $config;

    public function init()
    {
        parent::init();

        $config = $this->config;

        $this->addElement('text', 'delimiter', array(
            'label' => __('Default delimiter'),
            'value' => isset($config['delimiter']) ? $config['delimiter'] : ',',
        ));
        $this->addElement('text', 'enclosure', array(
            'label' => __('Default enclosure'),
            'value' => isset($config['enclosure']) ? $config['enclosure'] : '"',
        ));
        $this->addElement('text', 'escape', array(
            'label' => __('Default escape'),
            'value' => isset($config['escape']) ? $config['escape'] : '\\',
        ));
        $this->addElement('text', 'value_sep', array(
            'label' => __('Default value separator'),
            'value' => isset($config['value_sep']) ? $config['value_sep'] : '|',
        ));

        $this->addDisplayGroup(array(
            'delimiter',
            'enclosure',
            'escape',
            'value_sep',
        ), 'config_form');
    }

    public function setWriterConfig($config)
    {
        $this->config = $config;
    }
}
