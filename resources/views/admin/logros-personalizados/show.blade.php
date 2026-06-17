@extends('layouts.admin')

@section('title', 'Detalle de Logro - Panel Admin')
@section('page_title', 'Detalle del Logro')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.logros.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-trophy fs-4 text-warning me-2"></i>
                        <h5 class="mb-0 fw-bold">{{ $logro->nombre }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Descripción:</div>
                        <div class="col-sm-8">{{ $logro->descripcion ?? 'Sin descripción' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Tipo disparador:</div>
                        <div class="col-sm-8"><span class="badge bg-secondary">{{ ucfirst($logro->tipo_disparador) }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Valor disparador:</div>
                        <div class="col-sm-8">{{ $logro->valor_disparador }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Estado:</div>
                        <div class="col-sm-8">
                            @if($logro->estado == 'pendiente')
                                <span class="badge bg-warning text-dark px-3 py-2">Pendiente de revisión</span>
                            @elseif($logro->estado == 'aprobado')
                                <span class="badge bg-success px-3 py-2">Aprobado</span>
                            @else
                                <span class="badge bg-danger px-3 py-2">Rechazado</span>
                            @endif
                        </div>
                    </div>
                    @if($logro->comentario_revision)
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Comentario revisión:</div>
                        <div class="col-sm-8">{{ $logro->comentario_revision }}</div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Creado:</div>
                        <div class="col-sm-8">{{ $logro->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Usuarios que lo ganaron:</div>
                        <div class="col-sm-8"><span class="badge bg-info text-dark fs-6">{{ $logro->usuariosLogros->count() }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Propuesto por</h6>
                </div>
                <div class="card-body text-center">
                    @if($logro->propuestor)
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width:50px;height:50px;">
                            {{ strtoupper(substr($logro->propuestor->nombre ?? 'U', 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ $logro->propuestor->nombre }}</h6>
                        <small class="text-muted">{{ $logro->propuestor->email }}</small>
                    @else
                        <p class="text-muted">Sistema</p>
                    @endif
                </div>
            </div>

            @if($logro->estado == 'pendiente')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.logros.aprobar', $logro->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Aprobar este logro?')">
                            <i class="bi bi-check-circle me-1"></i> Aprobar
                        </button>
                    </form>
                    <form action="{{ route('admin.logros.rechazar', $logro->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('¿Rechazar este logro?')">
                            <i class="bi bi-x-circle me-1"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
