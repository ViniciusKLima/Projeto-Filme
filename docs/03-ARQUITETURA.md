# 🏗️ 03 - Arquitetura Geral

## Padrão MVC (Model-View-Controller)

LocMovies segue o padrão **MVC** implementado pelo framework Laminas:

```
┌─────────────────────────────────────────────────────┐
│           USER (Navegador/Cliente)                   │
└────────────────────┬────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────┐
│              ROUTER (url.com/filme/1)               │
│  module/Application/config/module.config.php       │
└────────────────────┬────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────┐
│           CONTROLLER (Handle Request)                │
│  module/Application/src/Controller/                 │
│                                                      │
│  ├─ HomeController (home)                           │
│  ├─ AuthController (login/register)                 │
│  ├─ DetalhesFilmeController (filme details)         │
│  └─ FilmesController (admin CRUD)                   │
└────────────────────┬────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        ▼                         ▼
┌──────────────────┐    ┌─────────────────────┐
│ MODEL            │    │ DATABASE            │
│ (Entities)       │    │ (MySQL/MariaDB)     │
│                  │    │                     │
│ ├─ User          │    │ ├─ users table      │
│ └─ Filme         │    │ └─ filmes table     │
│                  │    │                     │
│ Doctrine ORM     │    │ Doctrine persists   │
│ (3.x Attributes) │    │ data via DBAL       │
└──────────────────┘    └─────────────────────┘
        │
        └────────────┬────────────┐
                     ▼
┌─────────────────────────────────────────────────────┐
│  VIEW (Render HTML)                                  │
│  module/Application/view/                           │
│                                                      │
│  ├─ layout/layout.phtml (Master Layout)             │
│  │  ├─ components/header.phtml                      │
│  │  ├─ {content} (dinâmico)                         │
│  │  └─ components/footer.phtml                      │
│  ├─ application/home/index.phtml                    │
│  ├─ application/login/*.phtml                       │
│  ├─ application/filme/detalhesFilme.phtml           │
│  └─ application/filmes/index.phtml (admin)          │
└────────────────────┬────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────┐
│         HTML + CSS + JavaScript (Response)           │
│         (Enviado ao navegador)                       │
└─────────────────────────────────────────────────────┘
```

---

## Componentes Principais

### 1. **Router (Roteador)**

- **Arquivo:** `module/Application/config/module.config.php`
- **Função:** Mapear URLs para Controllers/Actions
- **Exemplo:**

```php
'filmes' => [
    'type' => Literal::class,
    'options' => [
        'route' => '/adm',  // URL
        'defaults' => [
            'controller' => FilmesController::class,  // Qual controller
            'action' => 'index',                       // Qual action
        ],
    ],
],
```

### 2. **Controller (Controlador)**

- **Localização:** `module/Application/src/Controller/`
- **Função:** Receber requisição, processar lógica, chamar model e view
- **Exemplo:**

```php
class HomeController extends AbstractActionController {
    public function indexAction() {
        $filmes = $this->getEntityManager()
            ->getRepository(Filme::class)
            ->findAll();

        return new ViewModel(['filmes' => $filmes]);
    }
}
```

### 3. **Model (Modelo)**

- **Localização:** `module/Application/src/Entity/`
- **Tecnologia:** Doctrine ORM com Attributes
- **Função:** Representar dados e persistência
- **Exemplo:**

```php
#[Entity, Table(name: 'filmes')]
class Filme {
    #[Id, Column(type: 'integer'), GeneratedValue]
    private ?int $id = null;

    #[Column(type: 'string', length: 255)]
    private ?string $nome = null;
}
```

### 4. **View (Visualização)**

- **Localização:** `module/Application/view/`
- **Tecnologia:** PHTML (PHP Templates)
- **Função:** Renderizar HTML com dados do controller
- **Exemplo:**

```php
<h1><?= $this->escapeHtml($filme->getNome()) ?></h1>
<p><?= $this->escapeHtml($filme->getSinopse()) ?></p>
```

### 5. **Service Manager (Contêiner DI)**

- **Arquivo:** `config/container.php`
- **Função:** Injetar dependências (EntityManager, etc)
- **Exemplo:**

```php
// No controller
$em = $this->getEvent()->getApplication()->getServiceManager()
    ->get(\Doctrine\ORM\EntityManager::class);
```

---

## Fluxo de Requisição Detalhado

### Exemplo: Acessar `/adm` como admin

```
1. GET /adm
   └─ Browser envia requisição HTTP

2. public/index.php
   └─ Inicializa aplicação Laminas

3. Router (module.config.php)
   └─ Mapeia /adm → FilmesController::indexAction()

4. FilmesController::indexAction()
   ├─ Chama requireAdmin()
   │  └─ Verifica $_SESSION['user']['tipo'] === 'admin'
   ├─ Se não admin → redireciona para /auth/login
   ├─ Se admin:
   │  ├─ Obtém EntityManager via Service Manager
   │  ├─ Busca filmes: Filme::findAll()
   │  └─ Retorna ViewModel com dados

5. View (filmes/index.phtml)
   ├─ Recebe ViewModel com ['filmes' => [...]]
   ├─ Itera sobre filmes
   └─ Renderiza HTML com dados

6. layout.phtml
   ├─ Header (components/header.phtml)
   ├─ Conteúdo (filmes/index.phtml)
   ├─ Footer (components/footer.phtml)
   └─ CSS/JS

7. Response HTTP
   └─ HTML completo enviado ao navegador
```

---

## Camadas de Abstração

### 1. **Apresentação (View)**

- Templates PHTML
- CSS/JavaScript
- Componentes reutilizáveis (header, footer)

### 2. **Aplicação (Controller)**

- Lógica de requisição
- Validação de entrada
- Chamadas ao model/service

### 3. **Persistência (Entity + Repository)**

- Doctrine ORM
- Mapping de atributos
- Queries ao banco

### 4. **Infraestrutura (Service Manager)**

- Injeção de dependências
- Configuração
- Pooling de recursos

---

## Diagrama de Dependências

```
public/index.php
    ↓
Laminas\Mvc\Application
    ├─ Router (config)
    ├─ Service Manager (container.php)
    │   ├─ Doctrine ORM
    │   │   ├─ EntityManager
    │   │   ├─ Entities (User, Filme)
    │   │   └─ Database Connection
    │   └─ Other Services
    ├─ Controllers
    │   ├─ HomeController
    │   ├─ AuthController
    │   ├─ DetalhesFilmeController
    │   └─ FilmesController
    └─ View Manager
        ├─ Templates
        ├─ Helpers (escapeHtml, basePath)
        └─ Layout
```

---

## Padrões de Resposta

### ViewModel (Retorna dados para view)

```php
return new ViewModel([
    'filmes' => $filmes,
    'total' => count($filmes),
]);
```

### Redirect (Redireciona para outra rota)

```php
return $this->redirect()->toRoute('home');
```

### JsonModel (Retorna JSON)

```php
return new JsonModel(['status' => 'success']);
```

---

## Fluxo de Autenticação

```
1. Usuario vai em /auth/login
   ↓
2. Preenche email + senha
   ↓
3. POST /auth/authenticate
   ↓
4. AuthController::authenticateAction()
   ├─ Valida credenciais no banco (User::findOneBy)
   ├─ Verifica senha (password_verify)
   ├─ Cria SESSION['user']
   └─ Redireciona conforme tipoConta
       ├─ admin → /adm
       └─ cliente → /home
   ↓
5. Próximas requisições carregam user from SESSION
```

---

## Proteção de Rota

Implementado em controllers:

```php
private function requireAdmin() {
    if (empty($_SESSION['user']) ||
        $_SESSION['user']['tipo'] !== 'admin') {
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
    return null;
}

public function indexAction() {
    $check = $this->requireAdmin();
    if ($check) return $check;  // Bloqueia acesso

    // Continua apenas se admin
}
```

---

## Resumo das Responsabilidades

| Componente          | Responsabilidade                 |
| ------------------- | -------------------------------- |
| **Router**          | Mapear URLs para Controllers     |
| **Controller**      | Processar lógica e orquestrar    |
| **Entity**          | Representar dados + persistência |
| **Repository**      | Consultar dados do banco         |
| **View**            | Renderizar HTML                  |
| **Service Manager** | Gerenciar dependências           |

---

Próximo: [ROUTING](./04-ROUTING.md)
