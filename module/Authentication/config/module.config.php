<?php

namespace Authentication;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'register' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/register',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'authentication' => [
            'orm_default' => [
                'identity_class'      => 'Application\Entity\User',
                'identity_property'   => 'username',
                'credential_property' => 'password',
                'credential_callable' => function (\Application\Entity\User $user, $password) {
                    if ($user->getPassword() == sha1('passwordStaticSalt' . $password . $user->getPasswordSalt())) {
                        return true;
                    } else {
                        return false;
                    }
                },
            ],
        ],
    ],
];
