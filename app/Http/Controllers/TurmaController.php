<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TurmaController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $turmas = Turma::when($busca, fn ($query) => $query->where('nome', 'like', "%{$busca}%"))
            ->withCount('materias')
            ->orderBy('nome')
            ->get();

        return view('grade.turmas.index', ['turmas' => $turmas, 'busca' => $busca]);
    }

    public function show(Turma $turma)
    {
        return view('grade.turmas.show', ['turma' => $turma]);
    }

    public function criar()
    {
        $materias = Materia::orderBy('nome')->get();

        return view('grade.turmas.criar', ['materias' => $materias]);
    }

    public function store(Request $request)
    {
        $dados = $this->validarTurma($request);

        DB::transaction(function () use ($request, $dados) {
            $turma = Turma::create($dados);
            $this->salvarMaterias($request, $turma);
        });

        return redirect('/grade/turmas')->with('sucesso', 'Série/turma cadastrada com sucesso!');
    }

    public function editar(Turma $turma)
    {
        $turma->load('materias');
        $materias = Materia::orderBy('nome')->get();

        return view('grade.turmas.editar', ['turma' => $turma, 'materias' => $materias]);
    }

    public function update(Request $request, Turma $turma)
    {
        $dados = $this->validarTurma($request, $turma);

        DB::transaction(function () use ($request, $dados, $turma) {
            $turma->update($dados);
            $this->salvarMaterias($request, $turma);
        });

        return redirect('/grade/turmas')->with('sucesso', 'Série/turma atualizada com sucesso!');
    }

    public function destroy(Turma $turma)
    {
        $turma->delete();

        return redirect('/grade/turmas')->with('sucesso', 'Série/turma excluída com sucesso!');
    }


    public function duplicar(Turma $turma)
    {
        $turma->load('materias');

        $nova = DB::transaction(function () use ($turma) {
            $copia = $turma->replicate();
            $copia->nome = $this->nomeDuplicadoDisponivel($turma->nome);
            $copia->save();

            $sync = $turma->materias->mapWithKeys(fn ($materia) => [
                $materia->id => ['quantidade_aulas' => $materia->pivot->quantidade_aulas],
            ])->all();
            $copia->materias()->sync($sync);

            return $copia;
        });

        return redirect("/grade/turmas/{$nova->id}/editar")
            ->with('sucesso', "Duplicada a partir de \"{$turma->nome}\". Ajuste o nome e o que for preciso.");
    }

    private function nomeDuplicadoDisponivel(string $nomeBase): string
    {
        $nome = "{$nomeBase} (cópia)";
        $contador = 2;

        while (Turma::where('nome', $nome)->exists()) {
            $nome = "{$nomeBase} (cópia {$contador})";
            $contador++;
        }

        return $nome;
    }

    private function validarTurma(Request $request, ?Turma $turma = null): array
    {
        $temManha = $request->boolean('tem_manha');
        $temTarde = $request->boolean('tem_tarde');

        if (! $temManha && ! $temTarde) {
            throw ValidationException::withMessages([
                'turno' => 'Selecione ao menos um turno (manhã ou tarde).',
            ]);
        }

        $regras = [
            'nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('turmas')->where('escola_id', session('escola_ativa_id'))->ignore($turma?->id),
            ],
            'dias_semana' => 'required|array|min:1',
            'dias_semana.*' => 'integer|between:1,6',
            'duracao_minutos' => 'required|integer|min:10|max:180',
        ];

        if ($temManha) {
            $regras['aulas_manha'] = 'required|integer|min:1|max:20';
            $regras['inicio_manha'] = 'required|date_format:H:i';
        }

        if ($temTarde) {
            $regras['aulas_tarde'] = 'required|integer|min:1|max:20';
            $regras['inicio_tarde'] = 'required|date_format:H:i';
        }

        $dados = $request->validate($regras);

        $dados['aulas_manha'] = $temManha ? $dados['aulas_manha'] : 0;
        $dados['inicio_manha'] = $temManha ? $dados['inicio_manha'] : null;
        $dados['aulas_tarde'] = $temTarde ? $dados['aulas_tarde'] : 0;
        $dados['inicio_tarde'] = $temTarde ? $dados['inicio_tarde'] : null;

        return $dados;
    }


    private function salvarMaterias(Request $request, Turma $turma): void
    {
        $dados = $request->validate([
            'materias' => 'nullable|array',
            'materias.*' => 'exists:materias,id',
            'quantidades' => 'nullable|array',
            'quantidades.*' => 'integer|min:1|max:20',
        ]);

        $materiaIds = $dados['materias'] ?? [];
        $quantidades = $dados['quantidades'] ?? [];

        $total = collect($materiaIds)->sum(fn ($id) => (int) ($quantidades[$id] ?? 0));

        if ($total > $turma->totalSlotsSemanais()) {
            throw ValidationException::withMessages([
                'materias' => "A soma das aulas das disciplinas ({$total}) excede o total de slots semanais da série/turma ({$turma->totalSlotsSemanais()}).",
            ]);
        }

        $sync = collect($materiaIds)->mapWithKeys(fn ($id) => [
            (int) $id => ['quantidade_aulas' => (int) ($quantidades[$id] ?? 0)],
        ])->all();

        $turma->materias()->sync($sync);
    }
}
