<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\HomeController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'filme' => [  // rota para FilmeController
                'type' => Segment::class,
                'options' => [
                    'route' => '/filme[/:action]',
                    'defaults' => [
                        'controller' => Controller\DetalhesFilmeController::class,
                        'action' => 'detalhes', // action padrão
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\HomeController::class => function($container) {
                return new Controller\HomeController();
            },
            Controller\DetalhesFilmeController::class => function($container) {
                return new Controller\DetalhesFilmeController();
            },
        ],
    ],



    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/home/index' => __DIR__ . '/../view/application/home/index.phtml',
            'application/detalhes-filme/detalhes' => __DIR__ . '/../view/application/filme/detalhesFilme.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
