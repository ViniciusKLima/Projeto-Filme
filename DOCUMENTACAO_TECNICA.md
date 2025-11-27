# 🔧 Documentação Técnica - Painel de Admin

## Arquitetura

```
┌─────────────────────────────────────────────────────────┐
│                    CAMADA DE APRESENTAÇÃO                │
│                     (View - PHTML)                        │
│  module/Application/view/application/filmes/index.phtml  │
└──────────────────────────┬────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    CAMADA DE CONTROLE                     │
│                   (Controller)                             │
│  module/Application/src/Controller/FilmesController.php   │
│  ├─ requireAdmin()    → Valida sessão admin              │
│  ├─ indexAction()     → Lista filmes + formulário        │
│  └─ POST/GET handlers → Add/Update/Delete filmes        │
└──────────────────────────┬────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    CAMADA DE DADOS                        │
│                  (Doctrine ORM)                           │
│  module/Application/src/Entity/Filme.php                 │
│  └─ Mapeia para tabela `filmes` no banco de dados        │
└──────────────────────────┬────────────────────────────────┘
                           │
                           ▼
                    MySQL Database
                    (projeto_filmes)
```

---

## Fluxo de Autenticação

```
1. Usuário acessa /auth/login
                    ↓
2. Submete email + senha
                    ↓
3. AuthController::authenticateAction()
   ├─ Valida credenciais no banco
   └─ Se válido, cria SESSION['user']
                    ↓
4. Verifica tipoConta
   ├─ Se 'admin' → redirect para /adm
   └─ Se 'cliente' → redirect para /home
                    ↓
5. Acessa /adm
   ├─ FilmesController::indexAction()
   └─ Chama requireAdmin()
      ├─ Verifica $_SESSION['user']['tipo'] === 'admin'
      └─ Se válido → exibe painel
         Se inválido → redirect para /auth/login
```

---

## Estrutura de SESSION

```php
$_SESSION['user'] = [
    'id'    => 1,                    // ID do usuário
    'nome'  => 'Administrador',      // Nome completo
    'email' => 'admin@filmes.local', // Email
    'tipo'  => 'admin'               // Tipo de conta: 'admin' ou 'cliente'
];
```

---

## Operações CRUD do FilmesController

### CREATE (Adicionar)

```
GET  /adm
     ↓
Exibe formulário em branco
     ↓
POST /adm (action=add)
     ↓
Valida nome (obrigatório)
     ↓
new Filme()
em->persist($filme)
em->flush()
     ↓
redirect /adm
```

### READ (Listar/Editar)

```
GET  /adm?edit=5
     ↓
$repo->find(5)
     ↓
Carrega dados no formulário
```

### UPDATE (Atualizar)

```
POST /adm (action=update, id=5)
     ↓
Valida campos
     ↓
$repo->find(5)
     ↓
film->setNome(), setDiretor(), ...
em->flush()
     ↓
redirect /adm
```

### DELETE (Remover)

```
GET  /adm?delete=5
     ↓
$repo->find(5)
     ↓
em->remove($filme)
em->flush()
     ↓
redirect /adm
```

---

## Scripts de Seed

### seed-filmes.php

**Localização:** `bin/seed-filmes.php`

**Uso:**

```bash
php bin/seed-filmes.php <arquivo.json>
```

**Lógica:**

```
1. Verifica se arquivo existe
2. Decodifica JSON
3. Valida se é array não-vazio
4. Conecta ao banco via Doctrine
5. Para cada filme:
   ├─ Valida campo 'nome'
   ├─ Cria nova entidade Filme
   ├─ Seta todos os campos
   └─ persist($filme)
6. Faz flush() único (batch insert)
7. Exibe relatório
```

**Tratamento de Erros:**

- Se arquivo não existe → exibe erro e sai
- Se JSON inválido → exibe erro JSON
- Se filme sem 'nome' → pula e continua
- Se banco indisponível → exibe erro de conexão

---

### create-admin.php

**Localização:** `bin/create-admin.php`

**Uso:**

```bash
php bin/create-admin.php
```

**Lógica:**

```
1. Conecta ao banco
2. Procura por 'admin@filmes.local'
3. Se existe → exibe info e sai
4. Se não existe:
   ├─ Cria novo User
   ├─ setNome('Administrador')
   ├─ setEmail('admin@filmes.local')
   ├─ setSenha(password_hash('Admin@123456', ...))
   ├─ setTipoConta('admin')
   └─ Salva no banco
```

---

## Rota de Proteção

**Arquivo:** `module/Application/config/module.config.php`

```php
'filmes' => [
    'type' => Literal::class,
    'options' => [
        'route' => '/adm',
        'defaults' => [
            'controller' => FilmesController::class,
            'action' => 'index',
        ],
    ],
],
```

**Proteção em Runtime:**

```php
private function requireAdmin()
{
    $this->ensureSession();
    if (empty($_SESSION['user']) ||
        ($_SESSION['user']['tipo'] ?? '') !== 'admin') {
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
    return null;
}

public function indexAction()
{
    $adminCheck = $this->requireAdmin();
    if ($adminCheck) return $adminCheck;  // ← Bloqueia se não for admin
    // ... resto da lógica
}
```

---

## Entidade Filme

**Localização:** `module/Application/src/Entity/Filme.php`

**Campos:**

```php
#[Id, Column(type: "integer")]
#[GeneratedValue]
private ?int $id = null;

#[Column(type: "string", length: 255)]
private ?string $nome = null;

#[Column(type: "text")]
private ?string $sinopse = null;

#[Column(type: "string", length: 500)]
private ?string $capaPrincipal = null;

#[Column(type: "string", length: 500)]
private ?string $capaFundo = null;

#[Column(type: "integer")]
private ?int $anoLancamento = null;

#[Column(type: "string", length: 255)]
private ?string $diretor = null;

#[Column(type: "text")]
private ?string $elenco = null;

#[Column(type: "string", length: 255)]
private ?string $genero = null;

#[Column(type: "float")]
private ?float $nota = null;

#[Column(type: "string", length: 500)]
private ?string $trailer = null;

#[Column(type: "string", length: 255)]
private ?string $streaming = null;
```

---

## Entidade User

**Localização:** `module/Application/src/Entity/User.php`

**Campos Relevantes:**

```php
#[Id, Column(type: "integer")]
#[GeneratedValue]
private ?int $id = null;

#[Column(type: "string", length: 255)]
private ?string $nome = null;

#[Column(type: "string", length: 255, unique: true)]
private ?string $email = null;

#[Column(type: "string", length: 255)]
private ?string $senha = null;

#[Column(type: "string", length: 50)]  // 'admin' ou 'cliente'
private ?string $tipoConta = null;

#[Column(type: "string", length: 255, nullable: true)]
private ?string $rememberToken = null;
```

---

## Fluxo de POST (Adicionar/Atualizar Filme)

```
User submete <form method="post">
       ↓
POST /adm
       ↓
FilmesController::indexAction()
  ├─ requireAdmin() ← Valida admin
  ├─ $request->isPost() ← True
  ├─ $post = $request->getPost()
  ├─ $action = $post['action']
  │
  ├─ if ($action === 'add')
  │  ├─ new Filme()
  │  ├─ setNome(), setDiretor(), ...
  │  ├─ em->persist()
  │  └─ em->flush()
  │
  └─ return redirect('/adm') ← PRG Pattern
```

---

## Validações Implementadas

| Campo          | Validação                        | Arquivo          |
| -------------- | -------------------------------- | ---------------- |
| `nome`         | Obrigatório, não-vazio           | FilmesController |
| `email` (User) | Única no banco                   | AuthController   |
| `tipoConta`    | Apenas 'admin' pode acessar /adm | FilmesController |
| JSON (seed)    | Deve ser array válido            | seed-filmes.php  |
| Filme (seed)   | Campo 'nome' obrigatório         | seed-filmes.php  |

---

## Mensagens de Erro

| Situação                   | Mensagem                              | Redirecionamento   |
| -------------------------- | ------------------------------------- | ------------------ |
| Não logado                 | Nenhuma exibida                       | `/auth/login`      |
| Logado como cliente        | Nenhuma exibida                       | `/auth/login`      |
| Nome do filme vazio        | "Nome do filme é obrigatório"         | `/adm` (permanece) |
| JSON inválido              | "JSON deve ser um array não-vazio"    | CLI (exit 1)       |
| Email duplicado (register) | "Já existe um usuário com esse email" | `/auth/cadastro`   |

---

## Ambiente

**Framework:** Laminas 3.x  
**ORM:** Doctrine 3.x (Attribute Mapping)  
**Banco:** MySQL  
**PHP:** 7.4+  
**Session:** Native PHP $\_SESSION

---

## Cache e Performance

- Sem cache atualmente (dev mode)
- Todas as queries ao vivo
- Sem pagination na lista de filmes
- Bulk insert otimizado no seed (flush uma vez ao final)

---

Documentação completa! 📚
