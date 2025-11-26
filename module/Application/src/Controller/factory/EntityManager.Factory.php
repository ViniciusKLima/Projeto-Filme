<?php

namespace Application\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;

class EntityManagerFactory
{
    public function __invoke($container)
    {
        // caminho correto para a pasta de entidades (sobe dois níveis desde 'factory')
        $paths = [__DIR__ . '/../../Entity']; // Pasta das entidades
        $isDevMode = true;

        // As entidades do projeto usam anotations (docblocks) @ORM\...,
        // portanto usamos a configuração por annotations, não por attributes.
        // Usar configuração por attributes (ORM 3)
        if (class_exists(\Doctrine\ORM\ORMSetup::class) && method_exists(\Doctrine\ORM\ORMSetup::class, 'createAttributeMetadataConfiguration')) {
            $config = \Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
        } else {
            throw new \RuntimeException('Nenhuma API do Doctrine disponível para configuração de metadata.');
        }

        $connectionParams = [
            'driver'   => 'pdo_mysql',
            'host'     => 'buqhwzttbfnrkryplja1-mysql.services.clever-cloud.com',
            'port'     => 3306,
            'user'     => 'u1o3iwc66b56iik7',
            'password' => 'SSExIOA2cAcORbYjQ4JC',
            'dbname'   => 'buqhwzttbfnrkryplja1',
            'charset'  => 'utf8mb4',
        ];

        $connection = DriverManager::getConnection($connectionParams);
        return new EntityManager($connection, $config);
    }
}
