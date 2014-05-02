<?php

namespace DorRequire\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class RequireAsset extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $resolver;

    protected $serviceLocator;

    public function __construct(\DorRequire\Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke()
    {
        $ids = func_get_args();

        foreach ($ids as $id) {
            $this->resolver->appendDependencie($id);
        }

        return $this;
    }

    public function __toString()
    {
        $priorities = $this->getPriorities();

        foreach ($priorities as $priority) {
            $this->resolver->prependDependencie($priority);
        }

        $files = $this->resolver->solve();
        return $this->render($files);
    }

    protected function render($files)
    {
        $tags = '';

        foreach ($files as $file) {
            $tags .= $this->createScriptTag($file);
        }

        return $tags;
    }

    protected function createScriptTag($file)
    {
        return '<script type="text/javascript" src="'. $file .'"></script>' . "\n";
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
