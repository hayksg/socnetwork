<?php

namespace Authentication;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Session\Container;
use Zend\Http;

class Module
{
    const VERSION = '3.0.2dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                // forms
                Form\RegisterForm::class => function ($container) {
                    $form = new Form\RegisterForm();
                    $form->setInputFilter($container->get(Filter\RegisterFilter::class));
                    return $form;
                },
                Form\LoginForm::class => function ($container) {
                    $form = new Form\LoginForm();
                    $form->setInputFilter($container->get(Filter\LoginFilter::class));
                    return $form;
                },
                Form\UpdateForm::class => function ($container) {
                    $form = new Form\UpdateForm();
                    $form->setInputFilter($container->get(Filter\UpdateFilter::class));
                    return $form;
                },

                // filters
                Filter\RegisterFilter::class => function ($container) {
                    return new Filter\RegisterFilter();
                },
                Filter\LoginFilter::class => function ($container) {
                    return new Filter\LoginFilter();
                },
                Filter\UpdateFilter::class => function ($container) {
                    return new Filter\UpdateFilter();
                },

                // authentication

                AuthenticationService::class => function ($container) {
                    return $container->get('doctrine.authenticationservice.orm_default');
                },
            ],
            'invokables' => [
                'authStorage' => Model\AppAuthStorage::class,
                'validationService' => Service\ValidationService::class,
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\RegisterController::class => function ($container) {
                    return new Controller\RegisterController(
                        $container->get(EntityManager::class),
                        $container->get(Form\RegisterForm::class),
                        $container->get('validationService'),
                        $container->get(AuthenticationService::class)
                    );
                },
                Controller\LoginController::class => function ($container) {
                    return new Controller\LoginController(
                        $container->get(EntityManager::class),
                        $container->get(Form\LoginForm::class),
                        $container->get(AuthenticationService::class),
                        $container->get('authStorage')
                    );
                },
                Controller\LogoutController::class => function ($container) {
                    return new Controller\LogoutController(
                        $container->get(AuthenticationService::class),
                        $container->get('authStorage')
                    );
                },
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'cutString' => View\Helper\CutString::class,
                'captcha'   => View\Helper\RandomCaptcha::class,
            ],
        ];
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
