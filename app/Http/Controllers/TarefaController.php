<?php

namespace App\Http\Controllers;

// Para requests
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Tarefa;
use Illuminate\Support\Carbon;

class TarefaController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        return view('tarefas.criar', [ 'usuarios' => $usuarios ]); 
    }
    public function salvarTarefa(Request $request)
    {
        $request->validate([
            'titulo'         => 'required|max:255',
            'descricao'      => 'required',
            'status'         => 'required|in:pendente,em_andamento,concluida,cancelada,pausada',
            'prazo'          => 'nullable|date',
            'observacoes'    => 'nullable',
            'responsaveis'   => 'required|array|min:1',
            'responsaveis.*' => 'exists:users,id',
        ]);

        $tarefa = Tarefa::create([
            'titulo'      => $request->input('titulo'),
            'descricao'   => $request->input('descricao'),
            'criador_id'  => session('usuario_id'),
            'status'      => $request->input('status'),
            'prazo'       => $request->input('prazo'),
            'observacoes' => $request->input('observacoes'),
        ]);

        $tarefa->responsaveis()->attach($request->input('responsaveis'));

        return redirect('/tarefas/criar')->with('sucesso', 'Tarefa criada com sucesso!');
    }
    public function listarTarefas(Request $request)
    {
        $query = Tarefa::with('responsaveis', 'criador');

        // Busca por título ou descrição
        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%")
                ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro por responsável
        if ($request->filled('responsavel')) {
            $query->whereHas('responsaveis', function ($q) use ($request) {
                $q->where('users.id', $request->input('responsavel'));
            });
        }

        $tarefas  = $query->latest()->get();
        $usuarios = User::orderBy('name')->get();

        return view('tarefas.listar', [
            'tarefas'  => $tarefas,
            'usuarios' => $usuarios,
        ]);
    }
    public function eventos(Request $request)
    {
        $query = Tarefa::with('responsaveis')->whereNotNull('prazo');

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('prazo', [
                Carbon::parse($request->query('start'))->toDateString(),
                Carbon::parse($request->query('end'))->toDateString(),
            ]);
        }

        $cores = [
            'pendente'     => '#f0ad4e',
            'em_andamento' => '#0d6efd',
            'pausada'      => '#6c757d',
            'concluida'    => '#198754',
            'cancelada'    => '#dc3545',
        ];

        $eventos = $query->get()->map(function ($tarefa) use ($cores) {
            return [
                'id'     => $tarefa->id,
                'title'  => $tarefa->titulo,
                'start'  => $tarefa->prazo->toDateString(),
                'allDay' => true,
                'color'  => $cores[$tarefa->status] ?? '#0d6efd',
                'extendedProps' => [
                    'status'       => $tarefa->status,
                    'descricao'    => $tarefa->descricao,
                    'responsaveis' => $tarefa->responsaveis->pluck('name')->implode(', '),
                ],
            ];
        });

        return response()->json($eventos);
    }

    public function iniciar($id)
    {
        $tarefa = Tarefa::findOrFail($id);
        $tarefa->update(['status' => 'em_andamento']);

        return redirect('/tarefas')->with('sucesso', 'Tarefa iniciada!');
    }

    public function concluir(Request $request, $id)
    {
        $request->validate([
            'resolucao' => 'required|min:10',
        ]);

        $tarefa = Tarefa::findOrFail($id);
        $tarefa->update([
            'status'       => 'concluida',
            'resolucao'    => $request->input('resolucao'),
            'concluida_em' => now(),
        ]);

        return redirect('/tarefas')->with('sucesso', 'Tarefa concluída!');
    }

    public function pausar($id)
    {
        Tarefa::findOrFail($id)->update(['status' => 'pausada']);
        return redirect('/tarefas')->with('sucesso', 'Tarefa pausada.');
    }

    public function cancelar($id)
    {
        Tarefa::findOrFail($id)->update(['status' => 'cancelada']);
        return redirect('/tarefas')->with('sucesso', 'Tarefa cancelada.');
    }
    
}