<?php

namespace App\Http\Controllers;

// Para requests
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hash de senha

// Models
use App\Models\User;
use App\Models\Tarefa;

class MenuController extends Controller
{
    public function index()
    {
        $resumo = [
            'pendentes'  => Tarefa::where('status', 'pendente')->count(),
            'concluidas' => Tarefa::where('status', 'concluida')->count(),
            'canceladas' => Tarefa::where('status', 'cancelada')->count(),
            'atrasadas'  => Tarefa::where('status', 'pendente')
                ->whereDate('data', '<', now())
                ->count(),
        ];

        return view('menu.index', ['resumo' => $resumo]);
    }

}
?>