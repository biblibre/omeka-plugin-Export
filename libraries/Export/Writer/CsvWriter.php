<?php

class Export_Writer_CsvWriter extends Export_Writer_AbstractWriter
    implements Export_Configurable, Export_Parametrizable
{
    use Export_ConfigurableTrait, Export_ParametrizableTrait;

    protected $currentRow;
    protected $currentRowData;
    protected $headers;

    public function getLabel()
    {
        return 'CSV';
    }

    public function getFilenameExt()
    {
        return 'csv';
    }

    public function getConfigForm()
    {
        return new Export_Form_CsvWriterConfigForm(array(
            'writerConfig' => $this->getConfig(),
        ));
    }

    public function handleConfigForm(Zend_Form $form)
    {
        $values = $form->getValues();
        $config = array(
            'delimiter' => $values['delimiter'],
            'enclosure' => $values['enclosure'],
            'escape' => $values['escape'],
            'value_sep' => $values['value_sep'],
        );

        $this->setConfig($config);
    }

    public function getParamsForm()
    {
        return new Export_Form_CsvWriterParamsForm(array(
            'writer' => $this,
        ));
    }

    public function handleParamsForm(Zend_Form $form)
    {
        $values = $form->getValues();
        $this->setParams(array(
            'delimiter' => $values['delimiter'],
            'enclosure' => $values['enclosure'],
            'escape' => $values['escape'],
            'value_sep' => $values['value_sep'],
        ));
    }

    public function write($fh)
    {
        $delimiter = $this->getParam('delimiter', ',');
        $enclosure = $this->getParam('enclosure', '"');
        $escape = $this->getParam('escape', '\\');

        $headers = $this->csvHeaders();
        fputcsv($fh, $headers, $delimiter, $enclosure, $escape);

        $db = get_db();
        $items = $db->getTable('Item')->findAll();

        foreach ($items as $item) {
            $this->logger->log('Processing item %s', Zend_Log::INFO, array($item->id));

            $fields = $this->csvFieldsFromItem($item);
            fputcsv($fh, $fields, $delimiter, $enclosure, $escape);
        }
    }

    protected function csvHeaders()
    {
        $elements = $this->getElements();

        $headers = array_map(function($e) {
            return implode(':', array($e->getElementSet()->name, $e->name));
        }, $elements);

        return $headers;
    }

    protected function csvFieldsFromItem(Item $item)
    {
        $elements = $this->getElements();
        $elementTexts = $item->getAllElementTextsByElement();
        $value_sep = $this->getParam('value_sep', '|');

        $fields = array();
        foreach ($elements as $element) {
            $field = '';

            if (isset($elementTexts[$element->id])) {
                $field = implode($value_sep, array_map(function ($et) {
                    return $et->text;
                }, $elementTexts[$element->id]));
            }

            $fields[] = $field;
        }

        return $fields;
    }

    protected function getElements()
    {
        if (!isset($this->elements)) {
            $this->elements = get_db()->getTable('Element')->findAll();
        }

        return $this->elements;
    }
}
