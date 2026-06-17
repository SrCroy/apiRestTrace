<?php

namespace App\Http\Controllers;

use App\Models\actividades;
use App\Models\reporte;
use App\Models\rutas;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $data = [
            'totalUsuarios'   => User::count(),
            'totalRutas'      => rutas::count(),
            'totalActividades' => actividades::count(),
            'totalReportes'    => reporte::where('estado', 'pendiente')->count(), 
            'ultimasActividades' => actividades::latest()->take(5)->get()
        ];

        return view('admin.dashboard', $data);
    }
}
