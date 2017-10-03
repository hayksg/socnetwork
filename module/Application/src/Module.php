<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Doctrine\ORM\EntityManager;
use Authentication\Form\UpdateForm;
use Zend\Mvc\MvcEvent;
use Application\Controller\IndexController;
use Zend\Session\Container;
use Zend\Http;
use Zend\ModuleManager\ModuleManagerInterface;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'getYear'           => View\Helper\GetYear::class,
                'getNameOrUsername' => View\Helper\GetNameOrUsername::class,
                'getHumanTiming'    => View\Helper\GetHumanTiming::class,
                'replaceBadWords'   => View\Helper\ReplaceBadWords::class,
                'getImage'          => View\Helper\GetImage::class,
            ],
            'factories' => [
                'isFriends' => function ($container) {
                    return new View\Helper\IsFriends(
                        $container->get(EntityManager::class)
                    );
                },
                'getStatusReplay' => function ($container) {
                    return new View\Helper\GetStatusReplay(
                        $container->get(EntityManager::class)
                    );
                },
                'getLikeCount' => function ($container) {
                    return new View\Helper\GetLikeCount(
                        $container->get(EntityManager::class)
                    );
                },
            ]
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'top_navigation' => Service\TopNavigation::class,
                'annotationFormService' => function ($container) {
                    return new Service\AnnotationFormService(
                        $container->get(EntityManager::class)
                    );
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($container) {
                    return new Controller\IndexController(
                        $container->get(EntityManager::class)
                    );
                },
                Controller\SearchController::class => function ($container) {
                    return new Controller\SearchController(
                        $container->get(EntityManager::class)
                    );
                },
                Controller\ProfileController::class => function ($container) {
                    return new Controller\ProfileController(
                        $container->get(EntityManager::class),
                        $container->get(UpdateForm::class)
                    );
                },
                Controller\FriendController::class => function ($container) {
                    return new Controller\FriendController(
                        $container->get(EntityManager::class)
                    );
                },
                Controller\TimelineController::class => function ($container) {
                    return new Controller\TimelineController(
                        $container->get(EntityManager::class)
                    );
                },
                Controller\GalleryController::class => function ($container) {
                    return new Controller\GalleryController(
                        $container->get(EntityManager::class),
                        $container->get('annotationFormService')
                    );
                },
            ],
        ];
    }

    public function getControllerPluginConfig()
    {
        return [
            'invokables' => [
                'clearString'       => Controller\Plugin\ClearString::class,
                'getAnnotationForm' => Controller\Plugin\GetAnnotationForm::class,
                'getUserFriends'    => Controller\Plugin\GetUserFriends::class,
            ],
            'factories' => [
                'isUserActive' => function ($container) {
                    return new Controller\Plugin\IsUserActive(
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
            MvcEvent::EVENT_DISPATCH,
            function ($e) {
                $controller = $e->getTarget();
                $controllerName = $controller->getEvent()->getRouteMatch()->getParam('controller', '');
                if ($controllerName !== IndexController::class) {
                    if (! $controller->identity()) {
                        return $controller->redirect()->toRoute('home');
                    }
                    if (! $controller->isUserActive($controller->identity()->getUsername())) {
                        return $controller->redirect()->toRoute('home');
                    }
                }
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
