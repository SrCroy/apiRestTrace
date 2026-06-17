@extends('layouts.admin')

@section('title', 'Logros Personalizados - Panel Admin')
@section('page_title', 'Logros Personalizados')

@section('content')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.logros.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">Buscar</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre del logro..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                            <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobados</option>
                            <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazados</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-1"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        <a href="{{ route('admin.logros.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-3"><strong>{{ $logros->total() }}</strong> logros personalizados</p>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th>Nombre</th>
                            <th>Propuesto por</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end px-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logros as $logro)
                        <tr>
                            <td class="px-3 text-muted">#{{ $logro->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-trophy fs-5 text-warning me-2"></i>
                                    <span class="fw-semibold">{{ $logro->nombre }}</span>
                                </div>
                            </td>
                            <td>
                                @if($logro->propuestor)
                                    <a href="{{ route('admin.usuarios.show', $logro->propuesto_por) }}" class="text-decoration-none">{{ $logro->propuestor->nombre }}</a>
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ ucfirst($logro->tipo_disparador) }}</span></td>
                            <td>{{ $logro->valor_disparador }}</td>
                            <td>
                                @if($logro->estado == 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($logro->estado == 'aprobado')
                                    <span class="badge bg-success">Aprobado</span>
                                @else
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $logro->created_at->format('d/m/Y') }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('admin.logros.show', $logro->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay logros personalizados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $logros->withQueryString()->links() }}
    </div>
@endsection
