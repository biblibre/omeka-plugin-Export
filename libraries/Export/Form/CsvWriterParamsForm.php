<?php

class Export_Form_CsvWriterParamsForm extends Omeka_Form
{
    protected $writer;

    public function init()
    {
        parent::init();

        $config = $this->writer->getConfig();

        $this->addElement('text', 'delimiter', array(
            'label' => __('Delimiter'),
            'value' => isset($config['delimiter']) ? $config['delimiter'] : ',',
        ));
        $this->addElement('text', 'enclosure', array(
            'label' => __('Enclosure character'),
            'value' => isset($config['enclosure']) ? $config['enclosure'] : '"',
        ));
        $this->addElement('text', 'escape', array(
            'label' => __('Escape character'),
            'value' => isset($config['escape']) ? $config['escape'] : '\\',
        ));
        $this->addElement('text', 'value_sep', array(
            'label' => __('Value separator'),
            'value' => isset($config['value_sep']) ? $config['value_sep'] : '|',
        ));

        $this->addDisplayGroup(array(
            'delimiter',
            'enclosure',
            'escape',
            'value_sep',
        ), 'csv_writer_params');
    }

    public function setWriter(Export_Writer $writer)
    {
        $this->writer = $writer;
    }
}
