<?php
namespace Application;

use Laminas\Mvc\MvcEvent;
use Doctrine\ORM\EntityManager;

class Module
{
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
                    $repo = $em->getRepository(\Application\Entity\User::class);
                    $user = $repo->findOneBy(['rememberToken' => $token]);
                    if ($user) {
                        $_SESSION['user'] = [
                            'id'    => $user->getId(),
                            'nome'  => $user->getNome(),
                            'email' => $user->getEmail(),
                            'tipo'  => $user->getTipoConta()
                        ];
                    }
                } catch (\Throwable $ex) {
                    // se falhar, não restaura nada
                }
            }
        }
    }
}
