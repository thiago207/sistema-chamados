<?php

namespace App\Http\Controllers;

// Para requests
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Tarefa;

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