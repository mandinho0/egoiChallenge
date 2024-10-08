<?php

declare(strict_types=1);

namespace Application;

use Application\Command\SendSMSCommand;
use Application\Controller\IndexController;
use Application\Model\RecipientRepository;
use Application\Service\SmsConsumer;
use Application\Service\SmsProducer;
use Application\Service\SmsService;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
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
            IndexController::class => function ($container) {
                $dbAdapter = $container->get(Adapter::class);
                return new IndexController($dbAdapter);
            },
            Controller\RecipientController::class => function ($container) {
                $recipientRepository= $container->get(Model\RecipientRepository::class);
                return new Controller\RecipientController($recipientRepository);
            },
        ],
    ],
    'service_manager' => [
        'factories' => [
            // Factory to RecipientRepository
            Model\RecipientRepository::class => function ($container) {
                return new Model\RecipientRepository($container->get('RecipientTableGateway'));
            },
            'RecipientTableGateway' => function ($container) {
                return new TableGateway('recipients', $container->get(Adapter::class));
            },
            SendSMSCommand::class => function ($container) {
                return new SendSMSCommand(
                    $container->get(SmsService::class),
                    $container->get(SmsProducer::class),
                    $container->get(SmsConsumer::class),
                    $container->get(RecipientRepository::class)
                );
            },
            SmsService::class => function ($container) {
                return new SmsService($container->get(RecipientRepository::class));
            },
            SmsProducer::class => InvokableFactory::class,
            SmsConsumer::class => InvokableFactory::class,
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
    'laminas-cli' => [
        'commands' => [
            'app:sendSMS' => Command\SendSMSCommand::class,
        ],
    ],
];
