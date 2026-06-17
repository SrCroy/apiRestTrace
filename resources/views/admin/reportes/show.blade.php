@extends('layouts.admin')

@section('title', 'Detalle de Reporte - Panel Admin')
@section('page_title', 'Detalle del Reporte #' . $reporte->id)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-flag me-2"></i>Información del Reporte</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Motivo:</div>
                        <div class="col-sm-8">{{ $reporte->motivo }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Detalles:</div>
                        <div class="col-sm-8">{{ $reporte->detalles ?? 'Sin detalles adicionales' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Tipo reportado:</div>
                        <div class="col-sm-8"><span class="badge bg-secondary">{{ class_basename($reporte->reportable_tipo) }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">ID del elemento:</div>
                        <div class="col-sm-8">#{{ $reporte->reportable_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Estado actual:</div>
                        <div class="col-sm-8">
                            @if($reporte->estado == 'pendiente')
                                <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
                            @elseif($reporte->estado == 'resuelto')
                                <span class="badge bg-success px-3 py-2">Resuelto</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Descartado</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Fecha:</div>
                        <div class="col-sm-8">{{ $reporte->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Reportador</h6>
                </div>
                <div class="card-body text-center">
                    @if($reporte->reportador)
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width:50px;height:50px;">
                            {{ strtoupper(substr($reporte->reportador->nombre ?? 'U', 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ $reporte->reportador->nombre }}</h6>
                        <small class="text-muted">{{ $reporte->reportador->email }}</small>
                    @else
                        <p class="text-muted">Usuario no disponible</p>
                    @endif
                </div>
            </div>

            @if($reporte->estado == 'pendiente')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reportes.resolver', $reporte->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Marcar como resuelto?')">
                            <i class="bi bi-check-circle me-1"></i> Marcar Resuelto
                        </button>
                    </form>
                    <form action="{{ route('admin.reportes.descartar', $reporte->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-secondary w-100" onclick="return confirm('¿Descartar este reporte?')">
                            <i class="bi bi-x-circle me-1"></i> Descartar
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
