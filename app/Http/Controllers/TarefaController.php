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
            'data'           => 'required|date',
            'observacoes'    => 'nullable',
            'responsaveis'   => 'required|array|min:1',
            'responsaveis.*' => 'exists:users,id',
        ]);

        $tarefa = Tarefa::create([
            'titulo'      => $request->input('titulo'),
            'descricao'   => $request->input('descricao'),
            'criador_id'  => session('usuario_id'),
            'status'      => 'pendente',
            'data'        => $request->input('data'),
            'observacoes' => $request->input('observacoes'),
        ]);

        $tarefa->responsaveis()->attach($request->input('responsaveis'));

        return redirect('/tarefas/criar')->with('sucesso', 'Tarefa criada com sucesso!');
    }
    public function show($id)
    {
        $tarefa = Tarefa::with('responsaveis', 'criador')->findOrFail($id);

        return view('tarefas.show', ['tarefa' => $tarefa]);
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
        $query = Tarefa::with('responsaveis')->whereNotNull('data');

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('data', [
                Carbon::parse($request->query('start'))->toDateString(),
                Carbon::parse($request->query('end'))->toDateString(),
            ]);
        }

        $cores = [
            'pendente'  => '#f0ad4e',
            'concluida' => '#198754',
            'cancelada' => '#dc3545',
        ];

        $eventos = $query->get()->map(function ($tarefa) use ($cores) {
            return [
                'id'     => $tarefa->id,
                'title'  => $tarefa->titulo,
                'start'  => $tarefa->data->toDateString(),
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

    public function concluir($id)
    {
        Tarefa::findOrFail($id)->update([
            'status'       => 'concluida',
            'concluida_em' => now(),
        ]);

        return redirect()->back()->with('sucesso', 'Tarefa concluída!');
    }

    public function cancelar($id)
    {
        Tarefa::findOrFail($id)->update(['status' => 'cancelada']);
        return redirect()->back()->with('sucesso', 'Tarefa cancelada.');
    }

}