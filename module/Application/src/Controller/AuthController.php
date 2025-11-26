<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Application\Entity\User;

class AuthController extends AbstractActionController
{
    private $entityManager;

    // Aceita EntityManager opcionalmente, mas preferimos carregar preguiçosamente
    public function __construct(EntityManager $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    // Carrega o EntityManager do ServiceManager quando necessário
    private function getEntityManager()
    {
        if ($this->entityManager === null) {
            $sm = $this->getEvent()->getApplication()->getServiceManager();
            $this->entityManager = $sm->get(\Doctrine\ORM\EntityManager::class);
        }

        return $this->entityManager;
    }

    /* ===========================================================
       ============        TELA DE CADASTRO        ===============
       =========================================================== */
    public function cadastroAction()
    {
        $vm = new ViewModel();
        $vm->setTerminal(true);
        return $vm;
    }

    /* ===========================================================
       ============      PROCESSAR CADASTRO        ===============
       =========================================================== */
    public function registerAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('auth', ['action' => 'cadastro']);
        }

        $usuario = trim($this->params()->fromPost('usuario'));
        $email   = trim($this->params()->fromPost('email'));
        $senha   = trim($this->params()->fromPost('senha'));

        // ========== Validação básica ==========
        if (empty($usuario) || empty($email) || empty($senha)) {
            $vm = new ViewModel([
                'error' => 'Preencha todos os campos.'
            ]);
            $vm->setTerminal(true);
            $vm->setTemplate('application/login/cadastro');
            return $vm;
        }

        // ========== Verificar se já existe ==========
        $repo = $this->getEntityManager()->getRepository(User::class);
        $existe = $repo->findOneBy(['email' => $email]);

        if ($existe) {
            $vm = new ViewModel([
                'error' => 'Já existe um usuário com esse email.'
            ]);
            $vm->setTerminal(true);
            $vm->setTemplate('application/login/cadastro');
            return $vm;
        }

        // ========== Criar usuário ==========
        $novo = new User();
        $novo->setNome($usuario);
        $novo->setEmail($email);
        $novo->setSenha(password_hash($senha, PASSWORD_DEFAULT));
        $novo->setTipoConta('cliente');

        $em = $this->getEntityManager();
        $em->persist($novo);
        $em->flush();

        // Depois de cadastrar, não autentica automaticamente.
        // Redireciona para a tela de login para que o usuário
        // informe email e senha.
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }

    /* ===========================================================
       ============        TELA DE LOGIN           ===============
       =========================================================== */
    public function loginAction()
    {
        $vm = new ViewModel();
        $vm->setTerminal(true);
        return $vm;
    }

    /* ===========================================================
       ============        AUTENTICAR LOGIN        ===============
       =========================================================== */
    public function authenticateAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }

        $email = trim($this->params()->fromPost('email'));
        $senha = trim($this->params()->fromPost('senha'));

        if (empty($email) || empty($senha)) {
            $vm = new ViewModel([
                'error' => 'Preencha email e senha.'
            ]);
            $vm->setTerminal(true);
            $vm->setTemplate('application/login/login');
            return $vm;
        }

        $repo = $this->getEntityManager()->getRepository(User::class);
        $usuario = $repo->findOneBy(['email' => $email]);

        if (!$usuario || !password_verify($senha, $usuario->getSenha())) {
            $vm = new ViewModel([
                'error' => 'Email ou senha incorretos.'
            ]);
            $vm->setTerminal(true);
            $vm->setTemplate('application/login/login');
            return $vm;
        }

        // ========== Salvar sessão ==========
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = [
            'id'    => $usuario->getId(),
            'nome'  => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'tipo'  => $usuario->getTipoConta()
        ];

        // redireciona admin
        if ($usuario->getTipoConta() === 'admin') {
            return $this->redirect()->toRoute('admin', ['action' => 'painel']);
        }

        // gerar token de remember-me e setar cookie persistente (30 dias)
        try {
            $token = bin2hex(random_bytes(32));
            $usuario->setRememberToken($token);
            $em = $this->getEntityManager();
            $em->persist($usuario);
            $em->flush();
            setcookie('remember_me', $token, time() + 30 * 24 * 3600, '/');
        } catch (\Throwable $e) {
            // ignora falha em gerar token
        }

        return $this->redirect()->toRoute('home');
    }

    /* ===========================================================
       ============       DESLOGAR USUÁRIO         ===============
       =========================================================== */
    public function logoutAction()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // limpar remember token no banco e cookie
        if (!empty($_SESSION['user']['id'])) {
            $em = $this->getEntityManager();
            $repo = $em->getRepository(User::class);
            $u = $repo->find($_SESSION['user']['id']);
            if ($u) {
                $u->setRememberToken(null);
                $em->persist($u);
                $em->flush();
            }
        }

        // remover cookie
        setcookie('remember_me', '', time() - 3600, '/');

        // destruir sessão
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
}