@extends('layouts.admin')

@section('title', 'Actividades - Panel Admin')
@section('page_title', 'Listado de Actividades')

@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.actividades.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Buscar</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Título o usuario..." value="{{ request('buscar') }}">
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
                        <label class="form-label fw-semibold small text-muted">Desde</label>
                        <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted">Hasta</label>
                        <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        <a href="{{ route('admin.actividades.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-3">Mostrando <strong>{{ $actividades->count() }}</strong> de <strong>{{ $actividades->total() }}</strong> actividades</p>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Título</th>
                            <th>Usuario</th>
                            <th>Deporte</th>
                            <th>Distancia</th>
                            <th>Duración</th>
                            <th>Calorías</th>
                            <th class="small">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actividades as $act)
                        <tr>
                            <td class="px-3 text-muted">#{{ $act->id }}</td>
                            <td class="fw-semibold">{{ $act->titulo ?? 'Sin título' }}</td>
                            <td>
                                @if($act->usuario)
                                    <a href="{{ route('admin.usuarios.show', $act->id_usuario) }}" class="text-decoration-none">{{ $act->usuario->nombre }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ ucfirst($act->tipo_deporte) }}</span></td>
                            <td>{{ $act->distancia_km }} km</td>
                            <td>
                                @php
                                    $h = floor($act->duracion_seg / 3600);
                                    $m = floor(($act->duracion_seg % 3600) / 60);
                                @endphp
                                {{ $h > 0 ? $h.'h ' : '' }}{{ $m }}min
                            </td>
                            <td>{{ number_format($act->calorias, 0) }} kcal</td>
                            <td class="text-muted small">{{ $act->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No se encontraron actividades.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $actividades->withQueryString()->links() }}
    </div>
@endsection
