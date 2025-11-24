 <?php
require 'vendor/autoload.php';

 //Carrega o EntityManager
$container = require 'config/autoload/doctrine.local.php';
$entityManager = $container['doctrine']['entity_manager'];

use Doctrine\ORM\Tools\SchemaTool;

$schemaTool = new SchemaTool($entityManager);
$classes = $entityManager->getMetadataFactory()->getAllMetadata();

$schemaTool->updateSchema($classes, true);
 echo "Tabelas criadas/atualizadas com sucesso!\n"; 
