# 🔐 06 - Autenticação & Session

## Como Funciona a Autenticação

### Fluxo Completo

```
1. Usuário acessa /auth/login
   ↓
2. Preenche email + senha
   ↓
3. Submete POST /auth/authenticate
   ↓
4. AuthController verifica:
   ├─ Email existe no banco?
   ├─ Senha está correta?
   └─ Tipo de conta (admin ou cliente)
   ↓
5. Se válido:
   ├─ Inicia sessão PHP
   ├─ Salva dados em $_SESSION['user']
   ├─ Gera token de "remember me" (cookie)
   └─ Redireciona conforme tipo:
       ├─ Admin → /adm
       └─ Cliente → /home
   ↓
6. Próximas requisições carregam dados da SESSION
```

---

## Entidade User

**Localização:** `module/Application/src/Entity/User.php`

```php
#[Entity, Table(name: 'users')]
class User {
    #[Id, Column(type: 'integer'), GeneratedValue]
    private ?int $id = null;

    #[Column(type: 'string', length: 255)]
    private ?string $nome = null;

    #[Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[Column(type: 'string', length: 255)]
    private ?string $senha = null;

    #[Column(type: 'string', length: 50)]  // 'admin' ou 'cliente'
    private ?string $tipoConta = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $rememberToken = null;

    // Getters e Setters...
}
```

**Campos:**

- `id` - Identificador único (auto-increment)
- `nome` - Nome completo do usuário
- `email` - Email único (login)
- `senha` - Hash da senha (PASSWORD_DEFAULT)
- `tipoConta` - Tipo: `"admin"` ou `"cliente"`
- `rememberToken` - Token para "remember me"

---

## Credenciais Padrão

```
Email:  admin@filmes.local
Senha:  Admin@123456
Tipo:   admin
```

**Como criar:**

```bash
php bin/create-admin.php
```

---

## Session Structure

Depois de fazer login, a SESSION tem esta estrutura:

```php
$_SESSION['user'] = [
    'id'    => 1,                    // ID do usuário
    'nome'  => 'Administrador',      // Nome completo
    'email' => 'admin@filmes.local', // Email (login)
    'tipo'  => 'admin'               // Tipo: 'admin' ou 'cliente'
];
```

---

## AuthController

**Localização:** `module/Application/src/Controller/AuthController.php`

### Ações (Actions)

#### 1. **loginAction()**

- **URL:** `GET /auth/login`
- **Função:** Exibir formulário de login
- **View:** `application/login/login.phtml`

```php
public function loginAction() {
    $vm = new ViewModel();
    $vm->setTerminal(true);
    return $vm;
}
```

#### 2. **authenticateAction()**

- **URL:** `POST /auth/authenticate`
- **Função:** Processar login (validar credenciais)
- **Fluxo:**
  1. Valida email e senha
  2. Busca usuário no banco
  3. Verifica senha com `password_verify()`
  4. Cria SESSION['user']
  5. Define cookies de persistência
  6. Redireciona conforme tipo

```php
public function authenticateAction() {
    $email = $this->params()->fromPost('email');
    $senha = $this->params()->fromPost('senha');

    $repo = $this->getEntityManager()->getRepository(User::class);
    $usuario = $repo->findOneBy(['email' => $email]);

    if (!password_verify($senha, $usuario->getSenha())) {
        // Erro
    }

    // Iniciar sessão ANTES de configurar cookies
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([...]);
        session_start();
    }

    $_SESSION['user'] = [
        'id'    => $usuario->getId(),
        'nome'  => $usuario->getNome(),
        'email' => $usuario->getEmail(),
        'tipo'  => $usuario->getTipoConta()
    ];

    // Redirecionar conforme tipo
    if ($usuario->getTipoConta() === 'admin') {
        return $this->redirect()->toRoute('filmes');  // /adm
    }

    return $this->redirect()->toRoute('home');
}
```

#### 3. **cadastroAction()**

- **URL:** `GET /auth/cadastro`
- **Função:** Exibir formulário de registro
- **View:** `application/login/cadastro.phtml`

#### 4. **registerAction()**

- **URL:** `POST /auth/register`
- **Função:** Criar nova conta
- **Validações:**
  - Email único
  - Campos não vazios
  - Senha hasheada com PASSWORD_DEFAULT

```php
public function registerAction() {
    $email = $this->params()->fromPost('email');

    // Verificar se email já existe
    $repo = $this->getEntityManager()->getRepository(User::class);
    $existe = $repo->findOneBy(['email' => $email]);
    if ($existe) {
        // Erro: Email duplicado
    }

    // Criar novo usuário
    $novo = new User();
    $novo->setEmail($email);
    $novo->setSenha(password_hash($senha, PASSWORD_DEFAULT));
    $novo->setTipoConta('cliente');  // Novo sempre é cliente

    $em->persist($novo);
    $em->flush();

    // Redirecionar para login
    return $this->redirect()->toRoute('auth', ['action' => 'login']);
}
```

#### 5. **logoutAction()**

- **URL:** `GET /auth/logout`
- **Função:** Fazer logout
- **Fluxo:**
  1. Limpa remember token no banco
  2. Remove cookies
  3. Destroi session
  4. Redireciona para login

```php
public function logoutAction() {
    // Limpar remember token
    $u->setRememberToken(null);
    $em->persist($u);
    $em->flush();

    // Remover cookie
    setcookie('remember_me', '', time() - 3600, '/');

    // Destruir session
    $_SESSION = [];
    session_destroy();

    return $this->redirect()->toRoute('auth', ['action' => 'login']);
}
```

---

## Proteção de Rota

Exemplo: Painel admin (`/adm`) só acessível por admin

```php
// FilmesController.php
private function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user']) ||
        ($_SESSION['user']['tipo'] ?? '') !== 'admin') {
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
    return null;
}

public function indexAction() {
    // Bloqueia se não for admin
    $check = $this->requireAdmin();
    if ($check) return $check;

    // Continua apenas se admin
}
```

---

## Persistência (Remember Me)

Token salvo em cookie por 30 dias:

```php
$token = bin2hex(random_bytes(32));
$usuario->setRememberToken($token);
$em->persist($usuario);
$em->flush();

setcookie('remember_me', $token, [
    'expires' => time() + 30 * 24 * 3600,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
```

(Nota: Funcionalidade de "remember me" automático poderia ser implementada futuramente)

---

## Security Best Practices

✅ **Implementado:**

- Senhas hasheadas com `PASSWORD_DEFAULT` (bcrypt)
- Session segura com httponly + samesite
- Validação de entrada (trim, empty)
- CSRF protection via Laminas (se habilitado)

⚠️ **Considere implementar:**

- HTTPS em produção
- Rate limiting no login
- Two-factor authentication (2FA)
- Logout automático por inatividade
- Refresh tokens

---

## Testando Autenticação

### Teste 1: Fazer login bem-sucedido

```
1. Vá para http://localhost/auth/login
2. Email: admin@filmes.local
3. Senha: Admin@123456
4. Clique "Entrar"
5. ✅ Redirecionado para /adm
```

### Teste 2: Senha incorreta

```
1. Email: admin@filmes.local
2. Senha: errada
3. ✅ Mensagem de erro "Senha incorreta"
```

### Teste 3: Email não existe

```
1. Email: inexistente@teste.com
2. Senha: qualquer coisa
3. ✅ Mensagem de erro "Não existe nenhuma conta"
```

### Teste 4: Logout

```
1. Estando logado
2. Clique em "Sair" (header)
3. ✅ Redirecionado para /auth/login
4. ✅ Session destroída
```

---

## Forms Necessários

### Login Form (`login.phtml`)

```html
<form method="post" action="/auth/authenticate">
  <input name="email" type="email" required />
  <input name="senha" type="password" required />
  <button type="submit">Entrar</button>
</form>
```

### Registro Form (`cadastro.phtml`)

```html
<form method="post" action="/auth/register">
  <input name="usuario" type="text" required />
  <input name="email" type="email" required />
  <input name="senha" type="password" required />
  <button type="submit">Criar Conta</button>
</form>
```

---

Próximo: [PROTEÇÃO DE ROTA](./08-PROTECAO-ROTA.md)
