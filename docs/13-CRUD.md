# 📊 13 - CRUD Operations

## O que é CRUD?

CRUD são as 4 operações básicas de banco de dados:

| Operação   | Descrição                    | SQL    | HTTP Method |
| ---------- | ---------------------------- | ------ | ----------- |
| **C**reate | Criar novo registro          | INSERT | POST        |
| **R**ead   | Ler/listar registros         | SELECT | GET         |
| **U**pdate | Modificar registro existente | UPDATE | POST        |
| **D**elete | Remover registro             | DELETE | GET/POST    |

---

## CREATE (Criar)

### Forma: POST /adm (action=add)

**Pré-requisitos:**

- Estar logado como admin
- Ter acesso ao painel (`/adm`)

**Passos:**

1. Preencha os campos do formulário
2. Clique **"Adicionar"**
3. Sistema cria novo registro no banco

**Validações:**

- Nome é obrigatório
- Se houver erro, exibe mensagem

**Código (FilmesController):**

```php
if ($request->isPost()) {
    $post = $request->getPost();
    $action = $post['action'] ?? 'add';

    if ($action === 'add') {
        // Validar nome
        $nome = trim((string)($post['nome'] ?? ''));
        if (empty($nome)) {
            return new ViewModel([
                'error' => 'Nome do filme é obrigatório.'
            ]);
        }

        // Criar entidade
        $filme = new Filme();
        $filme->setNome($nome);
        $filme->setDiretor(trim((string)($post['diretor'] ?? '')));
        $filme->setElenco(trim((string)($post['atoresPrincipais'] ?? '')));
        // ... outros campos

        // Persistir
        $em->persist($filme);
        $em->flush();

        // PRG: redireciona
        return $this->redirect()->toRoute('filmes');
    }
}
```

**Exemplo Completo:**

```
Formulário preenchido:
├─ nome: "Novo Filme"
├─ diretor: "Diretor Nome"
├─ ano: 2024
└─ nota: 8.5

Após POST:
├─ Valida nome ✓
├─ Cria Filme()
├─ Seta todos os campos
├─ em->persist()
├─ em->flush() → INSERT INTO filmes (...)
└─ redirect → reload página com novo filme na lista
```

---

## READ (Ler)

### Forma 1: Listar Todos (GET /adm)

**Função:** Exibir todos os filmes em uma lista

**Código:**

```php
$filmes = $repo->findAll();

return new ViewModel([
    'filmes' => $filmes,
    'editing' => null,
]);
```

**View (Loop):**

```php
<?php foreach ($this->filmes as $f): ?>
    <li class="filme-item">
        <strong><?= $this->escapeHtml($f->getNome()) ?></strong>
        <div><?= $this->escapeHtml($f->getDiretor()) ?> - <?= $f->getAnoLancamento() ?></div>
        <a href="?edit=<?= $f->getId() ?>">Editar</a>
        <a href="?delete=<?= $f->getId() ?>">Remover</a>
    </li>
<?php endforeach; ?>
```

---

### Forma 2: Buscar Um (GET /adm?edit=5)

**Função:** Carregar um filme específico para edição

**URL Query Param:**

```
/adm?edit=5  → Carrega filme com ID=5
```

**Código:**

```php
$editId = $this->params()->fromQuery('edit', null);
$editing = null;

if ($editId) {
    $editing = $repo->find((int)$editId);  // SELECT * FROM filmes WHERE id=5
}

return new ViewModel([
    'filmes' => $repo->findAll(),
    'editing' => $editing,  // Dados para preencher formulário
]);
```

**View:**

```php
<?php if ($this->editing): ?>
    <input name="id" value="<?= $this->editing->getId() ?>">
    <input name="nome" value="<?= $this->escapeHtmlAttr($this->editing->getNome()) ?>">
<?php endif; ?>
```

---

### Forma 3: Buscar Customizado

Exemplo de busca por email (User entity):

```php
$repo = $em->getRepository(User::class);
$usuario = $repo->findOneBy(['email' => 'admin@filmes.local']);

// Ou query customizada
$query = $em->createQuery('
    SELECT f FROM Filme f
    WHERE f.ano >= :ano
    ORDER BY f.nota DESC
');
$query->setParameter('ano', 2020);
$resultados = $query->getResult();
```

---

## UPDATE (Atualizar)

### Forma: POST /adm (action=update)

**Pré-requisitos:**

- Filme deve existir (via ?edit=ID)
- Estar logado como admin

**Passos:**

1. Clique "Editar" em um filme
2. Modifique os campos
3. Clique **"Atualizar"**
4. Sistema atualiza registro no banco

**Código:**

```php
if ($action === 'update') {
    $id = (int)($post['id'] ?? 0);
    $filme = $repo->find($id);  // SELECT * FROM filmes WHERE id=ID

    if ($filme) {
        // Atualizar campos
        $filme->setNome(trim((string)($post['nome'] ?? '')));
        $filme->setDiretor(trim((string)($post['diretor'] ?? '')));
        $filme->setElenco(trim((string)($post['atoresPrincipais'] ?? '')));
        $filme->setStreaming(trim((string)($post['streaming'] ?? '')));
        // ... outros setters

        // Salvar
        $em->flush();  // UPDATE filmes SET ... WHERE id=ID
    }
}

return $this->redirect()->toRoute('filmes');  // Reload
```

**Fluxo Completo:**

```
1. GET /adm?edit=5
   └─ Carrega filme #5 em editing

2. Usuário modifica nome: "Antigo" → "Novo"

3. POST /adm
   ├─ action=update
   ├─ id=5
   └─ nome="Novo"

4. FilmesController processa:
   ├─ $filme = $repo->find(5)
   ├─ $filme->setNome("Novo")
   └─ $em->flush()

5. Database:
   └─ UPDATE filmes SET nome='Novo' WHERE id=5

6. Redirect:
   └─ GET /adm (recarrega com novo nome)
```

---

## DELETE (Remover)

### Forma: GET /adm?delete=5

**Pré-requisitos:**

- Filme deve existir
- Estar logado como admin
- Confirmar exclusão (popup)

**Passos:**

1. Clique "Remover" em um filme
2. Confirme exclusão (popup "Remover?")
3. Filme removido do banco

**Código HTML (View):**

```html
<a href="?delete=<?= $f->getId() ?>" onclick="return confirm('Remover?');">
  Remover
</a>
```

**Código PHP (Controller):**

```php
$deleteId = $this->params()->fromQuery('delete', null);

if ($deleteId) {
    $filme = $repo->find((int)$deleteId);  // SELECT * FROM filmes WHERE id=?

    if ($filme) {
        $em->remove($filme);  // Mark for removal
        $em->flush();         // DELETE FROM filmes WHERE id=?
    }

    // Redireciona para recarregar lista
    return $this->redirect()->toRoute('filmes');
}
```

**Fluxo Completo:**

```
1. Usuário clica "Remover" (link com ?delete=5)

2. JavaScript executa:
   └─ confirm('Remover?') → true/false

3. Se confirmar:
   └─ Vai para /adm?delete=5

4. FilmesController processa:
   ├─ $filme = $repo->find(5)
   ├─ $em->remove($filme)
   └─ $em->flush()

5. Database:
   └─ DELETE FROM filmes WHERE id=5

6. Redirect:
   └─ GET /adm (lista sem o filme removido)
```

**Confirmação JavaScript:**

```javascript
onclick = "return confirm('Remover?');";
// true → segue link
// false → cancela operação
```

---

## Entidade Filme

**Arquivo:** `module/Application/src/Entity/Filme.php`

### Atributos

```php
#[Entity, Table(name: 'filmes')]
class Filme {
    #[Id, Column(type: 'integer'), GeneratedValue]
    private ?int $id = null;

    #[Column(type: 'string', length: 255)]
    private ?string $nome = null;

    #[Column(type: 'text')]
    private ?string $sinopse = null;

    #[Column(type: 'string', length: 500)]
    private ?string $capaPrincipal = null;

    #[Column(type: 'string', length: 500)]
    private ?string $capaFundo = null;

    #[Column(type: 'integer')]
    private ?int $anoLancamento = null;

    #[Column(type: 'string', length: 255)]
    private ?string $diretor = null;

    #[Column(type: 'text')]
    private ?string $elenco = null;

    #[Column(type: 'string', length: 255)]
    private ?string $genero = null;

    #[Column(type: 'float')]
    private ?float $nota = null;

    #[Column(type: 'string', length: 500)]
    private ?string $trailer = null;

    #[Column(type: 'string', length: 255)]
    private ?string $streaming = null;
}
```

### Getters & Setters

```php
public function getId(): ?int { return $this->id; }
public function getNome(): ?string { return $this->nome; }
public function setNome(?string $nome): self { ... }
// ... outros
```

---

## Repository Pattern

Doctrine fornece um Repository para queries:

```php
$repo = $em->getRepository(Filme::class);

// Buscar tudo
$filmes = $repo->findAll();

// Buscar um por ID
$filme = $repo->find(5);

// Buscar por campo específico
$filme = $repo->findOneBy(['nome' => 'Inception']);

// Buscar múltiplos
$filmes = $repo->findBy(['genero' => 'Ação']);

// Query customizada
$query = $em->createQuery('SELECT f FROM Filme f WHERE f.nota >= 8');
$resultados = $query->getResult();
```

---

## Padrão POST-Redirect-GET

Usado em todas as operações de escrita:

```
POST (write)
    ↓
Processa dados
    ↓
Redireciona (GET)
    ↓
Recarrega página (read)
```

**Benefícios:**

- Evita resubmissão de formulário
- Histórico navegador limpo
- Página recarrega com dados atualizados

---

## Transações (Futuro)

Se precisar múltiplas operações atômicas:

```php
$em->beginTransaction();
try {
    $filme1->setNota(8.5);
    $filme2->setNota(7.0);
    $em->flush();
    $em->commit();
} catch (\Exception $e) {
    $em->rollback();
    throw $e;
}
```

---

## Testes de CRUD

### Create

```bash
✅ Adicionar filme com nome válido
❌ Adicionar sem nome (erro esperado)
```

### Read

```bash
✅ Listar todos filmes
✅ Buscar filme específico (?edit=5)
```

### Update

```bash
✅ Editar nome de filme
✅ Editar múltiplos campos
```

### Delete

```bash
✅ Remover filme
✅ Confirmar antes de remover
```

---

Próximo: [PAINEL ADMIN](./12-PAINEL-ADMIN.md)
