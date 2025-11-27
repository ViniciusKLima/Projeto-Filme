<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Application\Entity\Filme;

/**
 * FilmesController
 *
 * Painel de administração para CRUD de filmes via Doctrine ORM.
 * Todas as operações são persistidas no banco de dados (tabela `filmes`).
 *
 * Fluxo:
 * - indexAction: lista filmes e exibe formulário de criar/editar
 * - POST com action='add': cria novo filme
 * - POST com action='update': atualiza filme existente
 * - ?delete=ID: remove filme (via GET com confirmação)
 * - ?edit=ID: carrega dados do filme para edição
 *
 * Proteção: apenas usuários logados como admin (tipoConta='admin') podem acessar.
 */
class FilmesController extends AbstractActionController
{
    private $entityManager;

    public function __construct(EntityManager $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    private function getEntityManager()
    {
        if ($this->entityManager === null) {
            $sm = $this->getEvent()->getApplication()->getServiceManager();
            $this->entityManager = $sm->get(\Doctrine\ORM\EntityManager::class);
        }
        return $this->entityManager;
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Verifica se usuário logado é admin
    private function requireAdmin()
    {
        $this->ensureSession();
        if (empty($_SESSION['user']) || ($_SESSION['user']['tipo'] ?? '') !== 'admin') {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }
        return null;
    }

    public function indexAction()
    {
        // Protege acesso apenas para admin
        $adminCheck = $this->requireAdmin();
        if ($adminCheck) return $adminCheck;

        $this->ensureSession();
        $em = $this->getEntityManager();
        $repo = $em->getRepository(Filme::class);
        $request = $this->getRequest();

        // ========== REMOVER FILME ==========
        $deleteId = $this->params()->fromQuery('delete', null);
        if ($deleteId) {
            $filme = $repo->find((int)$deleteId);
            if ($filme) {
                $em->remove($filme);
                $em->flush();
            }
            return $this->redirect()->toRoute('filmes');
        }

        // ========== CARREGAR PARA EDITAR ==========
        $editId = $this->params()->fromQuery('edit', null);
        $editing = null;
        if ($editId) {
            $editing = $repo->find((int)$editId);
        }

        // ========== PROCESSAR POST (CREATE/UPDATE) ==========
        if ($request->isPost()) {
            $post = $request->getPost();
            $action = $post['action'] ?? 'add';

            // Validação básica
            $nome = trim((string)($post['nome'] ?? ''));
            if (empty($nome)) {
                return new ViewModel([
                    'filmes' => $repo->findAll(),
                    'editing' => null,
                    'error' => 'Nome do filme é obrigatório.'
                ]);
            }

            if ($action === 'add') {
                $filme = new Filme();
                $filme->setNome($nome);
                $filme->setDiretor(trim((string)($post['diretor'] ?? '')));
                $filme->setElenco(trim((string)($post['atoresPrincipais'] ?? '')));
                $filme->setStreaming(trim((string)($post['streaming'] ?? '')));
                $filme->setAnoLancamento((int)($post['ano'] ?? 0));
                $filme->setCapaPrincipal(trim((string)($post['capaPrincipal'] ?? '')));
                $filme->setCapaFundo(trim((string)($post['capaFundo'] ?? '')));
                $filme->setTrailer(trim((string)($post['trailer'] ?? '')));
                $filme->setSinopse(trim((string)($post['sinopse'] ?? '')));
                $filme->setGenero(trim((string)($post['genero'] ?? 'Drama')));
                $filme->setNota((float)($post['nota'] ?? 0));

                $em->persist($filme);
                $em->flush();
            } elseif ($action === 'update') {
                $id = (int)($post['id'] ?? 0);
                $filme = $repo->find($id);
                if ($filme) {
                    $filme->setNome($nome);
                    $filme->setDiretor(trim((string)($post['diretor'] ?? '')));
                    $filme->setElenco(trim((string)($post['atoresPrincipais'] ?? '')));
                    $filme->setStreaming(trim((string)($post['streaming'] ?? '')));
                    $filme->setAnoLancamento((int)($post['ano'] ?? 0));
                    $filme->setCapaPrincipal(trim((string)($post['capaPrincipal'] ?? '')));
                    $filme->setCapaFundo(trim((string)($post['capaFundo'] ?? '')));
                    $filme->setTrailer(trim((string)($post['trailer'] ?? '')));
                    $filme->setSinopse(trim((string)($post['sinopse'] ?? '')));
                    $filme->setGenero(trim((string)($post['genero'] ?? '')));
                    $filme->setNota((float)($post['nota'] ?? 0));

                    $em->flush();
                }
            }

            // PRG: redireciona para limpar POST
            return $this->redirect()->toRoute('filmes');
        }

        return new ViewModel([
            'filmes' => $repo->findAll(),
            'editing' => $editing,
        ]);
    }
}
