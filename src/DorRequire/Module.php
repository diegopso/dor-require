<?php

namespace DorRequire;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use DorRequire\View\Helper\RequireAsset;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        (new ModuleRouteListener())->attach($e->getApplication()->getEventManager());
    }

    public function getConfig()
    {
        return array();
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'require' => function ($sm) {
                    $config = $sm->getServiceLocator()->get('config');

                    if (!isset($config['dor-require'])) {
                        $config['dor-require'] = array();
                    }

                    $resolver = new Resolver($config['dor-require']);
                    $require = new RequireAsset($resolver);
                    return $require;
                }
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
