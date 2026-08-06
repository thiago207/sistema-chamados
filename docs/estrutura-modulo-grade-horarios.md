# Módulo Grade de Horários — Estrutura Completa

Documento de arquitetura para implementação no projeto **Sale Marketing** (Laravel + Blade + Bootstrap + jQuery).
Sem código — apenas estrutura, decisões e ordem de execução.

---

## 1. Visão geral

O sistema passa a ter **dois módulos** dentro da mesma aplicação:

| Módulo | O que faz | Quem acessa |
|---|---|---|
| **Tarefas** | O que já existe (chamados, responsáveis, status) | papel `tarefas` e `master` |
| **Grade de Horários** | Cadastro escolar + geração automática de grade | papel `grade` e `master` |

Ao logar, o usuário cai numa **tela de seleção de módulo**. Se ele só tem acesso a um, vai direto pra ele. O `master` vê os dois e pode alternar.

A sidebar é **montada dinamicamente** conforme o módulo ativo — não é uma sidebar só com tudo dentro.

---

## 2. Multi-tenancy (as duas escolas)

Duas escolas usarão o sistema: **Auxiliadora** e **Mazzarello**, ambas em PE.

**Regra de ouro:** toda tabela do módulo grade carrega `escola_id`, e **nenhuma query roda sem filtrar por ele**.

Como garantir isso sem depender de disciplina:

- O `escola_id` do usuário logado fica na sessão no momento do login
- Cada model do módulo grade recebe um **Global Scope** — um filtro que o Eloquent aplica automaticamente em toda query daquele model, sem você escrever nada
- O `master` (que não pertence a escola nenhuma) precisa de um mecanismo pra "entrar" numa escola específica — um seletor de escola que grava `escola_ativa_id` na sessão

> Sem o global scope, basta esquecer um `where` em um controller pra vazar dado de um cliente pro outro. Com ele, o vazamento vira exceção em vez de regra.

---

## 3. Papéis de usuário

Coluna `papel` na tabela `users`, tipo `enum`:

- `master` — vê e administra os dois módulos, todas as escolas
- `tarefas` — só o módulo de tarefas
- `grade` — só o módulo de grade, e só da própria escola

Sistemas maiores usariam tabelas de roles/permissions (ex: pacote Spatie). Para 3 papéis fixos, o enum resolve e é muito mais simples. Migrar depois é tranquilo.

> **Importante:** dentro do módulo grade, o papel `grade` tem acesso completo de criação e edição nos cadastros (séries, matérias, turmas, professores, vínculos, disponibilidade, geração de grade) — não é somente leitura. `master` administra os dois módulos e todas as escolas, mas isso não significa que as ações de escrita do módulo grade sejam exclusivas dele.

---

## 4. Decisão: professor NÃO é usuário

**Tabela `professores` separada, não vinculada a `users`.**

Motivo: são conceitos diferentes.
- `users` = quem faz login no sistema
- `professores` = pessoas que são alocadas na grade

O professor não precisa de conta. Quem cadastra ele é o coordenador. Se um dia a escola quiser que professores consultem a própria grade online, adiciona-se uma coluna `user_id` nullable em `professores` — sem quebrar nada.

Misturar os dois desde o início obrigaria a criar login, senha e papel para cada professor cadastrado, o que não faz sentido.

---

## 5. Tabelas

### 5.1 `escolas`

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| nome | string | "Colégio Auxiliadora" |
| cidade | string, nullable | |
| timestamps | | |

---

### 5.2 `users` (alteração da existente)

Colunas a **adicionar**:

| Coluna | Tipo | Observação |
|---|---|---|
| escola_id | FK → escolas, **nullable** | null para o `master` |
| papel | enum(master, tarefas, grade) | default `tarefas` |

- `nullable` no `escola_id` é obrigatório: o master não pertence a escola alguma
- `default` no papel protege os usuários já cadastrados quando a migration rodar
- No `down()`: soltar a foreign key **antes** de dropar a coluna, senão o MySQL recusa

---

### 5.3 `series`

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| escola_id | FK → escolas | |
| nome | string | "6º ano", "7º ano" |
| timestamps | | |

Índice único composto: `(escola_id, nome)` — evita duas séries iguais na mesma escola.

---

### 5.4 `turmas`

É aqui que mora a configuração de grade horária (decisão "Jeito A").

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| escola_id | FK → escolas | |
| serie_id | FK → series | |
| nome | string | "A", "B" ou "Única" |
| dias_semana | json ou string | ex: `[1,2,3,4,5]` (seg a sex) |
| aulas_manha | tinyint | 0 se não tem turno manhã |
| inicio_manha | time, nullable | ex: 07:30 |
| aulas_tarde | tinyint | 0 se não tem turno tarde |
| inicio_tarde | time, nullable | ex: 13:00 |
| duracao_minutos | smallint | ex: 50 |
| timestamps | | |

**Decisão "Jeito A" confirmada:** a quantidade de aulas é a mesma para todos os dias da semana. Se na prática um dia tem menos aulas, os slots extras simplesmente ficam **em branco** na grade gerada — o gerador não força preenchimento.

**Os horários de relógio são derivados, não armazenados.** Com `inicio_manha = 07:30` e `duracao = 50`, o sistema calcula: 1ª aula 07:30–08:20, 2ª 08:20–09:10, etc. Isso deve ser um método no model `Turma` (ex: `horarioDoSlot($numero, $turno)`), nunca duplicado em cada view.

> Se no futuro precisar de intervalo/recreio, adicionar colunas `intervalo_apos_aula` e `intervalo_minutos`. Não incluído agora por não ter sido pedido.

---

### 5.5 `materias`

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| escola_id | FK → escolas | |
| nome | string | "Matemática" |
| tipo | enum(comum, eletiva, curricular) | só classificação |
| timestamps | | |

O `tipo` **não altera regra de encaixe** — é etiqueta para filtro e relatório. A carga horária real vem da tabela `turma_materia`.

---

### 5.6 `professores`

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| escola_id | FK → escolas | |
| nome | string | |
| email | string, nullable | |
| telefone | string, nullable | |
| ativo | boolean, default true | professor afastado sai da geração sem perder histórico |
| timestamps | | |

---

### 5.7 `turma_materia` — a carga horária

Define **quantas aulas de cada matéria** uma turma tem por semana. É a "lista de pendências" que o gerador precisa encaixar.

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| turma_id | FK → turmas | |
| materia_id | FK → materias | |
| quantidade_aulas | tinyint | aulas por semana |
| timestamps | | |

Índice único: `(turma_id, materia_id)` — uma matéria não pode aparecer duas vezes na mesma turma.

> Confirmado: a carga é definida **por turma**, não por série. No cadastro da turma há um select de matérias e um campo de quantidade para cada.

---

### 5.8 `professor_turma_materia` — os vínculos

Define **quem dá o quê para quem**. Um professor pode ter vários registros aqui.

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| professor_id | FK → professores | |
| turma_id | FK → turmas | |
| materia_id | FK → materias | |
| timestamps | | |

Índice único: `(professor_id, turma_id, materia_id)`

Exemplo: *João + 6A + Matemática*, *João + 6B + Matemática*, *João + 7A + Física*.

**Validação crítica:** cada linha de `turma_materia` precisa ter **exatamente um** professor vinculado em `professor_turma_materia`. Se tiver zero, o gerador não sabe quem alocar. Se tiver dois, há ambiguidade. Isso deve ser checado antes de permitir gerar a grade.

---

### 5.9 `disponibilidades`

Quando cada professor pode dar aula. **Por slot, não por relógio.**

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| professor_id | FK → professores | |
| dia_semana | tinyint | 1=segunda … 6=sábado |
| turno | enum(manha, tarde) | |
| numero_aula | tinyint | 1, 2, 3… |
| timestamps | | |

Índice único: `(professor_id, dia_semana, turno, numero_aula)`

Cada linha significa: *"o professor X está livre na 2ª aula da manhã de terça"*.

**Por que por slot e não por faixa de relógio:** o exemplo que você deu — "disponível de 7:30 às 11:00" — na prática significa "pode pegar as aulas 1, 2, 3 e 4 da manhã". Guardar o slot direto elimina conversão de horário na hora de gerar, e o encaixe vira simples comparação de números.

**Na interface:** a tela de disponibilidade deve ser uma **matriz clicável** (dias na horizontal × aulas na vertical), não um formulário de horários. O professor marca os quadradinhos.

---

### 5.10 `horarios` — o resultado

Cada linha é **uma aula encaixada na grade**.

| Coluna | Tipo | Observação |
|---|---|---|
| id | bigint PK | |
| escola_id | FK → escolas | |
| turma_id | FK → turmas | |
| materia_id | FK → materias | |
| professor_id | FK → professores | |
| dia_semana | tinyint | |
| turno | enum(manha, tarde) | |
| numero_aula | tinyint | |
| timestamps | | |

**Dois índices únicos que são a espinha dorsal da integridade:**

1. `(turma_id, dia_semana, turno, numero_aula)` — uma turma não pode ter duas aulas no mesmo slot
2. `(professor_id, dia_semana, turno, numero_aula)` — um professor não pode estar em duas turmas ao mesmo tempo

> Isso faz o **banco** garantir a regra, não só o código. Mesmo que um bug no gerador ou uma edição manual tentem criar conflito, o MySQL recusa. É a rede de segurança mais importante do módulo.

---

## 6. Relacionamentos Eloquent

| Model | Método | Tipo | Aponta para |
|---|---|---|---|
| Escola | series, turmas, materias, professores, usuarios | hasMany | respectivas |
| Serie | escola | belongsTo | Escola |
| Serie | turmas | hasMany | Turma |
| Turma | escola, serie | belongsTo | |
| Turma | materias | belongsToMany (`turma_materia`, withPivot `quantidade_aulas`) | Materia |
| Turma | horarios | hasMany | Horario |
| Materia | escola | belongsTo | |
| Materia | turmas | belongsToMany | Turma |
| Professor | escola | belongsTo | |
| Professor | disponibilidades | hasMany | Disponibilidade |
| Professor | vinculos | hasMany | ProfessorTurmaMateria |
| Professor | horarios | hasMany | Horario |
| Horario | turma, materia, professor, escola | belongsTo | |

**Sobre `professor_turma_materia`:** diferente da pivô `tarefa_user`, essa merece **model próprio** (`ProfessorTurmaMateria` ou `Vinculo`), porque você vai consultá-la diretamente — o gerador precisa iterar sobre os vínculos. Pivô sem model só funciona quando você nunca acessa a tabela sozinha.

**Casts necessários:**
- `Turma`: `dias_semana` → `array`; `inicio_manha` / `inicio_tarde` → string ou datetime conforme uso

---

## 7. Middleware (proteção de rotas)

Hoje o sistema **não tem proteção nenhuma** — qualquer URL é acessível sem login. Isso precisa ser resolvido antes de qualquer coisa nova.

Três middlewares:

| Middleware | O que faz |
|---|---|
| `Autenticado` | Barra quem não tem `usuario_id` na sessão → redireciona pro login |
| `VerificaPapel` | Recebe o papel exigido pela rota e barra quem não tem. O `master` passa sempre |
| `EscolaAtiva` | Garante que há uma escola ativa na sessão antes de acessar o módulo grade |

Aplicados por **grupo de rotas**, não rota a rota.

---

## 8. Rotas e controllers

### Estrutura de rotas

```
/                          → login
/auth/*                    → login, logout
/modulos                   → seleção de módulo (pós-login)

/tarefas/*                 → [middleware: papel:tarefas] módulo existente
/usuarios/*                → [middleware: papel:master] gestão de usuários

/grade/*                   → [middleware: papel:grade + escola.ativa]
    /escola/selecionar     → só master, troca de escola ativa
    /series                → CRUD
    /materias              → CRUD
    /turmas                → CRUD + configuração de carga horária
    /professores            → CRUD + vínculos + disponibilidade
    /gerar                 → tela de geração
    /horarios               → visualizar grade
    /horarios/editar        → ajuste manual
    /exportar/*             → PDF e Excel
```

### Controllers

| Controller | Responsabilidade |
|---|---|
| `ModuloController` | Tela de seleção de módulo, troca de escola ativa |
| `SerieController` | CRUD de séries |
| `MateriaController` | CRUD de matérias |
| `TurmaController` | CRUD de turmas + atribuição de carga horária |
| `ProfessorController` | CRUD de professores |
| `VinculoController` | Vincular professor a turma+matéria |
| `DisponibilidadeController` | Matriz de disponibilidade |
| `GradeController` | Validação de viabilidade + disparo da geração + edição manual |
| `ExportacaoController` | PDF e Excel |

### Service (fora do controller)

O algoritmo **não vai no controller**. Vai numa classe de serviço em `app/Services/`:

- `GeradorDeGradeService` — o motor de alocação
- `ValidadorDeViabilidadeService` — checagens pré-geração

Motivo: o algoritmo é a parte mais complexa do sistema. Isolado, ele pode ser testado, reescrito e melhorado sem tocar em rota, request ou view.

---

## 9. O gerador de grade — algoritmo

### 9.1 Validação de viabilidade (roda ANTES de gerar)

Nunca tentar gerar sem checar. Retornar lista de problemas legíveis:

1. **Toda linha de `turma_materia` tem professor vinculado?** Se não, listar quais faltam.
2. **A soma das cargas cabe na turma?** Somar `quantidade_aulas` de todas as matérias da turma e comparar com `dias × (aulas_manha + aulas_tarde)`. Se exceder, é impossível.
3. **Cada professor tem disponibilidade suficiente?** Somar todas as aulas que ele precisa dar (todas as turmas) e comparar com o número de slots que ele marcou como disponível.
4. **Há sobreposição impossível?** Professor com disponibilidade só em 3 slots mas com 8 aulas atribuídas.

Se qualquer uma falhar → mostrar o diagnóstico e **não gerar**. Metade dos problemas de grade são de cadastro, não de algoritmo.

### 9.2 Estruturas de trabalho

**Lista de aulas a alocar:** expandir `turma_materia` em unidades individuais.
Ex: 6A + Matemática + 5 aulas → 5 itens `{turma: 6A, materia: Mat, professor: João}`.

**Grade da turma:** matriz `dia × turno × numero_aula`, inicialmente vazia.

**Mapa de ocupação do professor:** para cada professor, quais slots ele já está ocupado.

### 9.3 Estratégia: guloso com ordenação + backtracking

**Passo 1 — Ordenar as aulas da mais restrita para a menos restrita.**
Alocar primeiro o que tem menos opções. Critério de restrição: professor com menos slots disponíveis, matéria com mais aulas semanais. Isso é a heurística *Most Constrained First* e reduz drasticamente a chance de travar no fim.

**Passo 2 — Para cada aula, buscar um slot válido.** Um slot é válido se, cumulativamente:
- A turma está livre naquele `dia + turno + numero_aula`
- O professor está livre naquele mesmo slot (em qualquer turma)
- O professor marcou disponibilidade para aquele slot
- O slot existe na configuração da turma (não passa de `aulas_manha`/`aulas_tarde`)

**Passo 3 — Preferência (não obrigação) por aulas geminadas.**
Você confirmou que matéria pode repetir no mesmo dia (ex: 3 aulas seguidas de matemática). Ao escolher entre slots válidos, **preferir o adjacente** a uma aula da mesma matéria/turma já alocada. Isso melhora a qualidade da grade sem custo de complexidade.

**Passo 4 — Backtracking quando travar.**
Se uma aula não achar slot algum, desfazer a última alocação e tentar outro caminho. Limitar profundidade e número de tentativas para não rodar infinitamente.

**Passo 5 — Relatório de não alocadas.**
Se após N tentativas restarem aulas sem slot, **gravar a grade parcial** e mostrar exatamente o que não coube e por quê ("Matemática 6A: 2 aulas não alocadas — professor João sem slots livres compatíveis"). Grade parcial + diagnóstico é muito mais útil que erro genérico.

### 9.4 Sobre "janelas" do professor

Você indicou que janelas (tempo vago no meio) são aceitáveis porque a escola paga por hora-aula. Então **não é restrição**, mas pode ser critério de desempate: entre dois slots igualmente válidos, preferir o que não cria janela. Fica como melhoria futura, não bloqueia a v1.

### 9.5 Regeneração

Gerar de novo deve **apagar os horários daquela escola/turma antes** de recriar. Envolver em transação: se a geração falhar no meio, faz rollback e a grade antiga permanece intacta.

---

## 10. Edição manual

Depois de gerada, a grade precisa ser ajustável.

- Tela em formato de **grade visual** (dias × aulas), uma turma por vez
- Drag and drop seria ideal, mas um modal de "trocar aula deste slot" já resolve a v1
- **Toda alteração revalida os conflitos** — se mover uma aula para um slot onde o professor já está ocupado, bloquear e explicar
- Os índices únicos do banco são a última linha de defesa

---

## 11. Exportação

### PDF
Pacote: `barryvdh/laravel-dompdf`

- **Por turma:** uma página, grade dias × aulas, com matéria e professor em cada célula, horário de relógio calculado
- **Geral:** todas as turmas, uma por página, com cabeçalho da escola
- **Por professor:** (bônus valioso) a grade individual de cada professor — a escola vai pedir isso

### Excel
Pacote: `maatwebsite/excel`

- Uma **aba por turma** no modo geral
- Mesma estrutura visual da grade
- Formatação: cabeçalho colorido, células mescladas para aulas geminadas

Os dois exportadores devem consumir a **mesma fonte de dados** (um método que monta a matriz da grade), para nunca divergirem entre si.

---

## 12. Telas necessárias

| Tela | Observação |
|---|---|
| Seleção de módulo | Pós-login, cards para Tarefas / Grade |
| Seletor de escola | Só master |
| CRUD Séries | Simples |
| CRUD Matérias | Com o campo tipo |
| CRUD Turmas | O mais complexo: dias, turnos, aulas por turno, duração, horário de início |
| Carga horária da turma | Select de matérias (Select2) + quantidade de cada |
| CRUD Professores | Simples |
| Vínculos do professor | Matriz ou lista: turma + matéria |
| Disponibilidade do professor | **Matriz clicável** dias × aulas, por turno |
| Painel de geração | Botão de validar → diagnóstico → botão de gerar |
| Visualização da grade | Grade visual por turma, com filtro |
| Edição manual | A partir da visualização |
| Exportação | Botões de PDF/Excel, por turma ou geral |

---

## 13. Ordem de implementação sugerida

**Fase 0 — Fundação (fazer antes de qualquer coisa nova)**
1. Middleware de autenticação — o sistema hoje está aberto
2. Migration `escolas`
3. Migration de alteração em `users` (escola_id + papel)
4. Middleware de papel + tela de seleção de módulo
5. Sidebar dinâmica por módulo

**Fase 1 — Cadastros básicos**
6. CRUD Séries
7. CRUD Matérias
8. CRUD Turmas (com toda a configuração de turno)
9. Global Scope de escola nos models

**Fase 2 — Professores**
10. CRUD Professores
11. Tela de vínculos (professor + turma + matéria)
12. Matriz de disponibilidade

**Fase 3 — Carga horária**
13. Tela de carga horária da turma

**Fase 4 — O motor**
14. `ValidadorDeViabilidadeService` + tela de diagnóstico
15. `GeradorDeGradeService` — versão gulosa simples
16. Backtracking e heurística de ordenação
17. Preferência por aulas geminadas

**Fase 5 — Resultado**
18. Visualização da grade
19. Edição manual com revalidação
20. Exportação PDF
21. Exportação Excel

> **Não pule a Fase 0.** Cadastrar tudo e descobrir depois que falta multi-tenancy significa refazer todos os controllers.

---

## 14. Pontos em aberto para decidir depois

- **Ano letivo / versionamento:** a grade de 2026 apaga a de 2025? Se a escola quiser histórico, adicionar `ano_letivo` em `turmas` e `horarios`
- **Intervalo/recreio:** não modelado. Se for necessário aparecer na grade impressa, adicionar colunas de intervalo em `turmas`
- **Professor consultando a própria grade:** exigiria `user_id` em `professores` + papel `professor`
- **Sala de aula como recurso:** se houver laboratórios/quadras disputados, vira mais uma restrição no gerador
