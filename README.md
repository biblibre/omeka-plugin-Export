# Export plugin for Omeka

A generic export plugin that intends to be easily extensible by other plugins.

Plugins can declare new *Writers*. *Writers* are responsible for exporting Omeka
data into a file.

The Export plugin provides a basic *Writer* that exports all items into a CSV
file.

## How to declare a new *Writer*

A plugin that wants to declare a new *Writer* would look like this:

```php
class MyExportWriterPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_filters = array('export_writers');

    public function filterExportWriters($writers)
    {
        $writers['my'] = 'MyExportWriter_Writer';

        return $writers;
    }
}
```

where `MyExportWriter_Writer` is the name of class that implements at least
`Export_Writer`. It can also implements `Export_Configurable` and
`Export_Parametrizable`.
