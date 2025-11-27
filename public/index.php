<?php

declare(strict_types=1);

use Laminas\Mvc\Application;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

/**
 * Bootstrap do front controller
 *
 * Observação: inserimos uma tentativa de restauração de sessão baseada em cookie
 * antes da execução completa do Laminas para que, se o cookie `remember_me`
 * estiver presente, o usuário apareça já autenticado na primeira página que
 * carregar (útil para manter usuário logado entre sessões do navegador).
 */

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
    if (is_string($path) && __FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `docker-compose run laminas composer install` if you are using Docker.\n"
    );
}

$container = require __DIR__ . '/../config/container.php';
// Tentativa de restaurar sessão a partir do cookie remember_me antes da aplicação iniciar
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
try {
    if (empty($_SESSION['user']) && !empty($_COOKIE['remember_me'])) {
        // Obter EntityManager do container se disponível
        if (isset($container) && $container->has(\Doctrine\ORM\EntityManager::class)) {
            $em = $container->get(\Doctrine\ORM\EntityManager::class);
            $token = $_COOKIE['remember_me'];
            $repo = $em->getRepository(\Application\Entity\User::class);
            $user = $repo->findOneBy(['rememberToken' => $token]);
            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user->getId(),
                    'nome' => $user->getNome(),
                    'email' => $user->getEmail(),
                    'tipo' => $user->getTipoConta(),
                ];
                @file_put_contents(__DIR__ . '/../data/remember_debug.log', date('c') . " - index restored session for user " . $user->getId() . "\n", FILE_APPEND);
            } else {
                @file_put_contents(__DIR__ . '/../data/remember_debug.log', date('c') . " - index token not matched\n", FILE_APPEND);
            }
        }
    }
} catch (\Throwable $ex) {
    @file_put_contents(__DIR__ . '/../data/remember_debug.log', date('c') . " - index restore error: " . $ex->getMessage() . "\n", FILE_APPEND);
}
// Run the application!
/** @var Application $app */
$app = $container->get('Application');
$app->run();
