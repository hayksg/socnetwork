<?php

namespace Admin;

use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;
use Authentication\Form\UpdateForm;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Http;
use Zend\Session\Container;

class Module
{
    const VERSION = '3.0.2dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'getAccess' => function ($container) {
                    return new Controller\Plugin\GetAccess(
                        $container->get(AuthenticationService::class)
                    );
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\UserController::class => function ($container) {
                    return new Controller\UserController(
                        $container->get(EntityManager::class),
                        $container->get(UpdateForm::class)
                    );
                },
                Controller\StatusController::class => function ($container) {
                    return new Controller\StatusController(
                        $container->get(EntityManager::class)
                    );
                },
            ],
        ];
    }

    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            'dispatch',
            function ($e) {
                $controller = $e->getTarget();
                $controller->getAccess();
            },
            100
        );
    }

    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            'dispatch',
            function ($e) {
                $request = $e->getRequest();
                if (! $request instanceof Http\Request) {
                    return;
                }

                $translator = $e->getApplication()->getServiceManager()->get('translator');
                $container = new Container('language');
                $lang = $container->language;

                if (! $lang) {
                    $lang = 'en_US';
                }

                $translator->setLocale($lang);
                $e->getViewModel()->setVariable('language', $lang);
            },
            100
        );
    }
}
