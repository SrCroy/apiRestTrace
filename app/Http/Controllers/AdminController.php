<?php

namespace App\Http\Controllers;

use App\Models\actividades;
use App\Models\logros_personalizados;
use App\Models\reporte;
use App\Models\rutas;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ─── Dashboard ───────────────────────────────
    public function index()
    {
        $data = [
            'totalUsuarios'   => User::count(),
            'totalRutas'      => rutas::count(),
            'totalActividades' => actividades::count(),
            'totalReportes'    => reporte::where('estado', 'pendiente')->count(), 
            'ultimasActividades' => actividades::with('usuario')->latest()->take(5)->get()
        ];

        return view('admin.dashboard', $data);
    }

    // ─── Usuarios ────────────────────────────────
    public function usuarios(Request $request)
    {
        $query = User::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('username', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado == 'baneado') {
                $query->where('esta_baneado', true);
            } elseif ($request->estado == 'activo') {
                $query->where('esta_baneado', false);
            }
        }

        if ($request->filled('fecha')) {
            switch ($request->fecha) {
                case 'hoy':
                    $query->whereDate('created_at', today());
                    break;
                case 'semana':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'mes':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        $usuarios = $query->latest()->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function usuarioShow($id)
    {
        $usuario = User::with(['rutas', 'actividades', 'reportesEnviados'])->findOrFail($id);
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function baneados(Request $request)
    {
        $query = User::where('esta_baneado', true);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('username', 'like', "%{$buscar}%");
            });
        }

        $baneados = $query->latest()->paginate(15);

        return view('admin.usuarios.baneados', compact('baneados'));
    }

    public function banear($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['esta_baneado' => true]);

        return back()->with('success', "Usuario {$usuario->nombre} ha sido baneado.");
    }

    public function desbanear($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['esta_baneado' => false]);

        return back()->with('success', "Usuario {$usuario->nombre} ha sido desbaneado.");
    }

    public function actualizarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'nullable|string|max:255',
            'username'         => 'required|string|max:255|unique:users,username,' . $id,
            'email'            => 'required|email|max:255|unique:users,email,' . $id,
            'peso_kg'          => 'nullable|numeric|min:0',
            'altura_cm'        => 'nullable|numeric|min:0',
            'fecha_nacimiento' => 'nullable|date',
            'biografia'        => 'nullable|string|max:500',
        ]);

        $usuario->update($validated);

        return back()->with('success', "Perfil de {$usuario->nombre} actualizado correctamente.");
    }

    public function cambiarRol(Request $request, $id)
    {
        // Solo el admin puede cambiar roles
        if (auth()->user()->rol !== 'admin') {
            return back()->with('error', 'Solo el administrador puede cambiar roles.');
        }

        $usuario = User::findOrFail($id);

        $request->validate([
            'rol' => 'required|in:admin,moderador,usuario',
        ]);

        $usuario->update(['rol' => $request->rol]);

        $roles = ['admin' => 'Administrador', 'moderador' => 'Moderador', 'usuario' => 'Usuario'];

        return back()->with('success', "{$usuario->nombre} ahora tiene el rol de {$roles[$request->rol]}.");
    }

    public function resetPassword($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['password' => bcrypt('password123')]);

        return back()->with('success', "Contraseña de {$usuario->nombre} reseteada a 'password123'.");
    }

    public function eliminarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $nombre = $usuario->nombre . ' ' . $usuario->apellido;
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', "Usuario {$nombre} eliminado permanentemente.");
    }

    // ─── Rutas ───────────────────────────────────
    public function rutas(Request $request)
    {
        $query = rutas::with('usuario');

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%");
        }

        if ($request->filled('creador')) {
            $creador = $request->creador;
            $query->whereHas('usuario', function ($q) use ($creador) {
                $q->where('nombre', 'like', "%{$creador}%")
                  ->orWhere('apellido', 'like', "%{$creador}%");
            });
        }

        if ($request->filled('deporte')) {
            $query->where('tipo_deporte', $request->deporte);
        }

        switch ($request->input('orden', 'recientes')) {
            case 'populares':
                $query->orderByDesc('veces_usada');
                break;
            default:
                $query->latest();
                break;
        }

        $rutas = $query->paginate(15);
        $deportes = rutas::select('tipo_deporte')->distinct()->pluck('tipo_deporte')->filter();

        return view('admin.rutas.index', compact('rutas', 'deportes'));
    }

    // ─── Actividades ─────────────────────────────
    public function actividades(Request $request)
    {
        $query = actividades::with('usuario');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhereHas('usuario', function ($q2) use ($buscar) {
                      $q2->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('deporte')) {
            $query->where('tipo_deporte', $request->deporte);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $actividades = $query->latest()->paginate(15);
        $deportes = actividades::select('tipo_deporte')->distinct()->pluck('tipo_deporte')->filter();

        return view('admin.actividades.index', compact('actividades', 'deportes'));
    }

    // ─── Reportes ────────────────────────────────
    public function reportes(Request $request)
    {
        $query = reporte::with('reportador');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('motivo')) {
            $query->where('motivo', 'like', "%{$request->motivo}%");
        }

        $reportes = $query->latest()->paginate(15);

        return view('admin.reportes.index', compact('reportes'));
    }

    public function reporteShow($id)
    {
        $reporte = reporte::with(['reportador'])->findOrFail($id);
        return view('admin.reportes.show', compact('reporte'));
    }

    public function reporteResolver($id)
    {
        $reporte = reporte::findOrFail($id);
        $reporte->update([
            'estado'      => 'resuelto',
            'revisado_por' => auth()->id(),
            'revisado_en'  => now(),
        ]);

        return back()->with('success', 'Reporte marcado como resuelto.');
    }

    public function reporteDescartar($id)
    {
        $reporte = reporte::findOrFail($id);
        $reporte->update([
            'estado'      => 'descartado',
            'revisado_por' => auth()->id(),
            'revisado_en'  => now(),
        ]);

        return back()->with('success', 'Reporte descartado.');
    }

    // ─── Logros Personalizados ───────────────────
    public function logros(Request $request)
    {
        $query = logros_personalizados::with('propuestor');

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $logros = $query->latest()->paginate(15);

        return view('admin.logros-personalizados.index', compact('logros'));
    }

    public function logroShow($id)
    {
        $logro = logros_personalizados::with(['propuestor', 'usuariosLogros'])->findOrFail($id);
        return view('admin.logros-personalizados.show', compact('logro'));
    }

    public function logroAprobar($id)
    {
        $logro = logros_personalizados::findOrFail($id);
        $logro->update([
            'estado'       => 'aprobado',
            'revisado_por' => auth()->id(),
            'revisado_en'  => now(),
        ]);

        return back()->with('success', "Logro '{$logro->nombre}' aprobado.");
    }

    public function logroRechazar($id)
    {
        $logro = logros_personalizados::findOrFail($id);
        $logro->update([
            'estado'       => 'rechazado',
            'revisado_por' => auth()->id(),
            'revisado_en'  => now(),
        ]);

        return back()->with('success', "Logro '{$logro->nombre}' rechazado.");
    }
}
