# Estratégias de Autenticação e Autorização

## Visão Geral

Este documento apresenta as estratégias recomendadas para implementação de autenticação e gerenciamento de roles/permissões no projeto Todo App, seguindo as melhores práticas de segurança e arquitetura de software.

---

## 1. Estratégias de Autenticação

### 1.1 Sessão Baseada em Cookies (Session-Based Authentication)

**Descrição:** Autenticação tradicional usando sessões PHP com cookies seguros.

**Implementação:**
```php
// Iniciar sessão segura
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Armazenar usuário na sessão após login
$_SESSION['user_id'] = $user->getId();
$_SESSION['last_activity'] = time();
```

**Vantagens:**
- Simples de implementar
- Controle total sobre a sessão
- Fácil invalidação de sessões

**Desvantagens:**
- Não escalável horizontalmente (sem sessão compartilhada)
- Requer proteção CSRF adicional

**Recomendação:** ✅ **USAR** - Para aplicações de pequeno/médio porte

---


---

## 2. Gerenciamento de Roles (Papéis)

### 2.1 Estrutura de Roles Recomendada

```php
enum UserRoleEnum: string
{
    case ADMIN = 'admin';           // Acesso total ao sistema
    case MANAGER = 'manager';       // Gerencia projetos e usuários
    case USER = 'user';             // Usuário padrão
    case GUEST = 'guest';           // Acesso limitado (visualização)
}
```

### 2.2 Hierarquia de Permissões

```
ADMIN
├── Gerenciar usuários (CRUD)
├── Gerenciar todos os projetos
├── Gerenciar configurações do sistema
└── Acesso a relatórios

MANAGER
├── Gerenciar projetos próprios e atribuídos
├── Criar/gerenciar listas e tarefas
├── Atribuir tarefas a outros usuários
└── Visualizar relatórios de equipe

USER
├── Gerenciar projetos próprios
├── Criar/gerenciar listas e tarefas
├── Comentar em tarefas
└── Fazer upload de anexos

GUEST
├── Visualizar projetos compartilhados
├── Visualizar tarefas
└── Adicionar comentários (se permitido)
```

---

## 3. Estratégias de Autorização

### 3.1 Role-Based Access Control (RBAC)

**Descrição:** Controle de acesso baseado em papéis atribuídos aos usuários.

**Implementação:**
```php
#[Entity]
#[Table(name: 'user_roles')]
class UserRole
{
    #[Id]
    #[ManyToOne(targetEntity: User::class)]
    private User $user;

    #[Id]
    #[Column(type: 'string', length: 50)]
    private string $role;

    #[Column(type: 'datetime', name: 'granted_at')]
    private DateTime $grantedAt;
}
```

**Vantagens:**
- Simples de entender e implementar
- Fácil auditoria de permissões
- Padrão amplamente utilizado

**Recomendação:** ✅ **USAR** - Base da autorização

---

## 4. Arquitetura Recomendada

### 4.1 Estrutura de Entidades

```
User
├── id: UUID
├── email: string (unique)
├── passwordHash: string
├── name: string
├── timezone: string
├── roles: UserRole[] (OneToMany)
├── projects: Project[]
└── createdAt: DateTime

UserRole
├── user: User (ManyToOne)
├── role: string (enum)
└── grantedAt: DateTime

Permission (Opcional para ACL)
├── id: int
├── user: User
├── resourceType: string
├── resourceId: string
└── actions: string[] (JSON)
```

### 4.2 Camada de Segurança

```php
// SecurityService.php
namespace App\Service\Security;

class SecurityService
{
    public function __construct(
        private UserRoleService $roleService,
        private PermissionService $permissionService,
        private SessionManager $sessionManager
    ) {}

    public function authenticate(string $email, string $password): ?User
    {
        // Implementação da autenticação
    }

    public function authorize(User $user, string $permission, ?object $resource = null): bool
    {
        // Verificação de permissão
    }

    public function hasRole(User $user, array $roles): bool
    {
        // Verificação de role
    }
}
```

### 4.3 Middleware/Controller Filter

```php
// AbstractController.php
abstract class AbstractController
{
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirectToURL('/login');
        }
    }

    protected function requireRole(array $roles): void
    {
        $this->requireAuth();

        if (!$this->securityService->hasRole($this->getCurrentUser(), $roles)) {
            $this->render('error/forbidden', [], false);
            exit;
        }
    }

    protected function can(string $action, object $resource): bool
    {
        return $this->securityService->authorize(
            $this->getCurrentUser(),
            $action,
            $resource
        );
    }
}
```

---

## 5. Implementação Passo a Passo

### Fase 1: Autenticação Básica (Prioridade Alta)

1. **Atualizar User Entity**
   - Adicionar campo `isActive`
   - Adicionar campo `lastLoginAt`
   - Implementar método `verifyPassword()`

2. **Criar AuthService**
   - Método `login()`
   - Método `logout()`
   - Método `register()`
   - Método `resetPassword()`

3. **Implementar Session Management**
   - Configurar sessões seguras
   - Implementar CSRF protection
   - Adicionar timeout de sessão

4. **Criar Middleware de Autenticação**
   - Verificar sessão em rotas protegidas
   - Redirecionar para login se não autenticado

### Fase 2: Roles e Permissões (Prioridade Média)

1. **Criar UserRole Entity**
   - Relacionamento ManyToMany com User
   - Enum de roles disponíveis

2. **Implementar RBAC**
   - Criar RoleService
   - Definir permissões por role
   - Criar decorator/attribute para controllers

3. **Adicionar Verificações nos Controllers**
   - Verificar permissões antes de ações
   - Retornar 403 Forbidden quando apropriado

### Fase 3: Permissões Granulares (Prioridade Baixa)

1. **Implementar ACL (se necessário)**
   - Criar tabela de permissões
   - Interface de gerenciamento
   - Verificações em tempo real

2. **Adicionar Compartilhamento de Projetos**
   - Convites por email
   - Níveis de acesso (viewer, editor, admin)
   - Revogação de acesso

---

## 6. Segurança

### 6.1 Senhas

- **Hash:** Use `password_hash()` com `PASSWORD_ARGON2ID`
- **Requisitos:** Mínimo 8 caracteres, maiúsculas, minúsculas, números, símbolos
- **Tentativas:** Bloquear após 5 tentativas falhas (throttling)

### 6.2 Sessões

```php
// Configuração recomendada
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', '3600'); // 1 hora
```

### 6.3 CSRF Protection

```php
// Gerar token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Verificar token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    throw new SecurityException('CSRF token invalid');
}
```

### 6.4 Headers de Segurança

```php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

---

## 7. Fluxo de Autenticação

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Login     │────▶│  Verificar  │────▶│   Criar     │
│   Form      │     │  Credenciais│     │   Sessão    │
└─────────────┘     └─────────────┘     └──────┬──────┘
                                                │
                       ┌────────────────────────┘
                       ▼
              ┌─────────────────┐
              │  Redirecionar   │
              │  para Dashboard │
              └─────────────────┘
                       │
                       ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Acesso    │◀────│  Verificar  │◀────│   Request   │
│  Permitido  │     │  Permissão  │     │   Recurso   │
└─────────────┘     └─────────────┘     └─────────────┘
```

---

## 8. Decisões de Arquitetura

| Aspecto | Decisão | Justificativa |
|---------|---------|---------------|
| Autenticação | Session-based | Simplicidade e controle |
| Autorização | RBAC + ABAC | Flexibilidade e granularidade |
| Password Hash | Argon2id | Algoritmo moderno e seguro |
| CSRF | Token por formulário | Proteção padrão web |
| Session Store | PHP Native | Suficiente para escala atual |
| Permissões | Database + Cache | Performance e persistência |

---

## 9. Bibliotecas Recomendadas

```json
{
    "require": {
        "firebase/php-jwt": "^6.0",
        "symfony/security-core": "^6.0",
        "ramsey/uuid": "^4.0"
    }
}
```

---

## 10. Checklist de Implementação

- [ ] Atualizar entidade User com campos de segurança
- [ ] Criar entidade UserRole
- [ ] Implementar AuthService
- [ ] Criar middleware de autenticação
- [ ] Implementar CSRF protection
- [ ] Adicionar verificação de roles nos controllers
- [ ] Criar página de gerenciamento de usuários (admin)
- [ ] Implementar recuperação de senha
- [ ] Adicionar logs de auditoria
- [ ] Configurar headers de segurança
- [ ] Implementar rate limiting
- [ ] Criar testes de segurança

---

## Conclusão

A arquitetura proposta combina **Session-based Authentication** com **RBAC + ABAC** para fornecer um sistema seguro, escalável e flexível. A implementação deve seguir a ordem das fases, priorizando a autenticação básica antes de adicionar recursos avançados.

Para dúvidas ou discussões sobre a implementação, consulte a equipe de arquitetura.
