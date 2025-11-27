<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * AdmController
 *
 * Painel simples de administração que mantém a lista de filmes em sessão.
 * Implementa operações básicas: listar, adicionar, editar e remover.
 * Lógica original que estava em JS foi movida para aqui.
 */
class AdmController extends AbstractActionController
{
    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function seedFilms(): array
    {
        return [
            [
                'id' => 1,
                'nome' => 'Um Sonho de Liberdade',
                'sinopse' => 'Preso na década de 1940 pelo duplo homicídio de sua esposa e do amante dela, o íntegro banqueiro Andy Dufresne inicia uma nova vida na prisão de Shawshank, onde utiliza suas habilidades contábeis para trabalhar para um diretor amoral. Durante seu longo período na prisão, Dufresne passa a ser admirado pelos outros detentos – incluindo um prisioneiro mais velho chamado Red – por sua integridade e inabalável senso de esperança.',
                'anoLancamento' => 1994,
                'diretor' => 'Frank Darabont',
                'atoresPrincipais' => 'Tim Robbins, Morgan Freeman, Bob Gunton, William Sadler',
                'genero' => 'Drama',
            ],
            [
                'id' => 2,
                'nome' => 'O Poderoso Chefão',
                'sinopse' => 'Don Vito Corleone comanda uma das maiores famílias mafiosas de Nova York. Quando uma tentativa de assassinato quase tira sua vida, seu filho Michael é forçado a assumir o império, mergulhando num mundo de poder, lealdade e traição.',
                'anoLancamento' => 1972,
                'diretor' => 'Francis Ford Coppola',
                'atoresPrincipais' => 'Al Pacino, Marlon Brando, James Caan, Diane Keaton',
                'genero' => 'Crime',
            ],
            [
                'id' => 3,
                'nome' => 'O Senhor dos Anéis: O Retorno do Rei',
                'sinopse' => 'Sauron prepara ataque a Minas Tirith. Gandalf e Pippin partem para ajudar na defesa da capital de Gondor. Enquanto isso, Frodo, Sam e Gollum continuam sua jornada para destruir o Anel na Montanha da Perdição.',
                'anoLancamento' => 2003,
                'diretor' => 'Peter Jackson',
                'atoresPrincipais' => 'Elijah Wood, Sean Astin, Karl Urban, Andy Serkis',
                'genero' => 'Ficção Científica',
            ],
        ];
    }

    public function indexAction()
    {
        $this->ensureSession();

        if (!isset($_SESSION['adm_filmes']) || !is_array($_SESSION['adm_filmes'])) {
            $_SESSION['adm_filmes'] = $this->seedFilms();
        }

        $request = $this->getRequest();

        // handle removal via ?delete=ID
        $deleteId = $this->params()->fromQuery('delete', null);
        if ($deleteId) {
            $id = (int)$deleteId;
            foreach ($_SESSION['adm_filmes'] as $k => $f) {
                if ((int)$f['id'] === $id) {
                    unset($_SESSION['adm_filmes'][$k]);
                    $_SESSION['adm_filmes'] = array_values($_SESSION['adm_filmes']);
                    break;
                }
            }

            return $this->redirect()->toUrl($this->getRequest()->getUriString());
        }

        $editing = null;
        $editId = $this->params()->fromQuery('edit', null);
        if ($editId) {
            $id = (int)$editId;
            foreach ($_SESSION['adm_filmes'] as $f) {
                if ((int)$f['id'] === $id) {
                    $editing = $f;
                    break;
                }
            }
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            $action = $post['action'] ?? 'add';

            $data = [
                'nome' => trim((string)($post['nome'] ?? '')),
                'diretor' => trim((string)($post['diretor'] ?? '')),
                'atoresPrincipais' => trim((string)($post['atoresPrincipais'] ?? '')),
                'streaming' => array_filter(array_map('trim', explode(',', (string)($post['streaming'] ?? '')))),
                'anoLancamento' => (int)($post['ano'] ?? 0),
                'capaPrincipal' => trim((string)($post['capaPrincipal'] ?? '')),
                'capaFundo' => trim((string)($post['capaFundo'] ?? '')),
                'trailer' => trim((string)($post['trailer'] ?? '')),
                'sinopse' => trim((string)($post['sinopse'] ?? '')),
            ];

            if ($action === 'add') {
                $maxId = 0;
                foreach ($_SESSION['adm_filmes'] as $f) { $maxId = max($maxId, (int)$f['id']); }
                $data['id'] = $maxId + 1;
                $_SESSION['adm_filmes'][] = $data;
            } elseif ($action === 'update') {
                $id = (int)($post['id'] ?? 0);
                foreach ($_SESSION['adm_filmes'] as $k => $f) {
                    if ((int)$f['id'] === $id) {
                        $data['id'] = $id;
                        $_SESSION['adm_filmes'][$k] = $data;
                        break;
                    }
                }
            }

            // PRG
            return $this->redirect()->toUrl($this->getRequest()->getUriString());
        }

        return new ViewModel([
            'filmes' => $_SESSION['adm_filmes'],
            'editing' => $editing,
        ]);
    }
}
