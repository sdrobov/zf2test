<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\File;
use Application\Model\FileTable;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        /** @var ApplicationInterface $application */
        $application = $e->getTarget();
        $sm = $application->getServiceManager();
        $application->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, function () use ($e, $sm) {
            $request  = $e->getRequest();
            $response = $e->getResponse();

            if (!($request instanceof HttpRequest && $response instanceof HttpResponse)) {
                return;
            }

            /* @var $authAdapter \Zend\Authentication\Adapter\Http */
            $authAdapter = $sm->get('Application\AuthenticationAdapter');

            $authAdapter->setRequest($request);
            $authAdapter->setResponse($response);

            $result = $authAdapter->authenticate();

            if ($result->isValid()) {
                return;
            }

            $response->setContent('Access denied');
            $response->setStatusCode(HttpResponse::STATUS_CODE_401);

            $e->setResult($response);

            return false;
        });

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Application\Model\FileTable' => function ($t) {
                    $tableGateway = $t->get('FileTableGateway');
                    $table = new FileTable($tableGateway);

                    return $table;
                },
                'FileTableGateway' => function ($t) {
                    $db = $t->get('Zend\Db\Adapter\Adapter');
                    $resultSet = new ResultSet();
                    $resultSet->setArrayObjectPrototype(new File());

                    return new TableGateway('file', $db, null, $resultSet);
                }
            ],
        ];
    }
}
