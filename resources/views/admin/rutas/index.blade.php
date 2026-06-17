@extends('layouts.admin')

@section('title', 'Rutas - Panel Admin')
@section('page_title', 'Listado de Rutas')

@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.rutas.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Buscar ruta</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre de ruta..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Creador</label>
                        <input type="text" name="creador" class="form-control" placeholder="Nombre del usuario..." value="{{ request('creador') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted">Deporte</label>
                        <select name="deporte" class="form-select">
                            <option value="">Todos</option>
                            @foreach($deportes as $deporte)
                                <option value="{{ $deporte }}" {{ request('deporte') == $deporte ? 'selected' : '' }}>{{ ucfirst($deporte) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted">Ordenar</label>
                        <select name="orden" class="form-select">
                            <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                            <option value="populares" {{ request('orden') == 'populares' ? 'selected' : '' }}>Más populares</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-3">Mostrando <strong>{{ $rutas->count() }}</strong> de <strong>{{ $rutas->total() }}</strong> rutas</p>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Nombre</th>
                            <th>Creador</th>
                            <th>Deporte</th>
                            <th>Distancia</th>
                            <th>Dificultad</th>
                            <th>Usos</th>
                            <th class="small">Creada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rutas as $ruta)
                        <tr>
                            <td class="px-3 text-muted">#{{ $ruta->id }}</td>
                            <td class="fw-semibold">{{ $ruta->nombre }}</td>
                            <td>
                                @if($ruta->usuario)
                                    <a href="{{ route('admin.usuarios.show', $ruta->usuario_id) }}" class="text-decoration-none">{{ $ruta->usuario->nombre }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ ucfirst($ruta->tipo_deporte) }}</span></td>
                            <td>{{ $ruta->distancia_km }} km</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $ruta->dificultad ? '-fill text-warning' : ' text-muted' }}" style="font-size:0.75rem;"></i>
                                @endfor
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $ruta->veces_usada }}</span></td>
                            <td class="text-muted small">{{ $ruta->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No se encontraron rutas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $rutas->withQueryString()->links() }}
    </div>
@endsection
