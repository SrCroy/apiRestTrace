@extends('layouts.admin')

@section('title', 'Dashboard - Resumen General')
@section('page_title', 'Resumen del Sistema')

@section('content')
    <div class="row mb-4">
        
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Usuarios Totales</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalUsuarios) }}</h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.usuarios.index') }}" class="text-white text-decoration-none small">Ver listado completo <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Rutas Creadas</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalRutas) }}</h2>
                    </div>
                    <i class="bi bi-map fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.rutas.index') }}" class="text-white text-decoration-none small">Ver todas las rutas <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Actividades Deportivas</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalActividades) }}</h2>
                    </div>
                    <i class="bi bi-bicycle fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.actividades.index') }}" class="text-white text-decoration-none small">Ver actividades <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-danger text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Reportes Pendientes</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalReportes) }}</h2>
                    </div>
                    <i class="bi bi-flag fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.reportes.index') }}" class="text-white text-decoration-none small">Revisar reportes <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Últimas Actividades Registradas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3">Usuario</th>
                                    <th>Deporte</th>
                                    <th>Distancia</th>
                                    <th>Fecha</th>
                                    <th class="text-end px-3">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasActividades as $actividad)
                                <tr>
                                    <td class="px-3 fw-semibold">{{ $actividad->usuario->nombre ?? 'N/A' }} {{ $actividad->usuario->apellido ?? '' }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($actividad->tipo_deporte) }}</span></td>
                                    <td>{{ $actividad->distancia_km }} km</td>
                                    <td class="text-muted">{{ $actividad->created_at->diffForHumans() }}</td>
                                    <td class="text-end px-3">
                                        <a href="{{ route('admin.actividades.index') }}" class="btn btn-sm btn-outline-primary">Detalles</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay actividades registradas aún.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection