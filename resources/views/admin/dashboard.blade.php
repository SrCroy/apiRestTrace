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
                        <h2 class="mb-0 fw-bold">1,250</h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="text-white text-decoration-none small">Ver listado completo <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Rutas Creadas</h6>
                        <h2 class="mb-0 fw-bold">342</h2>
                    </div>
                    <i class="bi bi-map fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="text-white text-decoration-none small">Ver todas las rutas <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Actividades Deportivas</h6>
                        <h2 class="mb-0 fw-bold">890</h2>
                    </div>
                    <i class="bi bi-bicycle fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="text-white text-decoration-none small">Ver actividades <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card bg-danger text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Reportes Pendientes</h6>
                        <h2 class="mb-0 fw-bold">12</h2>
                    </div>
                    <i class="bi bi-flag fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="text-white text-decoration-none small">Revisar reportes <i class="bi bi-arrow-right"></i></a>
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
                                <tr>
                                    <td class="px-3 fw-semibold">Carlos Pérez</td>
                                    <td><span class="badge bg-secondary">Ciclismo</span></td>
                                    <td>15 km</td>
                                    <td class="text-muted">Hace 10 min</td>
                                    <td class="text-end px-3">
                                        <button class="btn btn-sm btn-outline-primary">Detalles</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 fw-semibold">Ana Gómez</td>
                                    <td><span class="badge bg-secondary">Running</span></td>
                                    <td>5 km</td>
                                    <td class="text-muted">Hace 45 min</td>
                                    <td class="text-end px-3">
                                        <button class="btn btn-sm btn-outline-primary">Detalles</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection