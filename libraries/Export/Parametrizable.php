<?php

interface Export_Parametrizable
{
    public function setParams($params);
    public function getParams();

    public function getParamsForm();
    public function handleParamsForm(Zend_Form $form);
}
