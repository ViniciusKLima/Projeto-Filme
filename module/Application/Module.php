<?php
namespace Application;

use Laminas\Mvc\MvcEvent;
use Doctrine\ORM\EntityManager;

class Module
{
    /**
     * Module bootstrap hook
     *
     * Executado no bootstrap do Laminas. Aqui colocamos uma tentativa leve de
     * restaurar a sessão a partir do cookie `remember_me` caso o usuário tenha
     * marcado "lembrar-me" anteriormente. Isso permite que, em visitas futuras,
     * o usuário já esteja autenticado mesmo antes do framework processar a
     * requisição completa.
     *
     * Atenção:
     * - é um mecanismo de conveniência para desenvolvimento/ambiente local;
     * - para produção recomenda-se armazenar apenas hashes dos tokens e usar
     *   cookies seguros (secure flag) + HTTPS.
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Restaura sessão a partir do cookie remember_me, se existir
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['user']) && !empty($_COOKIE['remember_me'])) {
            $sm = $e->getApplication()->getServiceManager();
            if ($sm->has(EntityManager::class)) {
                $em = $sm->get(EntityManager::class);
                try {
                    $token = $_COOKIE['remember_me'];
                    // debug log to help diagnose cookie restore
                    @mkdir(__DIR__ . '/../../data', 0755, true);
                    @file_put_contents(__DIR__ . '/../../data/remember_debug.log', date('c') . " - found cookie remember_me\n", FILE_APPEND);
                    $repo = $em->getRepository(\Application\Entity\User::class);
                    $user = $repo->findOneBy(['rememberToken' => $token]);
                    if ($user) {
                        @file_put_contents(__DIR__ . '/../../data/remember_debug.log', date('c') . " - token matched user id: " . $user->getId() . "\n", FILE_APPEND);
                        $_SESSION['user'] = [
                            'id'    => $user->getId(),
                            'nome'  => $user->getNome(),
                            'email' => $user->getEmail(),
                            'tipo'  => $user->getTipoConta()
                        ];
                    }
                } catch (\Throwable $ex) {
                    // se falhar, não restaura nada
                    @file_put_contents(__DIR__ . '/../../data/remember_debug.log', date('c') . " - restore error: " . $ex->getMessage() . "\n", FILE_APPEND);
                }
            }
        }
    }
}
