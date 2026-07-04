<?php

namespace App\Http\Controllers;

// Para requests
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hash de senha

// Models
use App\Models\User;

class MenuController extends Controller
{
    public function index()
    {
        return view('menu.index');
    }
    
}
?>