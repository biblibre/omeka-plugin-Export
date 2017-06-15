<?php

abstract class Export_AbstractPluginManager
{
    protected $plugin;

    abstract protected function getEventName();
    abstract protected function getInterface();

    public function get($name)
    {
        $plugins = $this->getAll();
        if (array_key_exists($name, $plugins)) {
            return $plugins[$name];
        }
    }

    public function getAll()
    {
        if (!isset($this->plugins)) {
            $this->plugins = array();
            $events = Zend_EventManager_StaticEventManager::getInstance();
            $event = $this->getEventName();
            $listeners = $events->getListeners(ExportPlugin::class, $event);

            if (false !== $listeners) {
                $items = array();
                foreach ($listeners->getIterator() as $listener) {
                    $items = array_merge($items, $listener->call());
                }

                $interface = $this->getInterface();
                foreach ($items as $name => $class) {
                    if (class_exists($class) && in_array($interface, class_implements($class))) {
                        $this->plugins[$name] = new $class();
                    }
                }
            }

        }

        return $this->plugins;
    }
}
