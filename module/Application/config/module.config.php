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
            'auth' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/auth[/:action]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'filmes' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/adm',
                    'defaults' => [
                        'controller' => Controller\FilmesController::class,
                        'action' => 'index',
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
                    // Registrar AuthController sem instanciar o EntityManager (carregado preguiçosamente)
                    Controller\AuthController::class => function($container) {
                        return new Controller\AuthController();
                    },
        ],
    ],

    // Registrar a fábrica do EntityManager como serviço disponível no container
            'service_manager' => [
        'factories' => [
            \Doctrine\ORM\EntityManager::class => function($container) {
                $paths = [__DIR__ . '/../src/Entity'];
                $isDevMode = true;

                // Usar configuração por attributes (Doctrine ORM 3)
                if (class_exists(\Doctrine\ORM\ORMSetup::class) && method_exists(\Doctrine\ORM\ORMSetup::class, 'createAttributeMetadataConfiguration')) {
                    $config = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
                } else {
                    throw new \RuntimeException('Nenhuma API do Doctrine disponível para configuração de metadata.');
                }

                // Ler credenciais do array de configuração (merge de config/autoload/*.php)
                $appConfig = [];
                try {
                    $appConfig = $container->get('config');
                } catch (\Exception $e) {
                    // se não disponível, segue com fallback abaixo
                }

                $connectionParams = $appConfig['doctrine']['connection']['params'] ?? [
                    'driver'   => 'pdo_mysql',
                    'host'     => '127.0.0.1',
                    'port'     => 3306,
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'projeto_filmes',
                    'charset'  => 'utf8mb4',
                ];

                // Criar conexão DBAL e instanciar EntityManager (Doctrine ORM 3)
                $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
                return new \Doctrine\ORM\EntityManager($connection, $config);
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
                // views de autenticação
                'application/auth/login' => __DIR__ . '/../view/application/login/login.phtml',
                'application/auth/cadastro' => __DIR__ . '/../view/application/login/cadastro.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
