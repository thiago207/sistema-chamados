# Sale Marketing

Sistema web para a gestão interna de escolas, construído em **Laravel**.
Nasceu como um gerenciador de tarefas para a equipe de marketing e evoluiu
para uma plataforma multi-módulo, usada por escolas de Pernambuco.

---

## Visão geral

O sistema é dividido em **módulos independentes**, acessíveis conforme o
papel do usuário:

| Módulo | O que faz | Estado |
|---|---|---|
| **Tarefas** | Registro e acompanhamento de chamados/tarefas entre membros da equipe | Funcional |
| **Grade de Horários** | Cadastro escolar e geração automática da grade de aulas | Em desenvolvimento |

Ao entrar, o usuário escolhe um módulo (ou vai direto ao seu, se só tiver
acesso a um). Um usuário **master** enxerga os dois e administra o sistema.

---

## Stack

- **Laravel 12** / PHP 8.2
- **MySQL** (banco `sale_marketing`)
- **Blade** + **Bootstrap 5.3** + **Bootstrap Icons** + **jQuery 3.7** (via CDN)
- **Select2** para seleção múltipla
- Ambiente local: MySQL do **XAMPP** + servidor via `php artisan serve`
- Produção: **Hostinger** (deploy via Git/SSH)

> O projeto **não usa Vite, Tailwind nem npm**. Todo CSS/JS fica em `public/`
> e é carregado com `asset()`. Autenticação é **manual** (via sessão), sem
> Breeze/Jetstream/Fortify.

---

## Requisitos

- PHP 8.2 ou superior
- Composer
- MySQL 8 (ou MariaDB 10.11+)
- Extensões PHP padrão do Laravel (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON)

---

## Instalação (ambiente local)

```bash
# 1. Clonar o repositório
git clone <url-do-repositorio> sale-marketing
cd sale-marketing

# 2. Instalar dependências
composer install

# 3. Configurar o ambiente
cp .env.example .env
php artisan key:generate
```

Edite o `.env` com os dados do banco:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sale_marketing
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
```

Crie o banco (no DBeaver, phpMyAdmin ou linha de comando):

```sql
CREATE DATABASE sale_marketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Rode as migrations e suba o servidor:

```bash
php artisan migrate
php artisan serve
```

Acesse `http://localhost:8000`.

### Criar o primeiro usuário

Como o cadastro de usuários é interno (feito de dentro do sistema), o
primeiro usuário é criado manualmente pelo Tinker:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@admin.com',
    'password' => bcrypt('12345678'),
]);
```

---

## Estrutura do projeto

```
app/
├── Http/Controllers/    # AuthController, UsuarioController, TarefaController...
├── Models/              # User, Tarefa (e os models do módulo grade)
└── Services/            # Lógica de negócio pesada (ex: gerador de grade)

resources/views/
├── layouts/
│   ├── app.blade.php    # Layout interno (sidebar + topbar)
│   └── guest.blade.php  # Layout do login
├── login/
├── usuarios/
└── tarefas/

public/
├── css/                 # Estilos por página
├── js/                  # Scripts por página
└── img/                 # logo.jpg

routes/
└── web.php              # Rotas agrupadas por Route::prefix()
```

---

## Convenções de código

Decisões consolidadas que todo código novo deve seguir:

- **Autenticação manual:** login via `User::where('email',...)->first()` +
  `Hash::check()`, sessão via `session(['usuario_id' => ...])`. Nunca
  `Auth::user()` nem o middleware `auth`.
- **CSS e JS** sempre em arquivos separados em `public/`, carregados com
  `asset()`. Nunca inline na view.
- **Scripts de página** entram via `@section('scripts')`, que cai no
  `@yield('scripts')` do layout (depois do jQuery).
- Todo model tem `$fillable`. Toda entrada passa por `$request->validate()`.
  Todo form POST/PUT/DELETE tem `@csrf` e `@method` quando aplicável.
- Datas convertidas via `$casts` no model.
- Relacionamentos no model; `with()` para evitar N+1.
- Feedback via `->with('sucesso', ...)` + `@if(session('sucesso'))`.
- Interface toda em português do Brasil.
- Identidade visual: azul `#1a3a6b`, vermelho `#c0392b`.

---

## Módulo Tarefas

Gerenciamento de tarefas/chamados entre membros da equipe.

**Funcionalidades:**
- CRUD de usuários (com edição via modal, senha opcional, campo "confirmar
  senha" dinâmico e botão de revelar senha)
- Registro de tarefas com múltiplos responsáveis (relação N:N)
- Fluxo de status: pendente → em andamento → concluída (com registro de
  resolução), além de pausada e cancelada
- Ações contextuais por status (só aparece o que faz sentido para o estado atual)
- Filtros de busca por título, status e responsável
- Modal de detalhes e destaque de tarefas atrasadas

**Tabelas:** `users`, `tarefas`, `tarefa_user` (pivô de responsáveis).

---

## Módulo Grade de Horários

Automatiza a montagem da grade de horários escolar. O coordenador cadastra
escolas, séries, turmas, matérias e professores (com suas disponibilidades),
e o sistema **gera a grade** encaixando as aulas sem conflito de professor
ou de turma. A grade pode ser editada manualmente e exportada em PDF/Excel.

**Multi-tenant:** cada escola (cliente) só enxerga os próprios dados, isolados
por `escola_id` e um global scope.

**Documentação de projeto** (na pasta `docs/`):
- `contexto-projeto-sale-marketing.md` — contexto completo do projeto
- `estrutura-modulo-grade-horarios.md` — modelagem, tabelas, algoritmo do gerador
- `instrucoes-claude-code-grade.md` — plano de implementação, fase a fase
- `resumo-matriz-curricular.md` — dados reais de matérias e cargas horárias

**Status:** modelagem concluída; implementação a iniciar pela fundação
(proteção de rotas, escolas, papéis e multi-tenancy).

---

## Comandos úteis

```bash
php artisan serve                 # sobe o servidor local
php artisan migrate               # roda migrations pendentes
php artisan migrate:rollback      # desfaz a última leva de migrations
php artisan migrate:status        # lista migrations e seu estado
php artisan make:model Nome -m    # cria model + migration
php artisan make:controller Nome  # cria controller
php artisan route:list            # lista todas as rotas
php artisan tinker                # console interativo
```

> **Nunca** rodar `migrate:fresh` ou `migrate:refresh` em produção — eles
> apagam todos os dados. Em produção, toda mudança de estrutura é feita com
> migration de alteração.

---

## Deploy (produção)

Hospedado na Hostinger, atualizado via Git. Fluxo:

```bash
# Local
git add .
git commit -m "descrição"
git push

# No servidor (via SSH)
git pull
composer install --no-dev --optimize-autoloader   # se mudaram dependências
php artisan migrate --force                         # se há migrations novas
php artisan config:clear
php artisan view:clear
```

No `.env` de produção, garantir:

```
APP_ENV=production
APP_DEBUG=false
```

---

## Licença

Projeto privado. Uso restrito às escolas clientes.
