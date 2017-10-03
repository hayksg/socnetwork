<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/[:action]',
                    'constraints' => [
                        'action' => '[a-z]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/application[/page/:page][/:action[/:id]]',
                    'constraints' => [
                        'page' => '[0-9]+',
                        'action' => '[a-z-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'timeline' => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/timeline[/page/:page][/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-z-]*',
                        'id'     => '[0-9]+',
                        'page'   => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TimelineController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'friend' => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/friends[/:action[/:username]]',
                    'constraints' => [
                        'action'   => '[a-z]*',
                        'username' => '[a-zA-Z]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\FriendController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'search' => [
                'type'    => Literal::class,
                'options' => [
                    'route'       => '/search',
                    'defaults' => [
                        'controller' => Controller\SearchController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'profile' => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/profile[/:username[/:action]]',
                    'constraints' => [
                        'username' => '[a-zA-Z]*',
                        'action' => '[a-z]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'gallery' => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/gallery[/:action[/:username][/:id]]',
                    'constraints' => [
                        'action'   => '[a-z]*',
                        'id'       => '[0-9]+',
                        'username' => '[a-zA-Z]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\GalleryController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'navigation' => [
        'top_navigation' => [
            'timeline' => [
                'label' => 'Timeline',
                'route' => 'timeline',
            ],
            'contact' => [
                'label' => 'Contact',
                'route' => 'contact-us',
            ],
        ],
    ],
];
