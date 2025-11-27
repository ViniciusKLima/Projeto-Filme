# 🚀 01 - Início Rápido

## Instalação

### Pré-requisitos

- PHP 7.4+
- MySQL 5.7+
- Composer

### Passos de Instalação

1. **Clone o repositório**

```bash
git clone https://github.com/ViniciusKLima/Projeto-Filme.git
cd Projeto-Filme
```

2. **Instale as dependências**

```bash
composer install
```

3. **Configure o banco de dados**

Edite `config/autoload/doctrine.local.php` e adicione suas credenciais:

```php
<?php
return [
    'doctrine' => [
        'connection' => [
            'params' => [
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',      // seu host
                'port'     => 3306,
                'user'     => 'root',           // seu user
                'password' => '',               // sua senha
                'dbname'   => 'projeto_filmes', // seu DB
                'charset'  => 'utf8mb4',
            ],
        ],
    ],
];
```

4. **Crie o banco e as tabelas**

```bash
php create_schema.php
```

5. **Crie o usuário admin padrão**

```bash
php bin/create-admin.php
```

Credenciais criadas:

- Email: `admin@filmes.local`
- Senha: `Admin@123456`

6. **Importe filmes de exemplo (opcional)**

```bash
php bin/seed-filmes.php filmes-exemplo.json
```

7. **Rode o servidor de desenvolvimento**

```bash
php -S localhost:8000 -t public
```

Acesse: http://localhost:8000

---

## ✅ Verificação

Se tudo está funcionando:

- [ ] Servidor rodando em http://localhost:8000
- [ ] Home mostra lista de filmes
- [ ] Login funciona em `/auth/login`
- [ ] Painel admin acessível em `/adm` (após login)
- [ ] Adicionar/editar/deletar filmes funciona

---

## 🎯 Próximos Passos

1. Leia [ESTRUTURA DO PROJETO](./02-ESTRUTURA.md)
2. Explore a [ARQUITETURA GERAL](./03-ARQUITETURA.md)
3. Aprenda sobre [AUTENTICAÇÃO](./06-AUTENTICACAO.md)

---

## 🆘 Problemas Comuns

### "SQLSTATE[HY000]: General error"

Verifique se o banco está criado e as credenciais em `doctrine.local.php` estão corretas.

### "Route with name 'filmes' not found"

Rode o projeto novamente ou limpe o cache de config:

```bash
php bin/clear-config-cache.php
```

### Session errors ao fazer login

Certifique-se que `/tmp` tem permissão de escrita (Linux) ou que `C:\Windows\Temp` (Windows).

Mais dúvidas? Veja [TROUBLESHOOTING](./17-TROUBLESHOOTING.md)
