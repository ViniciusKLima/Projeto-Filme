# 📚 Documentação - LocMovies

Bem-vindo à documentação técnica do **LocMovies**, uma plataforma de gerenciamento e descoberta de filmes construída com Laminas e Doctrine ORM.

## 📖 Guias Disponíveis

### 🚀 Para Começar

- **[INÍCIO RÁPIDO](./01-INICIO-RAPIDO.md)** - Como instalar e rodar o projeto
- **[ESTRUTURA DO PROJETO](./02-ESTRUTURA.md)** - Organização de pastas e arquivos

### 🏗️ Arquitetura

- **[ARQUITETURA GERAL](./03-ARQUITETURA.md)** - Visão geral do MVC e componentes
- **[ROUTING](./04-ROUTING.md)** - Sistema de rotas e navegação
- **[PADRÕES DE DESIGN](./05-PADROES.md)** - Patterns e boas práticas

### 🔐 Autenticação & Segurança

- **[AUTENTICAÇÃO](./06-AUTENTICACAO.md)** - Login, registro e session
- **[AUTORIZAÇÃO](./07-AUTORIZACAO.md)** - Controle de acesso por role
- **[PROTEÇÃO DE ROTA](./08-PROTECAO-ROTA.md)** - Como proteger endpoints

### 💾 Banco de Dados

- **[ENTITIES](./09-ENTITIES.md)** - Entidades Doctrine (User, Filme)
- **[SEED DE DADOS](./10-SEED-DADOS.md)** - Como importar dados em lote
- **[MIGRATIONS](./11-MIGRATIONS.md)** - Versionar schema do banco (futuro)

### 🎬 Painel de Admin

- **[PAINEL ADMIN](./12-PAINEL-ADMIN.md)** - CRUD de filmes
- **[CRUD OPERATIONS](./13-CRUD.md)** - Operações Create, Read, Update, Delete

### 🎨 Frontend

- **[VIEWS & TEMPLATES](./14-VIEWS.md)** - Sistema de templates PHTML
- **[COMPONENTS](./15-COMPONENTS.md)** - Componentes reutilizáveis
- **[STYLING](./16-STYLING.md)** - CSS e design responsivo

### 🔧 Desenvolvimento

- **[TROUBLESHOOTING](./17-TROUBLESHOOTING.md)** - Erros comuns e soluções
- **[DEPLOYMENT](./18-DEPLOYMENT.md)** - Publicar em produção
- **[CONTRIBUINDO](./19-CONTRIBUINDO.md)** - Como contribuir

---

## 🎯 Roteiros de Aprendizado

### Iniciante

1. [Início Rápido](./01-INICIO-RAPIDO.md)
2. [Estrutura do Projeto](./02-ESTRUTURA.md)
3. [Arquitetura Geral](./03-ARQUITETURA.md)
4. [Autenticação](./06-AUTENTICACAO.md)

### Intermediário

5. [Routing](./04-ROUTING.md)
6. [Entities](./09-ENTITIES.md)
7. [Painel Admin](./12-PAINEL-ADMIN.md)
8. [CRUD Operations](./13-CRUD.md)

### Avançado

9. [Padrões de Design](./05-PADROES.md)
10. [Proteção de Rota](./08-PROTECAO-ROTA.md)
11. [Seed de Dados](./10-SEED-DADOS.md)
12. [Deployment](./18-DEPLOYMENT.md)

---

## 📊 Quick Reference

### Comandos Úteis

```bash
# Importar filmes em lote
php bin/seed-filmes.php filmes.json

# Criar usuário admin
php bin/create-admin.php

# Rodar em desenvolvimento
php -S localhost:8000 -t public

# Limpeza de cache (se needed)
php bin/clear-config-cache.php
```

### URLs Principais

| URL              | Descrição                   |
| ---------------- | --------------------------- |
| `/`              | Home - lista de filmes      |
| `/filme/{id}`    | Detalhes do filme           |
| `/auth/login`    | Tela de login               |
| `/auth/cadastro` | Tela de registro            |
| `/adm`           | Painel de admin (protegido) |

### Credenciais Padrão

```
Email:  admin@filmes.local
Senha:  Admin@123456
```

---

## 🛠️ Stack Técnico

- **Framework:** Laminas 3.x
- **ORM:** Doctrine 3.x
- **Banco:** MySQL/MariaDB
- **PHP:** 7.4+
- **Frontend:** HTML5, CSS3, Vanilla JS
- **Padrão:** MVC (Model-View-Controller)

---

## 📞 Suporte

Encontrou um problema? Consulte:

- [Troubleshooting](./17-TROUBLESHOOTING.md)
- [FAQ](#faq)
- Crie uma issue no GitHub

---

## 📄 Licença

Este projeto é licenciado sob a [MIT License](../LICENSE.md).

---

**Última atualização:** Novembro 2024
**Versão:** 1.0.0
