<?php

namespace DorRequire;

class Resolver
{
    public $map;

    protected $dependencies;

    public function __construct(array $map)
    {
        $this->map = $map;
        $this->dependencies = array();
    }

    public function appendDependencie($id)
    {
        if (!in_array($id, $this->dependencies)) {
            array_push($this->dependencies, $id);
        }
    }

    public function prependDependencie($id)
    {
        if (!in_array($id, $this->dependencies)) {
            array_unshift($this->dependencies, $id);
        }
    }

    public function solve($dependencies = false)
    {
        if ($dependencies === false) {
            $dependencies = $this->dependencies;
        }

        $files = array();

        if ($dependencies) {
            foreach ($dependencies as $dependencie) {
                $files = array_merge($files, $this->loadLibrary($this->map[$dependencie]));
            }
        }

        return $files;
    }

    protected function loadLibrary($files)
    {
        $loaded = array();

        foreach ($files as $file => $dependencies) {
            $loaded = array_merge($loaded, $this->solve($dependencies));
            $loaded[] = $file;
        }

        return array_unique($loaded);
    }
}
