<?php

namespace DorRequire\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class RequireAsset extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $queue;

    protected $dependencies;

    protected $serviceLocator;

    public function __construct()
    {
        $this->queue = array();
    }

    public function __invoke()
    {
        $ids = func_get_args();

        foreach ($ids as $id) {
            $this->addToQueue($id);
        }

        return $this;
    }

    public function __toString()
    {
        $this->queue = array_merge($this->getPriorities(), $this->queue);
        $tags = $this->resolveDependencies($this->queue);
        return implode("\n", $tags) . "\n";
    }

    protected function addToQueue($id)
    {
        if (!in_array($id, $this->queue)) {
            array_push($this->queue, $id);
        }
    }

    protected function resolveDependencies($dependencies)
    {
        $tags = array();

        if ($dependencies) {
            $config = $this->getConfig();

            foreach ($dependencies as $dependencie) {
                $tags += $this->loadLibrary($config[$dependencie]);
            }
        }

        return $tags;
    }

    protected function loadLibrary($files)
    {
        $tags = array();

        foreach ($files as $file => $subDependencies) {
            $tags += $this->resolveDependencies($subDependencies);
            $tag = array($file => $this->createScriptTag($file));
            $tags += $tag;
        }

        return $tags;
    }

    protected function createScriptTag($file)
    {
        return '<script type="text/javascript" src="'. $file .'"></script>';
    }

    protected function getPriorities()
    {
        $config = $this->getConfig();

        if (isset($config['priority'])) {
            return $config['priority'];
        }

        return array();
    }

    protected function getConfig()
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('config');

        if (isset($config['dor-require'])) {
            return $config['dor-require'];
        }

        return array();
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
