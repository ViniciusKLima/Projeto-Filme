# 🎬 12 - Painel de Admin

## Visão Geral

O painel de admin (`/adm`) é a interface para gerenciar filmes no banco de dados.

**URL:** http://localhost/adm  
**Proteção:** Apenas usuários logados como `admin`  
**Controller:** `FilmesController`  
**View:** `filmes/index.phtml`  
**Funcionalidades:** Add, Edit, Delete, List

---

## Acessar o Painel

### 1. Fazer Login

```
URL: http://localhost/auth/login
Email: admin@filmes.local
Senha: Admin@123456
```

### 2. Será Redirecionado para `/adm`

```
URL: http://localhost/adm
✅ Painel aberto
```

Se não fizer login como admin:

```
❌ Redirecionado para /auth/login
```

---

## Interface do Painel

```
┌─────────────────────────────────────────────────┐
│              PAINEL DE ADMINISTRAÇÃO              │
│         Gerenciar filmes (Banco de Dados)        │
└─────────────────────────────────────────────────┘

┌─────────────────────┬───────────────────────────┐
│                     │                           │
│   FORMULÁRIO        │    LISTA DE FILMES        │
│   (Lado esquerdo)   │    (Lado direito)         │
│                     │                           │
│  Nome*              │  #1 Inception             │
│  Diretor            │     Christopher Nolan     │
│  Elenco             │     2010                  │
│  Streaming          │     [Editar] [Remover]    │
│  Ano                │                           │
│  Capa Principal     │  #2 The Dark Knight       │
│  Capa Fundo         │     ...                   │
│  Trailer            │                           │
│  Gênero             │  #3 Matrix                │
│  Nota               │     ...                   │
│  Sinopse            │                           │
│                     │                           │
│  [Adicionar] ou     │                           │
│  [Atualizar]        │                           │
│                     │                           │
└─────────────────────┴───────────────────────────┘
```

---

## Operações CRUD

### 1. CREATE (Adicionar Filme)

**Forma:**

1. Preencha os campos no formulário (esquerda)
2. Clique em **"Adicionar"**
3. Filme salvo no banco

**Campos:**

- `nome` _(obrigatório)_ - Nome do filme
- `diretor` - Nome do diretor
- `atoresPrincipais` - Elenco (atores separados por vírgula)
- `streaming` - Plataforma (Netflix, Prime, etc)
- `ano` - Ano de lançamento
- `capaPrincipal` - URL da capa/poster
- `capaFundo` - URL da imagem de fundo
- `trailer` - URL do trailer (YouTube)
- `genero` - Gênero (Ação, Drama, etc)
- `nota` - Avaliação (0-5)
- `sinopse` - Descrição do filme

**Validação:**

- Nome é obrigatório
- Se vazio, exibe erro "Nome do filme é obrigatório"

**Código (FilmesController):**

```php
if ($action === 'add') {
    $filme = new Filme();
    $filme->setNome($nome);
    $filme->setDiretor($diretor);
    // ... setters para outros campos
    $em->persist($filme);
    $em->flush();
}
```

---

### 2. READ (Listar & Editar)

**Listar:**

- Painel carrega todos os filmes do banco automaticamente
- Mostra em uma tabela/lista na direita

**Editar:**

1. Clique em **"Editar"** no filme desejado
2. Dados carregam no formulário
3. Modifique o que quiser
4. Clique em **"Atualizar"**
5. Filme salvo no banco

**URL:**

```
GET /adm?edit=5
```

**Código (FilmesController):**

```php
$editId = $this->params()->fromQuery('edit', null);
if ($editId) {
    $editing = $repo->find((int)$editId);
}
```

---

### 3. UPDATE (Atualizar)

**Forma:**

1. Clique em "Editar" no filme
2. Formulário carrega com dados atuais
3. Modifique campos
4. Clique em **"Atualizar"**

**Código:**

```php
if ($action === 'update') {
    $id = (int)($post['id'] ?? 0);
    $filme = $repo->find($id);
    if ($filme) {
        $filme->setNome($nome);
        $filme->setDiretor($diretor);
        // ... outros campos
        $em->flush();  // Persiste mudanças
    }
}
```

---

### 4. DELETE (Remover)

**Forma:**

1. Clique em **"Remover"** no filme
2. Confirme a exclusão (popup)
3. Filme removido do banco

**URL:**

```
GET /adm?delete=5
```

**Confirmação:**

```javascript
onclick = "return confirm('Remover?')";
```

**Código:**

```php
$deleteId = $this->params()->fromQuery('delete', null);
if ($deleteId) {
    $filme = $repo->find((int)$deleteId);
    if ($filme) {
        $em->remove($filme);
        $em->flush();
    }
    return $this->redirect()->toRoute('filmes');  // Recarrega
}
```

---

## POST-Redirect-GET (PRG) Pattern

Após cada operação, o painel redireciona para `/adm`:

```
1. Usuário submete POST (add/update)
   ↓
2. Servidor processa
   ↓
3. redirect()->toRoute('filmes')  // GET /adm
   ↓
4. Página recarrega com dados atualizados
   ↓
5. Histório do navegador não duplica POST
```

**Benefício:** Evita "Resend form data?" ao recarregar.

---

## FilmesController

**Localização:** `module/Application/src/Controller/FilmesController.php`

### Método: requireAdmin()

Protege acesso ao painel:

```php
private function requireAdmin() {
    $this->ensureSession();
    if (empty($_SESSION['user']) ||
        ($_SESSION['user']['tipo'] ?? '') !== 'admin') {
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
    return null;
}
```

### Método: indexAction()

Ação principal do painel:

```php
public function indexAction() {
    // 1. Verifica se é admin
    $adminCheck = $this->requireAdmin();
    if ($adminCheck) return $adminCheck;

    $em = $this->getEntityManager();
    $repo = $em->getRepository(Filme::class);

    // 2. Processa DELETE (?delete=5)
    $deleteId = $this->params()->fromQuery('delete');
    if ($deleteId) { /* remover */ }

    // 3. Carrega para EDIT (?edit=5)
    $editId = $this->params()->fromQuery('edit');
    $editing = null;
    if ($editId) {
        $editing = $repo->find((int)$editId);
    }

    // 4. Processa POST (add/update)
    if ($request->isPost()) {
        $action = $post['action'];
        if ($action === 'add') { /* criar */ }
        elseif ($action === 'update') { /* atualizar */ }
        return $this->redirect()->toRoute('filmes');
    }

    // 5. Retorna dados para view
    return new ViewModel([
        'filmes' => $repo->findAll(),
        'editing' => $editing,
        'error' => null
    ]);
}
```

---

## Template (View)

**Localização:** `module/Application/view/application/filmes/index.phtml`

**Responsabilidades:**

- Exibir formulário de add/edit
- Listar filmes
- Chamar métodos da entidade (getNome, getId, etc)
- Escaper HTML para XSS protection

**Exemplo:**

```php
<!-- Formulário -->
<form method="post">
    <input name="action" value="<?= ($this->editing ? 'update' : 'add') ?>">
    <input name="nome" value="<?= $this->escapeHtmlAttr($this->editing->getNome()) ?>">
    <button type="submit">
        <?= ($this->editing ? 'Atualizar' : 'Adicionar') ?>
    </button>
</form>

<!-- Lista -->
<?php foreach ($this->filmes as $f): ?>
    <li>
        <strong><?= $this->escapeHtml($f->getNome()) ?></strong>
        <a href="?edit=<?= $f->getId() ?>">Editar</a>
        <a href="?delete=<?= $f->getId() ?>" onclick="return confirm('Remover?')">Remover</a>
    </li>
<?php endforeach; ?>
```

---

## Validações Implementadas

| Campo              | Validação                 | Local            |
| ------------------ | ------------------------- | ---------------- |
| `nome`             | Obrigatório, não-vazio    | FilmesController |
| `ano`              | Convertido para int       | FilmesController |
| `nota`             | Convertido para float     | FilmesController |
| `id` (edit/delete) | Validado no banco         | FilmesController |
| HTML output        | Escapado com escapeHtml() | View (PHTML)     |

---

## Testes

### Teste 1: Adicionar Filme

```
1. Acesse /adm (logado como admin)
2. Preencha nome: "Novo Filme"
3. Clique "Adicionar"
4. ✅ Filme aparece na lista
5. ✅ Salvo no banco de dados
```

### Teste 2: Editar Filme

```
1. Clique "Editar" em um filme
2. Modifique o nome
3. Clique "Atualizar"
4. ✅ Lista recarrega com novo nome
```

### Teste 3: Deletar Filme

```
1. Clique "Remover" em um filme
2. Confirme exclusão
3. ✅ Filme desaparece da lista
4. ✅ Removido do banco
```

### Teste 4: Proteção

```
1. Deslogue (/auth/logout)
2. Tente acessar /adm
3. ✅ Redirecionado para /auth/login
```

---

## Importar Filmes em Lote

Ao invés de adicionar um por um, importe um JSON:

```bash
php bin/seed-filmes.php filmes.json
```

Veja [SEED-DADOS](./10-SEED-DADOS.md) para mais detalhes.

---

## Boas Práticas

✅ **Faça:**

- Sempre validar entrada (not empty, type)
- Escapar output HTML (escapeHtml, escapeHtmlAttr)
- Usar prepared statements (Doctrine faz isso)
- Proteger rotas com requireAdmin()

❌ **Evite:**

- Inserir dados não validados
- Exibir output sem escaper (XSS)
- Confiar em $_GET/$\_POST direto
- Expor IDs sensíveis em URLs públicas

---

Próximo: [CRUD OPERATIONS](./13-CRUD.md)
