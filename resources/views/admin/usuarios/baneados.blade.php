@extends('layouts.admin')

@section('title', 'Usuarios Baneados - Panel Admin')
@section('page_title', 'Usuarios Baneados')

@section('content')
    {{-- Info --}}
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <i class="bi bi-info-circle me-2"></i>
        Listado de usuarios que han sido baneados del sistema. Puedes desbanear usuarios individualmente.
    </div>

    {{-- Búsqueda --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.usuarios.baneados') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small text-muted">Buscar baneado</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="buscar" class="form-control" placeholder="Nombre, email o username..." value="{{ request('buscar') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Contador --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0"><strong>{{ $baneados->total() }}</strong> usuarios baneados</p>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Fecha Registro</th>
                            <th class="text-end px-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($baneados as $usuario)
                        <tr>
                            <td class="px-3 text-muted">#{{ $usuario->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:0.85rem;">
                                        {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                        <small class="text-muted">{{ '@' . $usuario->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td class="text-muted small">{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('admin.usuarios.show', $usuario->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-eye"></i></a>
                                <form action="{{ route('admin.usuarios.desbanear', $usuario->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Desbanear a este usuario?')">
                                        <i class="bi bi-unlock"></i> Desbanear
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4"><i class="bi bi-emoji-smile fs-4 d-block mb-2"></i>No hay usuarios baneados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $baneados->withQueryString()->links() }}
    </div>
@endsection
