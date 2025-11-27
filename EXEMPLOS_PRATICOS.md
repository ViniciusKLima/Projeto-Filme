# 📚 Exemplos Práticos

## Exemplo 1: Importar Seus Próprios Filmes

### 1. Crie um arquivo `meus-filmes.json`:

```json
[
  {
    "nome": "Oppenheimer",
    "sinopse": "A história do desenvolvimento da bomba atômica durante a Segunda Guerra Mundial.",
    "capaPrincipal": "https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGClnuSuJ2a.jpg",
    "capaFundo": "https://image.tmdb.org/t/p/original/fm6KqXpG2Ow8o6c4pIIsix1rY9d.jpg",
    "anoLancamento": 2023,
    "diretor": "Christopher Nolan",
    "elenco": "Cillian Murphy, Robert Downey Jr., Emily Blunt",
    "genero": "Biografia, Drama, História",
    "nota": 8.3,
    "trailer": "https://www.youtube.com/watch?v=uYPbbksJxJ8",
    "streaming": "Netflix"
  },
  {
    "nome": "Barbie",
    "sinopse": "Barbie é libertada do mundo da fantasia de plástico de Barbie Land e entra no mundo real.",
    "capaPrincipal": "https://image.tmdb.org/t/p/w500/NNZ6pIII34tViNl9eFytjBZcrP.jpg",
    "capaFundo": "https://image.tmdb.org/t/p/original/iJFzqoVY2pwTh2MyMvDJuwKdD6i.jpg",
    "anoLancamento": 2023,
    "diretor": "Greta Gerwig",
    "elenco": "Margot Robbie, Ryan Gosling, Will Ferrell",
    "genero": "Comédia, Fantasia, Aventura",
    "nota": 7.8,
    "trailer": "https://www.youtube.com/watch?v=FYLyVxwvjEw",
    "streaming": "Max"
  },
  {
    "nome": "Homem-Aranha: Sem Volta para Casa",
    "sinopse": "Com sua identidade revelada, Peter Parker pede ajuda ao Doutor Estranho para restaurar o sigilo.",
    "capaPrincipal": "https://image.tmdb.org/t/p/w500/uJYYizSuA9w3sXrSCLSzAm5XVIII.jpg",
    "capaFundo": "https://image.tmdb.org/t/p/original/tVQHW5kjwRMb89w962gXvrVepe.jpg",
    "anoLancamento": 2021,
    "diretor": "Jon Watts",
    "elenco": "Tom Holland, Zendaya, Tobey Maguire",
    "genero": "Ação, Aventura, Fantasia",
    "nota": 8.2,
    "trailer": "https://www.youtube.com/watch?v=JfVOs4VSpmA",
    "streaming": "Sony Plus, Netflix"
  }
]
```

### 2. Execute o comando:

```bash
php bin/seed-filmes.php meus-filmes.json
```

### 3. Resultado esperado:

```
📽️  Preparando seed de 3 filme(s)...

✓ Filme #1: Oppenheimer
✓ Filme #2: Barbie
✓ Filme #3: Homem-Aranha: Sem Volta para Casa

============================================================
✅ Sucesso! 3 filme(s) inserido(s) no banco de dados.
============================================================
```

### 4. Acesse o painel em `/adm` para ver os filmes importados!

---

## Exemplo 2: Importar Filmes em Massa (100+)

Se você tem um arquivo grande com muitos filmes:

```bash
php bin/seed-filmes.php catalogo-completo.json
```

O script vai:

- ✅ Validar todos os filmes
- ✅ Inserir em um batch (mais rápido)
- ✅ Pular filmes com erro
- ✅ Exibir relatório final

Tempo estimado: ~1-2 segundos para 1000 filmes

---

## Exemplo 3: Adicionar Filme Manualmente via API (POST)

Se preferir, você pode fazer um POST direto ao formulário:

```bash
curl -X POST http://localhost/adm \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "action=add&nome=Dune&diretor=Denis Villeneuve&ano=2021&genero=Ficção Científica&nota=8.0"
```

**Nota:** Você precisa estar logado (ter session válida com admin).

---

## Exemplo 4: Criar Múltiplos Admins

Se quiser mais usuários admin, você pode:

### Opção A: Modificar o script `create-admin.php`

Edite as linhas:

```php
$admin->setEmail('novo-email@filmes.local');
$admin->setSenha(password_hash('Nova@Senha123', PASSWORD_DEFAULT));
```

E execute:

```bash
php bin/create-admin.php
```

### Opção B: Adicionar via banco diretamente

```sql
INSERT INTO users (nome, email, senha, tipo_conta) VALUES (
  'Novo Admin',
  'novo@filmes.local',
  '$2y$10$...',  -- use password_hash() em PHP ou bcrypt
  'admin'
);
```

---

## Exemplo 5: Usar com GitHub Actions (CI/CD)

Se quiser automatizar a importação de filmes em um deploy:

### `.github/workflows/deploy.yml`

```yaml
name: Deploy e Seed

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.1"

      - name: Install dependencies
        run: composer install

      - name: Import films
        run: php bin/seed-filmes.php filmes-exemplo.json
```

---

## Exemplo 6: Estrutura Avançada de JSON (Validação de Campos)

Para máxima compatibilidade, siga este formato:

```json
[
  {
    "nome": "Filme Completo",
    "sinopse": "Uma descrição detalhada do filme com múltiplas linhas\ne parágrafos.",
    "capaPrincipal": "https://exemplo.com/poster.jpg",
    "capaFundo": "https://exemplo.com/backdrop.jpg",
    "anoLancamento": 2024,
    "diretor": "Nome Completo do Diretor",
    "elenco": "Ator Principal, Ator Coadjuvante, Mais um Ator",
    "genero": "Ação, Drama, Ficção Científica",
    "nota": 8.5,
    "trailer": "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
    "streaming": "Netflix, Prime Video, Disney+"
  }
]
```

**Validações:**

- `nome` (string, 255 chars máx) — **OBRIGATÓRIO**
- `sinopse` (text, ilimitado) — opcional
- `capaPrincipal` (URL) — opcional
- `capaFundo` (URL) — opcional
- `anoLancamento` (inteiro) — opcional
- `diretor` (string, 255 chars) — opcional
- `elenco` (text) — opcional
- `genero` (string, 255 chars) — opcional
- `nota` (float, 0-5) — opcional
- `trailer` (URL) — opcional
- `streaming` (string, 255 chars) — opcional

---

## Exemplo 7: Testar Proteção de Rota

### Teste 1: Sem login

```bash
curl http://localhost/adm
```

→ Redirecionado para `/auth/login`

### Teste 2: Logado como cliente

1. Faça login em `/auth/login` com uma conta cliente
2. Tente acessar `/adm`
   → Redirecionado para `/auth/login` (só admin consegue)

### Teste 3: Logado como admin

1. Faça login com `admin@filmes.local` / `Admin@123456`
2. Você é redirecionado automático para `/adm`
3. Acesso concedido! ✅

---

## Exemplo 8: Recuperar de Erro (Deletar Admins por Acidente)

Se deletou o admin acidentalmente, basta rodar novamente:

```bash
php bin/create-admin.php
```

Se a conta já existe, ele avisa:

```
⚠️  Usuário admin já existe!
Email: admin@filmes.local
Tipo: admin
```

Se foi deletado, ele recria:

```
✅ Usuário admin criado com sucesso!
```

---

## Exemplo 9: Estrutura de Pastas de Projeto

Após seguir tudo, sua estrutura fica assim:

```
c:\projetoFilmes\
├── bin/
│   ├── seed-filmes.php           ← ✨ NOVO: importar filmes
│   ├── create-admin.php          ← ✨ NOVO: criar admin
│   └── ...
├── module/Application/
│   ├── src/Controller/
│   │   ├── FilmesController.php  ← ✨ NOVO: painel admin
│   │   ├── AuthController.php    ← ✨ MODIFICADO: rota admin
│   │   └── ...
│   ├── view/application/
│   │   └── filmes/
│   │       └── index.phtml       ← ✨ NOVO: template painel
│   └── ...
├── filmes-exemplo.json           ← ✨ NOVO: exemplo de dados
├── PAINEL_ADMIN_GUIA.md          ← ✨ NOVO: guia de uso
├── DOCUMENTACAO_TECNICA.md       ← ✨ NOVO: documentação técnica
├── FUNCIONALIDADES_CRIADAS.md    ← ✨ NOVO: resumo
└── ...
```

---

## Exemplo 10: Workflow Completo de Uso

### Dia 1: Setup Inicial

```bash
# 1. Criar admin
php bin/create-admin.php

# 2. Importar filmes iniciais
php bin/seed-filmes.php filmes-exemplo.json
```

### Dia 2: Adicionar Filmes Manualmente

1. Acesse `/auth/login`
2. Login com `admin@filmes.local` / `Admin@123456`
3. Vai para `/adm` automaticamente
4. Adicione filmes um por um ou importe em lote

### Dia 3: Em Produção

- Mesmos scripts funcionam sem mudança
- Rota `/adm` protegida contra acesso não-autorizado
- Relatórios de import em CLI para logging

---

Pronto para usar! 🚀
