# Sale Marketing

Sistema web para gestão interna de escolas, construído em Laravel. Dividido em módulos independentes, acessíveis conforme o papel do usuário.

---

## Módulos

| Módulo | Descrição | Estado |
|---|---|---|
| **Tarefas** | Registro e acompanhamento de tarefas/chamados entre membros da equipe | Funcional |
| **Grade de Horários** | Cadastro escolar e geração automática da grade de aulas | Em desenvolvimento |

Ao entrar, o usuário escolhe um módulo — ou vai direto ao seu, se tiver acesso a apenas um. O usuário **master** acessa os dois e administra o sistema.

---

## Stack

- Laravel 12 / PHP 8.2
- MySQL (banco `sale_marketing`)
- Blade + Bootstrap 5.3 + Bootstrap Icons + jQuery 3.7 (via CDN)
- Select2 para seleção múltipla
- Local: MySQL do XAMPP + `php artisan serve`
- Produção: Hostinger (deploy via Git/SSH)

O projeto não usa Vite, Tailwind nem npm. CSS/JS ficam em `public/` e são carregados com `asset()`. Autenticação é manual via sessão, sem Breeze/Jetstream/Fortify.

---

## Módulo Tarefas

Gerenciamento de tarefas/chamados entre membros da equipe.

- CRUD de usuários, com edição via modal, senha opcional na edição, campo de confirmar senha dinâmico e botão de revelar senha
- Registro de tarefas com múltiplos responsáveis (relação N:N)
- Fluxo de status: pendente, em andamento, concluída (com registro de resolução), pausada e cancelada
- Ações contextuais conforme o status atual da tarefa
- Filtros por título/descrição, status e responsável
- Modal de detalhes e destaque visual de tarefas atrasadas

Tabelas: `users`, `tarefas`, `tarefa_user`.

---

## Módulo Grade de Horários

Automatiza a montagem da grade de horários escolar. O coordenador cadastra escolas, séries, turmas, matérias e professores com suas disponibilidades, e o sistema gera a grade encaixando as aulas sem conflito de professor ou de turma. A grade pode ser editada manualmente e exportada em PDF e Excel, por turma ou geral.

- Multi-tenant: cada escola só enxerga os próprios dados
- Cadastro de séries, matérias (comum/curricular/eletiva) e turmas com configuração de turnos e horários
- Carga horária definida por turma (matérias e número de aulas de cada)
- Professores com vínculos (turma + matéria) e disponibilidade por horário
- Geração automática da grade com validação de viabilidade prévia
- Edição manual com revalidação de conflitos
- Exportação em PDF e Excel

Documentação de projeto na pasta `docs/`:
- `contexto-projeto-sale-marketing.md`
- `estrutura-modulo-grade-horarios.md`
- `instrucoes-claude-code-grade.md`
- `resumo-matriz-curricular.md`

Status: modelagem concluída; implementação a iniciar.

---

## Instalação (local)

```bash
git clone <url-do-repositorio> sale-marketing
cd sale-marketing
composer install
cp .env.example .env
php artisan key:generate
```

Configurar o `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sale_marketing
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
```

Criar o banco:

```sql
CREATE DATABASE sale_marketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Rodar migrations e subir o servidor:

```bash
php artisan migrate
php artisan serve
```

Acessar `http://localhost:8000`.

O primeiro usuário é criado pelo Tinker (o cadastro de usuários é interno):

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

## Estrutura

```
app/
├── Http/Controllers/    # AuthController, UsuarioController, TarefaController...
├── Models/              # User, Tarefa (e os models do módulo grade)
└── Services/            # Lógica de negócio pesada (ex: gerador de grade)

resources/views/
├── layouts/             # app (interno) e guest (login)
├── login/
├── usuarios/
└── tarefas/

public/
├── css/                 # estilos por página
├── js/                  # scripts por página
└── img/                 # logo.jpg

routes/
└── web.php              # rotas agrupadas por Route::prefix()
```

---

## Deploy (produção)

Hospedado na Hostinger, atualizado via Git:

```bash
# Local
git add .
git commit -m "descrição"
git push

# Servidor (via SSH)
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan view:clear
```

No `.env` de produção:

```
APP_ENV=production
APP_DEBUG=false
```

---

## Licença

Projeto privado. Uso restrito às escolas clientes.
