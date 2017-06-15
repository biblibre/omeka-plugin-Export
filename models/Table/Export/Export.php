<?php

class Table_Export_Export extends Omeka_Db_Table
{
    public function getLastExports()
    {
        return $this->findBy(array(
            'sort_field' => 'id',
            'sort_dir' => 'd',
        ), 5);
    }
}
