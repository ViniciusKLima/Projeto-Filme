<?php

namespace Application\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class EntityManagerFactory
{
    public function __invoke($container)
    {
        $paths = [__DIR__ . '/../Entity']; // Pasta das entidades
        $isDevMode = true;

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: $isDevMode
        );

        $connectionParams = [
            'driver'   => 'pdo_mysql',
            'host'     => 'buqhwzttbfnrkryplja1-mysql.services.clever-cloud.com',
            'port'     => 3306,
            'user'     => 'u1o3iwc66b56iik7',
            'password' => 'SSExIOA2cAcORbYjQ4JC',
            'dbname'   => 'buqhwzttbfnrkryplja1',
            'charset'  => 'utf8mb4',
        ];

        return EntityManager::create($connectionParams, $config);
    }
}
