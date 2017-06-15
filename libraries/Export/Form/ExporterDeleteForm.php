<?php

class Export_Form_ExporterDeleteForm extends Omeka_Form
{
    /**
     * @var Export_Exporter
     */
    protected $exporter;

    public function init()
    {
        parent::init();

        $this->addElement('submit', 'submit', array(
            'label' => __('Delete exporter'),
        ));
    }

    public function setExporter(Export_Exporter $exporter)
    {
        $this->exporter = $exporter;
    }
}
