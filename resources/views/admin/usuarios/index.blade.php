@extends('layouts.admin')

@section('title', 'Usuarios - Panel Admin')
@section('page_title', 'Listado de Usuarios')

@section('content')
    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.usuarios.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">Buscar usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="buscar" class="form-control" placeholder="Nombre, email o username..." value="{{ request('buscar') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="baneado" {{ request('estado') == 'baneado' ? 'selected' : '' }}>Baneados</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Fecha registro</label>
                        <select name="fecha" class="form-select">
                            <option value="">Todas</option>
                            <option value="hoy" {{ request('fecha') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                            <option value="semana" {{ request('fecha') == 'semana' ? 'selected' : '' }}>Última semana</option>
                            <option value="mes" {{ request('fecha') == 'mes' ? 'selected' : '' }}>Último mes</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Contador --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Mostrando <strong>{{ $usuarios->count() }}</strong> de <strong>{{ $usuarios->total() }}</strong> usuarios</p>
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
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th class="text-end px-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td class="px-3 text-muted">#{{ $usuario->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $usuario->avatar) }}" 
                                         alt="Avatar de {{ $usuario->nombre }}" 
                                         class="rounded-circle me-2" 
                                         style="width:35px;height:35px;object-fit:cover;"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle bg-primary text-white align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:0.85rem;display:none;">
                                        {{ strtoupper(substr($usuario->nombre ?? $usuario->username ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                        <small class="text-muted">{{ '@' . $usuario->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if($usuario->rol == 'admin')
                                    <span class="badge bg-warning text-dark">Admin</span>
                                @elseif($usuario->rol == 'moderador')
                                    <span class="badge bg-info">Moderador</span>
                                @else
                                    <span class="badge bg-secondary">Usuario</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->esta_baneado)
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Baneado</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('admin.usuarios.show', $usuario->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No se encontraron usuarios.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $usuarios->withQueryString()->links() }}
    </div>
@endsection
