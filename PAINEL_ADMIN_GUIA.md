# 📽️ Painel de Administração - Guia de Uso

## 🔐 Credenciais para Login

### Usuário Admin

- **Email:** `admin@filmes.local`
- **Senha:** `Admin@123456`

### Acessar Painel

1. Vá para: http://localhost/auth/login
2. Insira o email e senha acima
3. Após logar, você será redirecionado automaticamente para: http://localhost/adm

**Nota:** Apenas usuários com `tipoConta = 'admin'` conseguem acessar o painel. Outros usuários serão redirecionados para a home.

---

## 📥 Importar Filmes em Lote (Seed)

Se você tem uma lista de filmes em JSON e quer inserir **todos de uma vez** no banco de dados, use o script `seed-filmes.php`.

### Passo 1: Preparar arquivo JSON

Crie um arquivo `filmes.json` com a seguinte estrutura:

```json
[
  {
    "nome": "Inception",
    "sinopse": "Descrição do filme...",
    "capaPrincipal": "https://image.example.com/poster.jpg",
    "capaFundo": "https://image.example.com/backdrop.jpg",
    "anoLancamento": 2010,
    "diretor": "Christopher Nolan",
    "elenco": "Leonardo DiCaprio, Marion Cotillard",
    "genero": "Ficção Científica, Ação",
    "nota": 8.8,
    "trailer": "https://youtube.com/watch?v=...",
    "streaming": "Netflix"
  },
  {
    "nome": "The Dark Knight",
    ...
  }
]
```

**Campos esperados:**

- `nome` **(obrigatório)** — Nome do filme
- `sinopse` — Descrição do filme
- `capaPrincipal` — URL da capa/poster
- `capaFundo` — URL da imagem de fundo
- `anoLancamento` — Ano (número inteiro)
- `diretor` — Nome do diretor
- `elenco` — Atores (string com nomes separados por vírgula)
- `genero` — Gêneros (ex: "Ação, Drama")
- `nota` — Avaliação (0 a 5, pode ter decimais)
- `trailer` — URL do trailer (YouTube, etc)
- `streaming` — Serviço de streaming (Netflix, Prime Video, etc)

### Passo 2: Rodar o Script

```bash
# Se tiver um arquivo na raiz do projeto chamado "filmes.json"
php bin/seed-filmes.php filmes.json

# Se o arquivo estiver em outro local
php bin/seed-filmes.php /caminho/completo/filmes.json
```

### Exemplo de Uso

```bash
# Exemplo com arquivo de teste incluído
php bin/seed-filmes.php filmes-exemplo.json
```

### Saída Esperada

```
📽️  Preparando seed de 5 filme(s)...

✓ Filme #1: Inception
✓ Filme #2: The Dark Knight
✓ Filme #3: Interestelar
✓ Filme #4: Pulp Fiction
✓ Filme #5: Matrix

============================================================
✅ Sucesso! 5 filme(s) inserido(s) no banco de dados.
============================================================
```

---

## 👤 Criar Usuário Admin (Inicial)

Se você precisa criar um novo usuário admin (ou se o padrão não foi criado), use:

```bash
php bin/create-admin.php
```

Isso vai:

1. Verificar se `admin@filmes.local` já existe
2. Se não existir, criar com a senha `Admin@123456`
3. Exibir as credenciais para usar

---

## 🎬 Operações no Painel de Admin (/adm)

Depois de logado como admin, você pode:

### ✏️ Adicionar Filme

- Preencha os campos do formulário na seção esquerda
- Clique em **"Adicionar"**
- Será salvo no banco de dados

### 📝 Editar Filme

1. Encontre o filme na lista (direita)
2. Clique em **"Editar"**
3. Os dados serão carregados no formulário
4. Modifique o que quiser
5. Clique em **"Atualizar"**

### 🗑️ Deletar Filme

1. Na lista (direita), clique em **"Remover"** do filme
2. Confirme a exclusão
3. Será removido do banco de dados

---

## 🔒 Proteção da Rota

A rota `/adm` **está 100% protegida**:

- ✅ Se você está logado como **admin** → acesso liberado
- ❌ Se não está logado → redirecionado para `/auth/login`
- ❌ Se está logado como **cliente** → redirecionado para `/auth/login`

**Código responsável:** Método `requireAdmin()` no `FilmesController.php`

---

## 📂 Arquivos Criados/Modificados

```
bin/
  ├── seed-filmes.php        ← Script para importar filmes em lote
  ├── create-admin.php       ← Script para criar usuário admin

filmes-exemplo.json          ← Exemplo de JSON para testar seed

module/Application/
  ├── src/Controller/
  │   ├── FilmesController.php    ← Controller do painel (✅ protegido)
  │   └── AuthController.php      ← Atualizado para redirecionar admin → /adm
  ├── view/application/
  │   └── filmes/
  │       └── index.phtml         ← Template do painel
  └── config/
      └── module.config.php       ← Rota /adm registrada
```

---

## 🚀 Resumo Rápido

| Ação            | Comando                               | Resultado                             |
| --------------- | ------------------------------------- | ------------------------------------- |
| Criar admin     | `php bin/create-admin.php`            | Cria usuário `admin@filmes.local`     |
| Importar filmes | `php bin/seed-filmes.php filmes.json` | Insere todos os filmes do JSON no BD  |
| Acessar painel  | Ir para `/adm`                        | Abre painel (só se logado como admin) |
| Fazer login     | Ir para `/auth/login`                 | Login com email/senha                 |
| Fazer logout    | Clique em "Logout" (se existir)       | Destrói sessão                        |

---

## ⚠️ Possíveis Erros e Soluções

### "Não existe nenhuma conta com esse email"

→ Use `admin@filmes.local` ou crie nova conta com `php bin/create-admin.php`

### "Erro ao conectar ao banco"

→ Verifique se as credenciais em `config/autoload/doctrine.local.php` estão corretas

### "Arquivo não encontrado" ao rodar seed

→ Certifique-se que o arquivo JSON existe e o caminho está correto

### "Você precisa estar logado como admin"

→ Faça login com uma conta que tenha `tipoConta = 'admin'`

---

## 📝 Formato Esperado do JSON

O JSON **deve ser um array** de objetos filmes:

✅ **Correto:**

```json
[
  { "nome": "Filme 1", ... },
  { "nome": "Filme 2", ... }
]
```

❌ **Errado:**

```json
{
  "filmes": [
    { "nome": "Filme 1", ... }
  ]
}
```

---

Pronto! O seu painel está **100% funcional e protegido!** 🎉
