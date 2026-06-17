@extends('layouts.admin')

@section('title', 'Reportes - Panel Admin')
@section('page_title', 'Reportes de Usuarios')

@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                            <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>Resueltos</option>
                            <option value="descartado" {{ request('estado') == 'descartado' ? 'selected' : '' }}>Descartados</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">Motivo</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Buscar por motivo..." value="{{ request('motivo') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-3"><strong>{{ $reportes->total() }}</strong> reportes encontrados</p>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Reportador</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end px-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportes as $reporte)
                        <tr>
                            <td class="px-3 text-muted">#{{ $reporte->id }}</td>
                            <td>
                                @if($reporte->reportador)
                                    <a href="{{ route('admin.usuarios.show', $reporte->reportador_id) }}" class="text-decoration-none">{{ $reporte->reportador->nombre }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ class_basename($reporte->reportable_tipo) }}</span></td>
                            <td class="fw-semibold">{{ Str::limit($reporte->motivo, 40) }}</td>
                            <td>
                                @if($reporte->estado == 'pendiente')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pendiente</span>
                                @elseif($reporte->estado == 'resuelto')
                                    <span class="badge bg-success"><i class="bi bi-check me-1"></i>Resuelto</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x me-1"></i>Descartado</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $reporte->created_at->format('d/m/Y') }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('admin.reportes.show', $reporte->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay reportes.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $reportes->withQueryString()->links() }}
    </div>
@endsection
