# Instruções para o Claude Code — Módulo Grade de Horários

Guia de prompts prontos para construir o módulo passo a passo, sem perder o controle do que está sendo gerado.

---

## Como usar este documento

1. Salve este arquivo e o `estrutura-modulo-grade-horarios.md` numa pasta `docs/` dentro do projeto
2. **Uma fase por conversa.** Não peça tudo de uma vez
3. Ao iniciar cada sessão, cole o **Bloco de Contexto** (seção 1) antes do prompt da fase
4. Revise, teste e commite antes de passar para a próxima fase

> **Por que não pedir tudo de uma vez:** o Claude Code vai gerar dezenas de arquivos. Você não consegue revisar esse volume, e quando algo quebrar não vai saber em qual etapa. Uma fase por vez, testando entre elas, é mais lento por mensagem e muito mais rápido no total.

---

## 1. Bloco de Contexto

**Cole isto no início de toda sessão nova do Claude Code:**

```
Estou trabalhando num projeto Laravel existente chamado Sale Marketing.
Leia docs/estrutura-modulo-grade-horarios.md antes de começar — ele tem a
modelagem completa do módulo que vamos construir.

CONTEXTO DO PROJETO EXISTENTE:
- Laravel 12, PHP 8.2, MySQL (banco: sale_marketing)
- Frontend: Blade + Bootstrap 5.3 (via CDN) + Bootstrap Icons + jQuery 3.7 (via CDN)
- Select2 já usado em campos de seleção múltipla
- CSS e JS ficam em public/css/ e public/js/, carregados com asset().
  O projeto NÃO usa Vite. A pasta resources/css e resources/js não é usada.
- Layouts: resources/views/layouts/app.blade.php (sidebar lateral colapsável,
  topbar, @yield('content') e @yield('scripts')) e layouts/guest.blade.php (login)
- Autenticação é MANUAL, feita à mão: o login busca o usuário com
  User::where('email',...)->first(), compara com Hash::check(), e grava
  session(['usuario_id' => ..., 'usuario_nome' => ...]).
  NÃO uso o sistema Auth do Laravel, nem Breeze, nem Jetstream.
- Rotas em routes/web.php, organizadas com Route::prefix('x')->group()
- Views em resources/views/<modulo>/<acao>.blade.php
- Cores da identidade: azul #1a3a6b (primário) e vermelho #c0392b (ação)
- Todo texto de interface em português do Brasil

REGRAS INVIOLÁVEIS:
1. NÃO instale Breeze, Jetstream, Fortify ou qualquer scaffolding de auth
2. NÃO use Vite, Tailwind ou npm. Bootstrap e jQuery vêm por CDN
3. NÃO use Auth::user(), auth()->user() ou o middleware 'auth' do Laravel.
   Use session('usuario_id')
4. NUNCA rode migrate:fresh ou migrate:refresh sem me perguntar antes
5. Todo formulário POST/PUT/DELETE precisa de @csrf e @method quando aplicável
6. Toda entrada de usuário passa por $request->validate()
7. Todo model precisa de $fillable
8. CSS e JS novos vão em arquivos separados em public/, nunca inline na view

COMO QUERO TRABALHAR:
Explique o que vai fazer e por quê ANTES de escrever o código.
Estou aprendendo Laravel — venho de CodeIgniter 3 — então quando usar
um recurso do Laravel que não existe no CI3, explique o conceito.
Ao terminar, resuma o que mudou e o que eu devo testar.
```

---

## 2. FASE 0 — Fundação (fazer primeiro, sem exceção)

> O sistema hoje não tem proteção nenhuma de rota. Qualquer URL é acessível sem login. Se você cadastrar escolas e turmas antes disso, vai reescrever todos os controllers depois.

### Prompt 0.1 — Proteção de rotas

```
Preciso proteger as rotas do sistema. Hoje qualquer pessoa acessa
/tarefas ou /usuarios digitando a URL, mesmo sem estar logada.

Crie um middleware chamado Autenticado que:
- Verifica se existe session('usuario_id')
- Se não existir, redireciona para '/' com uma mensagem de erro
- Se existir, deixa passar

Registre ele no bootstrap/app.php com um alias, e aplique nos grupos de
rotas de /tarefas e /usuarios em routes/web.php.
As rotas '/' e '/auth/login' devem continuar públicas.

Antes de escrever, me explique o que é um middleware no Laravel e onde
ele se encaixa no ciclo da requisição.
```

### Prompt 0.2 — Escolas e papéis

```
Vamos criar a base do multi-tenancy e dos papéis de usuário.

1. Migration create_escolas_table:
   - id, nome (string), cidade (string nullable), timestamps

2. Migration de ALTERAÇÃO em users (a tabela já existe e já rodou):
   - escola_id: foreignId nullable, FK para escolas, onDelete cascade
   - papel: enum('master','tarefas','grade'), default 'tarefas'
   - No down(): dropForeign antes de dropColumn

3. Model Escola com $fillable

4. Adicione escola_id e papel ao $fillable do model User

Não rode migrate ainda — quero revisar os arquivos primeiro.
```

### Prompt 0.3 — Controle de acesso por papel

```
Agora o controle de acesso por módulo.

1. Middleware VerificaPapel que recebe o papel exigido como parâmetro.
   Regras: quem tem papel 'master' passa sempre; os demais só passam se
   o papel bater; quem não passa vê uma tela de acesso negado.

2. Rota /modulos com uma tela de seleção de módulo (cards para "Tarefas"
   e "Grade de Horários"), mostrando apenas os módulos que o usuário
   logado pode acessar.

3. Após o login bem-sucedido, gravar também session(['usuario_papel' => ...])
   e session(['usuario_escola_id' => ...]), e redirecionar para /modulos
   em vez de /menu.

4. Se o usuário só tem acesso a um módulo, redirecionar direto para ele.
```

### Prompt 0.4 — Sidebar dinâmica

```
A sidebar em layouts/app.blade.php hoje é fixa. Preciso que ela mude
conforme o módulo ativo.

- Gravar session('modulo_ativo') quando o usuário escolhe um módulo
- A sidebar renderiza os itens do módulo ativo:
    * Módulo Tarefas: Tarefas (registrar/listar), Cadastro (usuários)
    * Módulo Grade: Séries, Matérias, Turmas, Professores, Gerar Grade,
      Horários, Exportar
- Se o usuário é master, mostrar um item para trocar de módulo
- Manter o comportamento de colapsar e os submenus com collapse do Bootstrap

Separe a sidebar num partial (resources/views/partials/sidebar.blade.php)
para não inchar o layout.
```

---

## 3. FASE 1 — Todas as migrations do módulo

> Esta é a fase que você pediu para começar. Só faça depois da Fase 0.

### Prompt 1.1 — As migrations

```
Crie as migrations do módulo de grade de horários. Todas as tabelas de
uma vez, na ordem correta de dependência (os timestamps dos nomes de
arquivo precisam respeitar a ordem, pois há foreign keys entre elas).

Ordem: series, materias, professores, turmas, turma_materia,
professor_turma_materia, disponibilidades, horarios.

=== series ===
id
escola_id      → FK escolas, cascade
nome           → string ("6º ano")
timestamps
unique(escola_id, nome)

=== materias ===
id
escola_id      → FK escolas, cascade
nome           → string
tipo           → enum('comum','eletiva','curricular'), default 'comum'
timestamps
unique(escola_id, nome)

=== professores ===
id
escola_id      → FK escolas, cascade
nome           → string
email          → string nullable
telefone       → string nullable
ativo          → boolean default true
timestamps

=== turmas ===
id
escola_id        → FK escolas, cascade
serie_id         → FK series, cascade
nome             → string ("A", "B", "Única")
dias_semana      → json  (ex: [1,2,3,4,5] onde 1=segunda)
aulas_manha      → tinyint unsigned, default 0
inicio_manha     → time nullable
aulas_tarde      → tinyint unsigned, default 0
inicio_tarde     → time nullable
duracao_minutos  → smallint unsigned, default 50
timestamps
unique(serie_id, nome)

=== turma_materia ===
id
turma_id          → FK turmas, cascade
materia_id        → FK materias, cascade
quantidade_aulas  → tinyint unsigned
timestamps
unique(turma_id, materia_id)

=== professor_turma_materia ===
id
professor_id  → FK professores, cascade
turma_id      → FK turmas, cascade
materia_id    → FK materias, cascade
timestamps
unique(professor_id, turma_id, materia_id)

=== disponibilidades ===
id
professor_id  → FK professores, cascade
dia_semana    → tinyint unsigned (1=segunda ... 6=sábado)
turno         → enum('manha','tarde')
numero_aula   → tinyint unsigned
timestamps
unique(professor_id, dia_semana, turno, numero_aula)

=== horarios ===
id
escola_id     → FK escolas, cascade
turma_id      → FK turmas, cascade
materia_id    → FK materias, cascade
professor_id  → FK professores, cascade
dia_semana    → tinyint unsigned
turno         → enum('manha','tarde')
numero_aula   → tinyint unsigned
timestamps

DOIS ÍNDICES ÚNICOS OBRIGATÓRIOS em horarios:
- unique(turma_id, dia_semana, turno, numero_aula)
  → uma turma não pode ter duas aulas no mesmo slot
- unique(professor_id, dia_semana, turno, numero_aula)
  → um professor não pode estar em duas turmas ao mesmo tempo

Esses dois índices são a garantia de integridade da grade no nível do
banco. Não os omita.

Todos os down() devem dropar as tabelas na ordem inversa.
Não rode migrate ainda.
```

### Prompt 1.2 — Os models

```
Agora os models de todas as tabelas criadas, com $fillable, $casts e
relacionamentos.

RELACIONAMENTOS:

Escola:      hasMany series, turmas, materias, professores, usuarios
Serie:       belongsTo escola | hasMany turmas
Materia:     belongsTo escola | belongsToMany turmas (via turma_materia,
             withPivot quantidade_aulas)
Professor:   belongsTo escola | hasMany disponibilidades, vinculos, horarios
Turma:       belongsTo escola, serie | hasMany horarios
             belongsToMany materias (via turma_materia,
             withPivot quantidade_aulas)
Horario:     belongsTo escola, turma, materia, professor
Disponibilidade: belongsTo professor
Vinculo (professor_turma_materia): belongsTo professor, turma, materia

IMPORTANTE: professor_turma_materia precisa de MODEL PRÓPRIO (chame de
Vinculo, com $table = 'professor_turma_materia'), porque o gerador vai
consultar essa tabela diretamente. Diferente de uma pivô pura.

CASTS:
Turma: dias_semana → array

MÉTODOS AUXILIARES na Turma:
- totalSlotsSemanais(): dias × (aulas_manha + aulas_tarde)
- horarioDoSlot($turno, $numeroAula): calcula o horário de relógio a
  partir de inicio_manha/inicio_tarde + duracao_minutos.
  Ex: inicio 07:30, duração 50, aula 2 → "08:20 às 09:10"
  Esse cálculo NÃO pode ser duplicado nas views.

Depois de criar tudo, rode as migrations e confirme no banco.
```

### Prompt 1.3 — Isolamento por escola

```
Agora o isolamento multi-tenant. Preciso garantir que um usuário da
escola A nunca veja dados da escola B, mesmo que eu esqueça um where
em algum controller.

1. Crie um Global Scope chamado EscolaScope que filtra automaticamente
   por escola_id usando o valor da sessão

2. Aplique nos models: Serie, Materia, Professor, Turma, Horario

3. O usuário master não pertence a escola nenhuma. Ele precisa
   "entrar" numa escola: crie uma rota que grava
   session('escola_ativa_id') e um seletor de escola na interface

4. O scope deve usar session('escola_ativa_id') quando existir,
   caindo para session('usuario_escola_id') caso contrário

5. Crie um middleware EscolaAtiva que barra o acesso ao módulo grade
   se não houver escola definida na sessão

Me explique o que é um Global Scope e como ele difere de simplesmente
escrever o where em cada query.
```

---

## 4. FASE 2 — Cadastros básicos

### Prompt 2.1

```
CRUD completo de Séries e Matérias, seguindo o padrão que já existe no
módulo de usuários do projeto (veja app/Http/Controllers/UsuarioController.php
e resources/views/usuarios/ como referência de estilo).

Para cada um: listar (com filtro de busca), criar, editar via modal,
excluir com confirmação.

Rotas sob o prefixo /grade.
Matéria tem o campo tipo (comum/eletiva/curricular) exibido como badge
colorido na listagem.
```

### Prompt 2.2 — Turmas (a tela mais complexa)

```
CRUD de Turmas. Esta é a tela mais complexa dos cadastros porque
configura a grade horária.

Campos do formulário:
- Série (select das séries cadastradas)
- Nome da turma (ex: "A", "B", "Única")
- Dias da semana: checkboxes de segunda a sábado → salva como array json
- Turno manhã: checkbox "tem aula de manhã" que, quando marcado, revela
  os campos "quantidade de aulas" e "horário de início"
- Turno tarde: mesma lógica
- Duração de cada aula em minutos (default 50)

Validações:
- Pelo menos um turno precisa estar ativo
- Pelo menos um dia da semana selecionado
- Se um turno está ativo, quantidade de aulas e horário de início são
  obrigatórios

Na listagem, mostrar a série, o nome, os dias e um resumo dos turnos
(ex: "Manhã: 6 aulas a partir das 07:30").

O JavaScript de mostrar/esconder os campos de turno vai em
public/js/turmas.js, seguindo o padrão de public/js/usuarios.js.

Na tela de detalhes da turma, mostre a grade de horários vazia que a
configuração gera (dias × aulas com os horários de relógio calculados),
para o usuário conferir se configurou certo.
```

### Prompt 2.3 — Carga horária

```
Tela de carga horária da turma: define quantas aulas por semana cada
matéria tem naquela turma. É o que alimenta o gerador.

- Acessível a partir da listagem de turmas
- Select2 múltiplo para escolher as matérias
- Para cada matéria escolhida, um campo numérico de quantidade de aulas
- Ao salvar, usar sync() na relação belongsToMany com o withPivot

VALIDAÇÃO IMPORTANTE: a soma das quantidades não pode exceder
totalSlotsSemanais() da turma. Mostre em tempo real (jQuery) o total
alocado vs o total disponível, com destaque visual quando estourar.

Ao final me explique a diferença entre attach(), sync() e syncWithoutDetaching()
no contexto de pivô com dados extras.
```

---

## 5. FASE 3 — Professores

### Prompt 3.1

```
CRUD de Professores (nome, email opcional, telefone opcional, ativo).
Mesmo padrão dos outros cadastros.

Na listagem, mostrar quantos vínculos e quantos slots de disponibilidade
cada professor tem, para dar visibilidade de quem está incompleto.
```

### Prompt 3.2 — Vínculos

```
Tela de vínculos do professor: define o que ele leciona e para quem.

- A partir da listagem de professores, botão "Vínculos"
- Adicionar vínculo: select de turma + select de matéria
- O select de matéria deve mostrar apenas matérias que aquela turma tem
  na carga horária (carregado por AJAX ao escolher a turma)
- Listar os vínculos existentes com opção de remover
- Impedir vínculo duplicado

ALERTA VISUAL: se a combinação turma+matéria já tem outro professor
vinculado, avisar antes de salvar — cada matéria de cada turma deve ter
exatamente um professor.
```

### Prompt 3.3 — Disponibilidade

```
Tela de disponibilidade do professor. É uma MATRIZ CLICÁVEL, não um
formulário de horários.

- Eixo horizontal: dias da semana
- Eixo vertical: números de aula, separados por turno (manhã e tarde)
- Cada célula é um toggle: clicar marca/desmarca disponível
- Visual claro: verde = disponível, cinza = indisponível
- Botões de atalho: "marcar dia inteiro", "marcar turno inteiro",
  "limpar tudo"
- Salvar tudo de uma vez: apagar as disponibilidades antigas do professor
  e inserir as marcadas, dentro de uma transação

A quantidade de linhas da matriz vem da maior configuração de turno entre
as turmas em que o professor tem vínculo.

Mostre também, acima da matriz, quantas aulas o professor precisa dar no
total (soma dos vínculos) versus quantos slots ele marcou — para ele
perceber na hora se marcou de menos.

JavaScript em public/js/disponibilidade.js.
```

---

## 6. FASE 4 — O gerador

### Prompt 4.1 — Validação de viabilidade

```
Antes do algoritmo de geração, o validador. A maior parte dos problemas
de grade é erro de cadastro, não de algoritmo.

Crie app/Services/ValidadorDeViabilidadeService.php que verifica, para
uma escola, e retorna uma lista de problemas legíveis em português:

1. Toda linha de turma_materia tem exatamente um professor vinculado?
   Listar as que estão sem professor ou com mais de um.
2. Para cada turma: a soma das quantidade_aulas cabe em
   totalSlotsSemanais()? Listar as que estouram e por quanto.
3. Para cada professor: o total de aulas que ele precisa dar (somando
   todas as turmas) é menor ou igual ao número de slots de
   disponibilidade que ele marcou? Listar os deficitários.
4. Toda turma tem pelo menos uma matéria com carga definida?
5. Todo professor com vínculo tem alguma disponibilidade cadastrada?

Crie também a tela /grade/gerar que roda essa validação e exibe o
diagnóstico. O botão "Gerar Grade" só fica habilitado se não houver
nenhum problema bloqueante.
```

### Prompt 4.2 — O algoritmo

```
Agora o motor: app/Services/GeradorDeGradeService.php

ESTRUTURAS DE TRABALHO:
- Lista de aulas a alocar: expandir turma_materia em unidades individuais.
  Ex: 6A + Matemática + 5 aulas → 5 itens {turma, materia, professor}
  (o professor vem da tabela de vínculos)
- Grade de cada turma: matriz dia × turno × numero_aula, vazia
- Mapa de ocupação de cada professor: quais slots ele já ocupa

ALGORITMO — guloso com ordenação e backtracking:

Passo 1 — Ordenar as aulas da mais restrita para a menos restrita
(heurística Most Constrained First): primeiro as de professores com
menos slots disponíveis, depois as matérias com mais aulas semanais.
Isso reduz muito a chance de travar no final.

Passo 2 — Para cada aula, procurar um slot válido. Um slot só é válido
se TODAS as condições forem verdadeiras:
  a) A turma está livre nesse dia+turno+numero_aula
  b) O professor está livre nesse mesmo slot (em qualquer turma)
  c) O professor marcou disponibilidade para esse slot
  d) O slot existe na configuração da turma (não passa de aulas_manha
     ou aulas_tarde, e o dia está em dias_semana)

Passo 3 — Preferência por aulas geminadas: entre os slots válidos,
preferir o adjacente a uma aula da mesma matéria+turma já alocada.
Matéria PODE repetir no mesmo dia, inclusive várias seguidas.

Passo 4 — Backtracking: se uma aula não encontrar nenhum slot, desfazer
a última alocação e tentar outro caminho. Limitar profundidade e número
de tentativas para não rodar indefinidamente.

Passo 5 — Se sobrarem aulas não alocadas após o limite, GRAVAR A GRADE
PARCIAL mesmo assim e retornar um relatório explicando o que não coube
e por quê. Ex: "Matemática 6A: 2 aulas não alocadas — professor João sem
slots livres compatíveis".

REGENERAÇÃO: apagar os horários existentes da escola antes de recriar,
tudo dentro de uma transação (DB::transaction). Se falhar no meio, o
rollback preserva a grade anterior.

Janelas na grade do professor (tempo vago no meio) são PERMITIDAS —
não são restrição. Se quiser, use como critério de desempate entre
slots igualmente válidos.

Explique o algoritmo antes de implementar, e comente o código em
português nos pontos de decisão.
```

---

## 7. FASE 5 — Visualização, edição e exportação

### Prompt 5.1 — Visualizar e editar

```
Tela de visualização da grade gerada:
- Seletor de turma
- Grade visual: dias na horizontal, aulas na vertical, separada por turno
- Cada célula mostra a matéria e o professor
- Horários de relógio nas laterais (usar horarioDoSlot da Turma)
- Células vazias visualmente distintas
- Cores por matéria ajudam a leitura

Edição manual:
- Clicar numa célula abre um modal para trocar/remover a aula
- O modal mostra apenas as opções VÁLIDAS: matérias da turma que ainda
  têm aulas a alocar, e apenas com professores livres naquele slot
- Ao salvar, revalidar todos os conflitos antes de gravar
- Se o usuário tentar criar conflito, bloquear e explicar qual é

Crie também uma visualização por PROFESSOR (a grade individual dele) —
a escola vai pedir isso.
```

### Prompt 5.2 — Exportação

```
Exportação da grade.

PDF (pacote barryvdh/laravel-dompdf):
- Por turma: uma página com a grade completa
- Geral: todas as turmas, uma por página, com cabeçalho da escola
- Por professor: a grade individual de cada professor

Excel (pacote maatwebsite/excel):
- Modo geral: uma aba por turma
- Mesma estrutura visual da grade
- Cabeçalho colorido, células mescladas em aulas geminadas

IMPORTANTE: os dois exportadores devem consumir a MESMA fonte de dados.
Crie um método único que monta a matriz da grade (ex: um service
MontadorDeGradeService) e use nos dois. Assim eles nunca divergem.

Me avise antes de rodar composer require, e explique o que cada pacote faz.
```

---

## 8. Checklist entre fases

Antes de passar para a próxima fase, confirme:

- [ ] Rodou as migrations e conferiu as tabelas no DBeaver
- [ ] Testou o fluxo na interface, não só o código
- [ ] Fez commit no Git com mensagem descritiva
- [ ] Nenhum arquivo novo em `resources/css` ou `resources/js`
- [ ] Nenhum pacote instalado sem você ter aprovado
- [ ] O `.env` não foi alterado sem você saber

---

## 9. Se o Claude Code sair do padrão

Prompts de correção prontos:

**Se ele usar Auth::user() ou o middleware auth:**
```
Este projeto não usa o sistema Auth do Laravel. A autenticação é manual
via session('usuario_id'). Refaça usando a sessão.
```

**Se ele criar CSS ou JS inline na view:**
```
Neste projeto CSS e JS ficam em arquivos separados em public/css e
public/js, carregados com asset(). Mova o código e ajuste a view.
```

**Se ele instalar algo sem avisar:**
```
Não instale pacotes sem me perguntar antes. Desfaça e me explique o que
esse pacote faria e se existe alternativa sem dependência nova.
```

**Se ele gerar muito código de uma vez:**
```
Está grande demais para eu revisar. Divida em partes menores e faça uma
de cada vez, esperando eu confirmar entre elas.
```
