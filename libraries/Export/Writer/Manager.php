<?php

class Export_Writer_Manager extends Export_AbstractPluginManager
{
    protected function getEventName()
    {
        return 'writers';
    }

    protected function getInterface()
    {
        return 'Export_Writer';
    }
}
