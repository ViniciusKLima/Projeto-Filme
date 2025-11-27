# 📥 10 - Seed de Dados (Importação em Lote)

## Visão Geral

O script `seed-filmes.php` permite importar uma lista completa de filmes do JSON diretamente para o banco de dados, sem precisar adicionar um por um na interface.

**Arquivo:** `bin/seed-filmes.php`  
**Função:** Ler JSON, validar dados, inserir no banco  
**Uso:** CLI (Command Line Interface)

---

## Como Usar

### Passo 1: Preparar o JSON

Crie um arquivo com a lista de filmes:

```bash
# Exemplo: filmes.json
[
  {
    "nome": "Inception",
    "sinopse": "Um filme sobre sonhos...",
    "capaPrincipal": "https://example.com/poster.jpg",
    "capaFundo": "https://example.com/backdrop.jpg",
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

### Passo 2: Rodar o Script

```bash
cd c:\projetoFilmes
php bin/seed-filmes.php filmes.json
```

### Passo 3: Ver Resultado

```
📽️  Preparando seed de 3 filme(s)...

✓ Filme #1: Inception
✓ Filme #2: The Dark Knight
⏭️  Filme #3: "Interestelar" já existe no banco, pulando...

============================================================
✅ Sucesso! 2 filme(s) inserido(s) no banco de dados.
⏭️  1 filme(s) duplicado(s) (já existia no banco).
============================================================
```

---

## Estrutura JSON

### Campos Obrigatórios

- `nome` _(string, 255 chars)_ — **OBRIGATÓRIO**

### Campos Opcionais

- `sinopse` _(text)_ — Descrição do filme
- `capaPrincipal` _(URL)_ — URL da capa/poster
- `capaFundo` _(URL)_ — URL da imagem de fundo
- `anoLancamento` _(inteiro)_ — Ano de lançamento
- `diretor` _(string, 255 chars)_ — Nome do diretor
- `elenco` _(text)_ — Atores (separados por vírgula)
- `genero` _(string, 255 chars)_ — Gênero(s)
- `nota` _(float, 0-5)_ — Avaliação
- `trailer` _(URL)_ — URL do trailer
- `streaming` _(string, 255 chars)_ — Plataforma(s)

### Exemplo Completo

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
    "sinopse": "Barbie é libertada do mundo da fantasia...",
    "capaPrincipal": "...",
    "capaFundo": "...",
    "anoLancamento": 2023,
    "diretor": "Greta Gerwig",
    "elenco": "Margot Robbie, Ryan Gosling",
    "genero": "Comédia, Fantasia",
    "nota": 7.8,
    "trailer": "...",
    "streaming": "Max"
  }
]
```

---

## Validações

### Na Leitura do JSON

```
✅ Arquivo existe?
✅ JSON é válido?
✅ É um array?
✅ Array não vazio?
```

### Para Cada Filme

```
✅ Campo 'nome' existe e não está vazio?
✅ Filme não duplicado (mesmo nome)?
✅ Campos convertidos para tipos corretos?
```

---

## Duplicação

O script **evita filmes duplicados**:

```php
// Verifica se filme com mesmo nome já existe
$existe = $repo->findOneBy(['nome' => trim($dados['nome'])]);

if ($existe) {
    echo "⏭️  Filme já existe no banco, pulando...\n";
    $duplicado++;
    continue;
}
```

**Resultado:** Se você rodar o seed 2 vezes:

- Primeira vez: insere 5 filmes
- Segunda vez: pula os 5 filmes (duplicados)

---

## Fluxo Detalhado

```
1. Validações Iniciais
   ├─ Arquivo existe?
   ├─ JSON decodifica sem erro?
   ├─ É um array?
   └─ Array não vazio?

2. Conexão ao Banco
   ├─ Conecta via Doctrine
   ├─ Lê config doctrine.local.php
   └─ Exibe "Conectado ao banco: projeto_filmes"

3. Para Cada Filme
   ├─ Valida campo 'nome' (obrigatório)
   ├─ Verifica duplicação (findOneBy)
   ├─ Cria entidade Filme
   ├─ Seta todos os campos
   ├─ persist($filme)
   └─ Exibe "✓ Filme: Nome"

4. Batch Insert
   ├─ Em->flush() (salva todos de uma vez)
   ├─ Mais rápido que flush() individual
   └─ Exibe relatório final

5. Relatório
   ├─ Quantos inseridos?
   ├─ Quantos duplicados?
   ├─ Quantos falharam?
   └─ Status final (✅ ou ❌)
```

---

## Tratamento de Erros

### Erro: Arquivo não encontrado

```bash
$ php bin/seed-filmes.php inexistente.json

❌ Erro: Arquivo 'inexistente.json' não encontrado.
```

### Erro: JSON inválido

```bash
$ php bin/seed-filmes.php filmes-ruim.json

❌ Erro ao decodificar JSON: Syntax error
```

### Erro: Banco indisponível

```bash
❌ Erro ao conectar ao banco: SQLSTATE[HY000]...
```

### Aviso: Nome vazio

```bash
⚠️  Filme #1: Campo 'nome' vazio, pulando...
```

---

## Performance

### Velocidade

- **100 filmes:** ~1 segundo
- **1000 filmes:** ~5-10 segundos
- **Batch insert** é muito mais rápido que insert individual

### Otimização

```php
// ✅ BOM: Flush uma vez ao final (batch)
foreach ($filmes as $filme) {
    $em->persist($filme);
}
$em->flush();  // Salva todos de uma vez

// ❌ RUIM: Flush para cada filme
foreach ($filmes as $filme) {
    $em->persist($filme);
    $em->flush();  // Lento!
}
```

---

## Casos de Uso

### 1. Populating Inicial

```bash
# Primeira vez que roda o projeto
php bin/seed-filmes.php filmes-exemplo.json
```

### 2. Adicionar Filmes em Lote

```bash
# Você criou uma lista de 50 filmes
php bin/seed-filmes.php novos-filmes.json
```

### 3. Atualizar Dados (com cuidado)

```bash
# ⚠️ Se rodar 2 vezes, detecta duplicação automaticamente
php bin/seed-filmes.php filmes.json
```

---

## Comparação: Manual vs Seed

### ❌ Manual (Interface)

```
1. Login em /adm
2. Preencher nome
3. Preencher diretor
4. Preencher elenco
5. ... (8+ campos)
6. Clique "Adicionar"
7. Repetir 50 vezes 😅

Tempo: ~30-60 minutos para 50 filmes
```

### ✅ Seed (JSON)

```
1. Prepare 1 arquivo filmes.json
2. php bin/seed-filmes.php filmes.json

Tempo: ~5 segundos para 50 filmes
```

---

## Arquivos Fornecidos

### filmes-exemplo.json

Incluído no projeto com 5 filmes:

- Inception
- The Dark Knight
- Interestelar
- Pulp Fiction
- Matrix

```bash
php bin/seed-filmes.php filmes-exemplo.json
```

---

## Boas Práticas

✅ **Faça:**

- Validar JSON antes de rodar
- Usar nomes únicos para filmes
- Testar com pequeno lote primeiro
- Fazer backup do banco antes

❌ **Evite:**

- URLs inválidas em capas/trailer
- Deixar nome vazio
- Rodar em produção durante pico

---

## Troubleshooting

### "SQLSTATE[HY000]: General error"

Banco não conectou. Verifique `config/autoload/doctrine.local.php`.

### "Arquivo não encontrado"

Certifique-se que o caminho é relativo a `c:\projetoFilmes\`.

### "JSON inválido"

Valide seu JSON em https://jsonlint.com/

### Script roda mas não insere nada

Verifique se todos filmes já existem (duplicados).

---

## Próximos Passos

1. Prepare seu arquivo filmes.json
2. Rode `php bin/seed-filmes.php filmes.json`
3. Acesse `/adm` para ver os filmes inseridos
4. Edite/delete via painel se necessário

---

Próximo: [PAINEL ADMIN](./12-PAINEL-ADMIN.md)
