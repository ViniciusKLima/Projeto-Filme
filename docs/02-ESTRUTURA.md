# 📁 02 - Estrutura do Projeto

```
c:\projetoFilmes\
├── bin/
│   ├── clear-config-cache.php      ← Limpa cache de configuração
│   ├── create-admin.php            ← Cria usuário admin
│   ├── create_schema_local.php     ← Cria tabelas do banco
│   └── seed-filmes.php             ← Importa filmes em lote
│
├── config/
│   ├── application.config.php      ← Configuração principal
│   ├── container.php               ← Configuração DI Container
│   ├── modules.config.php          ← Módulos habilitados
│   └── autoload/
│       ├── doctrine.local.php      ← Config Doctrine (credenciais)
│       ├── development.local.php   ← Config desenvolvimento
│       └── global.php              ← Config global
│
├── module/Application/
│   ├── config/
│   │   └── module.config.php       ← Rotas, controllers, views
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── AuthController.php     ← Login/Registro
│   │   │   ├── HomeController.php     ← Home page
│   │   │   ├── DetalhesFilmeController.php ← Detalhes do filme
│   │   │   └── FilmesController.php   ← Painel admin CRUD
│   │   ├── Entity/
│   │   │   ├── User.php            ← Entidade User
│   │   │   └── Filme.php           ← Entidade Filme
│   │   └── Module.php              ← Inicialização do módulo
│   ├── view/
│   │   ├── layout/
│   │   │   └── layout.phtml        ← Layout principal (header + footer)
│   │   ├── application/
│   │   │   ├── home/
│   │   │   │   └── index.phtml     ← Home page
│   │   │   ├── login/
│   │   │   │   ├── login.phtml     ← Tela de login
│   │   │   │   └── cadastro.phtml  ← Tela de registro
│   │   │   ├── filme/
│   │   │   │   └── detalhesFilme.phtml ← Detalhes do filme
│   │   │   └── filmes/
│   │   │       └── index.phtml     ← Painel admin
│   │   └── components/
│   │       ├── header.phtml        ← Header reutilizável
│   │       └── footer.phtml        ← Footer reutilizável
│   └── test/                       ← Testes (future)
│
├── public/
│   ├── index.php                   ← Entry point
│   ├── web.config                  ← Config IIS
│   ├── css/
│   │   ├── header.css              ← Estilo header
│   │   ├── index.css               ← Estilo home
│   │   ├── login.css               ← Estilo login
│   │   ├── cadastro.css            ← Estilo cadastro
│   │   ├── detalhesFilme.css       ← Estilo detalhes
│   │   ├── adm.css                 ← Estilo painel admin
│   │   └── modal.css               ← Estilo modais
│   └── js/
│       ├── main.js                 ← JS global
│       ├── login.js                ← JS login
│       ├── cadastro.js             ← JS cadastro
│       ├── password-toggle.js      ← Toggle senha
│       └── protect-filme.js        ← Proteção e modais
│
├── vendor/                         ← Dependências (composer)
├── data/
│   └── cache/                      ← Cache de config
│
├── docs/                           ← 📚 DOCUMENTAÇÃO
│   ├── README.md                   ← Índice de docs
│   ├── 01-INICIO-RAPIDO.md         ← Como começar
│   ├── 02-ESTRUTURA.md             ← Este arquivo
│   ├── 03-ARQUITETURA.md           ← MVC e componentes
│   ├── 04-ROUTING.md               ← Sistema de rotas
│   ├── 05-PADROES.md               ← Design patterns
│   ├── 06-AUTENTICACAO.md          ← Login/Session
│   ├── 07-AUTORIZACAO.md           ← Controle de acesso
│   ├── 08-PROTECAO-ROTA.md         ← Proteção de endpoints
│   ├── 09-ENTITIES.md              ← Doctrine entities
│   ├── 10-SEED-DADOS.md            ← Import de dados
│   ├── 11-MIGRATIONS.md            ← Controle de versão DB
│   ├── 12-PAINEL-ADMIN.md          ← Painel CRUD
│   ├── 13-CRUD.md                  ← Operações CRUD
│   ├── 14-VIEWS.md                 ← Templates PHTML
│   ├── 15-COMPONENTS.md            ← Componentes
│   ├── 16-STYLING.md               ← CSS e responsivo
│   ├── 17-TROUBLESHOOTING.md       ← Problemas e soluções
│   ├── 18-DEPLOYMENT.md            ← Deploy em produção
│   └── 19-CONTRIBUINDO.md          ← Como contribuir
│
├── composer.json                   ← Dependências PHP
├── composer.lock                   ← Lock de versões
├── phpunit.xml.dist                ← Config testes
├── psalm.xml                       ← Config static analysis
├── phpcs.xml                       ← Config linting
├── create_schema.php               ← Script criar DB
├── filmes-exemplo.json             ← Dados de exemplo
├── QUICK_START.md                  ← Start rápido
├── PAINEL_ADMIN_GUIA.md            ← Guia admin
├── DOCUMENTACAO_TECNICA.md         ← Docs técnica
├── EXEMPLOS_PRATICOS.md            ← Exemplos
├── FUNCIONALIDADES_CRIADAS.md      ← Resumo features
├── README.md                       ← README principal
└── LICENSE.md                      ← Licença
```

---

## 🎯 Rotas Principais

### Públicas

- `/` → Home (lista de filmes)
- `/filme/{id}` → Detalhes do filme
- `/auth/login` → Fazer login
- `/auth/cadastro` → Criar conta
- `/auth/logout` → Sair

### Protegidas (Admin)

- `/adm` → Painel de administração (CRUD de filmes)

---

## 📂 Convenções

### Controllers

- Localização: `module/Application/src/Controller/`
- Nomeação: `{Nome}Controller.php`
- Ação: `{action}Action()` em camelCase
- Exemplo: `LoginController->loginAction()`

### Views

- Localização: `module/Application/view/application/{controller}/`
- Nomeação: `{action}.phtml`
- Exemplo: `view/application/login/login.phtml`

### Entities

- Localização: `module/Application/src/Entity/`
- Nomeação: `{NomeEntidade}.php`
- Exemplo: `Entity/User.php`, `Entity/Filme.php`

### CSS

- Localização: `public/css/`
- Nomeação: `{nome}.css`
- Responsive com `@media` queries

### JavaScript

- Localização: `public/js/`
- Nomeação: `{nome}.js`
- Vanilla JS (sem jQuery)

---

## 🔑 Arquivos Importantes

| Arquivo                                       | Propósito                           |
| --------------------------------------------- | ----------------------------------- |
| `config/application.config.php`               | Habilita módulos e config           |
| `module/Application/config/module.config.php` | Rotas, controllers, views, services |
| `module/Application/Module.php`               | Inicialização do módulo             |
| `public/index.php`                            | Entry point da aplicação            |
| `create_schema.php`                           | Cria tabelas no banco               |

---

## 🚀 Fluxo de Requisição

```
1. Usuário acessa URL
   ↓
2. public/index.php (entry point)
   ↓
3. Laminas Router (config/modules.config.php)
   ↓
4. Controller + Action (src/Controller/)
   ↓
5. View (module/Application/view/)
   ↓
6. HTML renderizado com layout.phtml
```

---

Próximo: [ARQUITETURA GERAL](./03-ARQUITETURA.md)
