<?php
// Script para criar/atualizar o schema do Doctrine localmente
require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;

$paths = [__DIR__ . '/../module/Application/src/Entity'];
$isDevMode = true;

$config = null;
// Compatibilidade: preferir ORM\Tools\Setup, que fornece createAnnotationMetadataConfiguration
// Usar configuração por ATTRIBUTES (Doctrine ORM 3)
if (class_exists(ORMSetup::class) && method_exists(ORMSetup::class, 'createAttributeMetadataConfiguration')) {
    $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
} else {
    fwrite(STDERR, "Nenhuma API compatível do Doctrine encontrada para criar metadata configuration.\n");
    exit(1);
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
$entityManager = new EntityManager($connection, $config);

$schemaTool = new SchemaTool($entityManager);
$classes = $entityManager->getMetadataFactory()->getAllMetadata();

if (empty($classes)) {
    echo "Nenhuma metadata encontrada. Verifique o namespace e o path das entidades.\n";
    exit(1);
}

try {
    $schemaTool->updateSchema($classes, true);
    echo "Tabelas criadas/atualizadas com sucesso!\n";
} catch (\Exception $e) {
    echo "Erro ao atualizar schema: " . $e->getMessage() . "\n";
    exit(1);
}
