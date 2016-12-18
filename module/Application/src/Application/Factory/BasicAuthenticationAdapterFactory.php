<?php

namespace Application\Factory;

use Zend\Authentication\Adapter\Http\FileResolver;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Adapter\Http as HttpAdapter;

class BasicAuthenticationAdapterFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $authConfig = $config['authentication_basic']['adapter'];
        $authAdapter = new HttpAdapter($authConfig['config']);

        $digest = new FileResolver();
        $digest->setFile($authConfig['basic']);

        $authAdapter->setBasicResolver($digest);

        return $authAdapter;
    }
}
