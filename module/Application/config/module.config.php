<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'recipients' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/recipients[/:id]',
                    'defaults' => [
                        'controller' => Controller\RecipientController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'bulk' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/bulk',
                            'defaults' => [
                                'controller' => Controller\RecipientController::class,
                                'action' => 'bulk',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            // Factory to RecipientController
            Controller\RecipientController::class => function($container) {
                return new Controller\RecipientController(
                    $container->get(Model\RecipientTable::class)
                );
            },
        ],
    ],
    'service_manager' => [
        'factories' => [
            // Factory to RecipientTable
            Model\RecipientTable::class => function($container) {
                $tableGateway = $container->get('RecipientTableGateway');
                return new Model\RecipientTable($tableGateway);
            },
            'RecipientTableGateway' => function ($container) {
                $dbAdapter = $container->get(\Laminas\Db\Adapter\Adapter::class);
                return new \Laminas\Db\TableGateway\TableGateway('recipients', $dbAdapter);
            },
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
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
