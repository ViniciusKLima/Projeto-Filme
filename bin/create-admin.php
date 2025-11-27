<?php
/**
 * Script para criar usuário admin no banco de dados
 * 
 * Uso: php bin/create-admin.php
 * 
 * Cria um usuário admin com:
 * - Email: admin@filmes.local
 * - Senha: Admin@123456
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Application\Entity\User;

// ========== Configurar Doctrine ==========
try {
    $paths = [__DIR__ . '/../module/Application/src/Entity'];
    $isDevMode = true;

    if (class_exists(ORMSetup::class) && method_exists(ORMSetup::class, 'createAttributeMetadataConfiguration')) {
        $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
    } else {
        throw new RuntimeException('Nenhuma API do Doctrine disponível.');
    }

    // Carregar configuração do banco
    $configPath = __DIR__ . '/../config/autoload/doctrine.local.php';
    if (file_exists($configPath)) {
        $dbConfig = include $configPath;
        $connectionParams = $dbConfig['doctrine']['connection']['params'] ?? [];
    } else {
        $connectionParams = [
            'driver'   => 'pdo_mysql',
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'user'     => 'root',
            'password' => '',
            'dbname'   => 'projeto_filmes',
            'charset'  => 'utf8mb4',
        ];
    }

    $connection = DriverManager::getConnection($connectionParams);
    $em = new EntityManager($connection, $config);

    echo "✅ Conectado ao banco de dados.\n\n";

} catch (Exception $e) {
    echo "❌ Erro ao conectar ao banco: " . $e->getMessage() . "\n";
    exit(1);
}

// ========== Verificar se admin já existe ==========
$repo = $em->getRepository(User::class);
$adminExistente = $repo->findOneBy(['email' => 'admin@filmes.local']);

if ($adminExistente) {
    echo "⚠️  Usuário admin já existe!\n";
    echo "Email: admin@filmes.local\n";
    echo "Tipo: " . $adminExistente->getTipoConta() . "\n";
    exit(0);
}

// ========== Criar usuário admin ==========
try {
    $admin = new User();
    $admin->setNome('Administrador');
    $admin->setEmail('admin@filmes.local');
    $admin->setSenha(password_hash('Admin@123456', PASSWORD_DEFAULT));
    $admin->setTipoConta('admin');

    $em->persist($admin);
    $em->flush();

    echo "✅ Usuário admin criado com sucesso!\n\n";
    echo str_repeat("=", 60) . "\n";
    echo "📧 Email:    admin@filmes.local\n";
    echo "🔑 Senha:    Admin@123456\n";
    echo "👤 Tipo:     admin\n";
    echo str_repeat("=", 60) . "\n";
    echo "\n💡 Use essas credenciais para fazer login em: /auth/login\n";

} catch (Exception $e) {
    echo "❌ Erro ao criar admin: " . $e->getMessage() . "\n";
    exit(1);
}
