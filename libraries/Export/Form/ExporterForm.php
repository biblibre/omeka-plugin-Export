<?php

class Export_Form_ExporterForm extends Omeka_Form
{
    /**
     * @var Export_Exporter
     */
    protected $exporter;

    public function init()
    {
        parent::init();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $this->addElement('text', 'name', array(
            'label' => __('Name'),
            'value' => isset($this->exporter) ? $this->exporter->name : null,
        ));

        $this->addElement('select', 'writer_name', array(
            'label' => __('Writer'),
            'multiOptions' => $this->getWriterOptions(),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => __('Save'),
        ));

        $this->addDisplayGroup(array(
            'name',
            'writer_name',
        ), 'exporter_info');

        $this->addDisplayGroup(array(
            'submit',
        ), 'exporter_submit');
    }

    public function setExporter(Export_Exporter $exporter)
    {
        $this->exporter = $exporter;
    }

    protected function getWriterOptions()
    {
        $writerOptions = array();

        $writerManager = Zend_Registry::get('export_writer_manager');
        $writers = $writerManager->getAll();
        foreach ($writers as $key => $writer) {
            $writerOptions[$key] = $writer->getLabel();
        }

        return $writerOptions;
    }
}
