<?php

class Export_Form_ExporterStartForm extends Omeka_Form
{
    public function init()
    {
        $this->addElement('submit', 'submit', array(
            'label' => __('Start export'),
        ));
    }
}
