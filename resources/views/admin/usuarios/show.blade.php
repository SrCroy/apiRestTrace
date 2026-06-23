@extends('layouts.admin')

@section('title', 'Detalle de Usuario - Panel Admin')
@section('page_title', 'Detalle de Usuario')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver al listado</a>
    </div>

    <div class="row">
        {{-- Info principal --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $usuario->avatar) }}" 
                         alt="Avatar de {{ $usuario->nombre }}" 
                         class="rounded-circle mx-auto mb-3 d-block" 
                         style="width:80px;height:80px;object-fit:cover;"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="rounded-circle bg-primary text-white align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;font-size:2rem;display:none;">
                        {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-0">{{ $usuario->nombre }} {{ $usuario->apellido }}</h5>
                    <p class="text-muted">{{ '@' . $usuario->username }}</p>
                    
                    @if($usuario->esta_baneado)
                        <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i> Baneado</span>
                    @else
                        <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i> Activo</span>
                    @endif

                    <hr>
                    <div class="text-start">
                        <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $usuario->email }}</p>
                        <p class="mb-2"><i class="bi bi-calendar me-2 text-muted"></i>Registrado: {{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-2"><i class="bi bi-person-badge me-2 text-muted"></i>Rol: 
                            @if($usuario->rol == 'admin')
                                <span class="badge bg-warning text-dark">Admin</span>
                            @elseif($usuario->rol == 'moderador')
                                <span class="badge bg-info">Moderador</span>
                            @else
                                <span class="badge bg-secondary">Usuario</span>
                            @endif
                        </p>
                        @if($usuario->biografia)
                            <p class="mb-2"><i class="bi bi-chat-quote me-2 text-muted"></i>{{ $usuario->biografia }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="col-lg-8 mb-4">
            <div class="row mb-4">
                <div class="col-sm-4 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-map fs-3 text-success"></i>
                            <h3 class="fw-bold mb-0 mt-2">{{ $usuario->rutas->count() }}</h3>
                            <small class="text-muted">Rutas creadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-bicycle fs-3 text-info"></i>
                            <h3 class="fw-bold mb-0 mt-2">{{ $usuario->actividades->count() }}</h3>
                            <small class="text-muted">Actividades</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="bi bi-flag fs-3 text-danger"></i>
                            <h3 class="fw-bold mb-0 mt-2">{{ $usuario->reportesEnviados->count() }}</h3>
                            <small class="text-muted">Reportes enviados</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Editar Perfil --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editForm">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse" id="editForm">
                    <div class="card-body">
                        <form action="{{ route('admin.usuarios.actualizar', $usuario->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Nombre</label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $usuario->nombre) }}">
                                    @error('nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Apellido</label>
                                    <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $usuario->apellido) }}">
                                    @error('apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $usuario->username) }}">
                                        @error('username')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}">
                                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Peso (kg)</label>
                                    <input type="number" step="0.1" name="peso_kg" class="form-control" value="{{ old('peso_kg', $usuario->peso_kg) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Altura (cm)</label>
                                    <input type="number" step="0.1" name="altura_cm" class="form-control" value="{{ old('altura_cm', $usuario->altura_cm) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted">Fecha Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-muted">Biografía</label>
                                    <textarea name="biografia" class="form-control" rows="3">{{ old('biografia', $usuario->biografia) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Acciones de administración --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-shield-exclamation me-2"></i>Acciones de Administración</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Botón para abrir modal de cambiar rol (SOLO visible para admin) --}}
                        @if(auth()->user()->rol == 'admin')
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCambiarRol">
                                <i class="bi bi-shield-check me-1"></i> Cambiar Rol
                            </button>
                        @endif

                        {{-- Banear / Desbanear --}}
                        @if($usuario->esta_baneado)
                            <form action="{{ route('admin.usuarios.desbanear', $usuario->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" onclick="return confirm('¿Desbanear a este usuario?')">
                                    <i class="bi bi-unlock me-1"></i> Desbanear
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.usuarios.banear', $usuario->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Banear a este usuario?')">
                                    <i class="bi bi-person-x me-1"></i> Banear
                                </button>
                            </form>
                        @endif

                        {{-- Resetear contraseña --}}
                        <form action="{{ route('admin.usuarios.resetPassword', $usuario->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-info" onclick="return confirm('¿Resetear la contraseña a \"password123\"? El usuario deberá cambiarla.')">
                                <i class="bi bi-key me-1"></i> Resetear Contraseña
                            </button>
                        </form>
                    </div>

                    <hr>

                    {{-- Eliminar usuario --}}
                    <form action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST" 
                          onsubmit="return confirm('⚠️ ACCIÓN IRREVERSIBLE ⚠️\n\n¿Estás seguro de eliminar a {{ $usuario->nombre }} {{ $usuario->apellido }}?\n\nSe eliminarán todos sus datos permanentemente.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Eliminar Usuario Permanentemente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Cambiar Rol de Usuario                                  --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->rol == 'admin')
    <div class="modal fade" id="modalCambiarRol" tabindex="-1" aria-labelledby="modalCambiarRolLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalCambiarRolLabel">
                            <i class="bi bi-shield-check me-2 text-warning"></i>Cambiar Rol
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Selecciona el nuevo rol para <strong>{{ $usuario->nombre }} {{ $usuario->apellido }}</strong></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-3">
                    <form action="{{ route('admin.usuarios.cambiarRol', $usuario->id) }}" method="POST" id="formCambiarRol">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="rol" id="rolSeleccionado" value="{{ $usuario->rol }}">

                        <div class="d-flex flex-column gap-2">
                            {{-- Opción: Usuario --}}
                            <div class="rol-option card border-2 {{ $usuario->rol == 'usuario' ? 'border-primary' : 'border-light' }}" 
                                 data-rol="usuario" 
                                 style="cursor:pointer;transition:all 0.2s ease;">
                                <div class="card-body d-flex align-items-center py-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width:45px;height:45px;background:#e9ecef;">
                                        <i class="bi bi-person fs-5 text-secondary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">Usuario</h6>
                                        <small class="text-muted">Acceso estándar a la app. Sin permisos de panel.</small>
                                    </div>
                                    <div class="rol-check">
                                        @if($usuario->rol == 'usuario')
                                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Opción: Moderador --}}
                            <div class="rol-option card border-2 {{ $usuario->rol == 'moderador' ? 'border-info' : 'border-light' }}" 
                                 data-rol="moderador" 
                                 style="cursor:pointer;transition:all 0.2s ease;">
                                <div class="card-body d-flex align-items-center py-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width:45px;height:45px;background:#cff4fc;">
                                        <i class="bi bi-shield-check fs-5 text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">Moderador</h6>
                                        <small class="text-muted">Puede gestionar usuarios, reportes y logros. No asigna roles.</small>
                                    </div>
                                    <div class="rol-check">
                                        @if($usuario->rol == 'moderador')
                                            <i class="bi bi-check-circle-fill text-info fs-5"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Opción: Administrador --}}
                            <div class="rol-option card border-2 {{ $usuario->rol == 'admin' ? 'border-warning' : 'border-light' }}" 
                                 data-rol="admin" 
                                 style="cursor:pointer;transition:all 0.2s ease;">
                                <div class="card-body d-flex align-items-center py-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width:45px;height:45px;background:#fff3cd;">
                                        <i class="bi bi-shield-fill-exclamation fs-5 text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">Administrador</h6>
                                        <small class="text-muted">Control total del sistema. Puede asignar roles a otros.</small>
                                    </div>
                                    <div class="rol-check">
                                        @if($usuario->rol == 'admin')
                                            <i class="bi bi-check-circle-fill text-warning fs-5"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4" id="btnConfirmarRol" disabled>
                        <i class="bi bi-check-lg me-1"></i> Confirmar Cambio
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const opciones = document.querySelectorAll('.rol-option');
    const inputRol = document.getElementById('rolSeleccionado');
    const btnConfirmar = document.getElementById('btnConfirmarRol');
    const formCambiarRol = document.getElementById('formCambiarRol');
    const rolActual = '{{ $usuario->rol }}';

    const colores = {
        'usuario': { border: 'primary', check: 'primary' },
        'moderador': { border: 'info', check: 'info' },
        'admin': { border: 'warning', check: 'warning' }
    };

    opciones.forEach(function(opcion) {
        opcion.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'translateX(4px)';
                this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
            }
        });
        opcion.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = '';
                this.style.boxShadow = '';
            }
        });

        opcion.addEventListener('click', function() {
            const rol = this.dataset.rol;
            inputRol.value = rol;

            // Limpiar todas las opciones
            opciones.forEach(function(o) {
                o.classList.remove('selected');
                o.className = o.className.replace(/border-\w+/g, 'border-light');
                o.style.transform = '';
                o.style.boxShadow = '';
                const check = o.querySelector('.rol-check');
                check.innerHTML = '<i class="bi bi-circle text-muted fs-5"></i>';
            });

            // Marcar la seleccionada
            const color = colores[rol];
            this.classList.add('selected');
            this.className = this.className.replace(/border-light/g, 'border-' + color.border);
            this.style.transform = 'translateX(4px)';
            this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
            const check = this.querySelector('.rol-check');
            check.innerHTML = '<i class="bi bi-check-circle-fill text-' + color.check + ' fs-5"></i>';

            // Habilitar botón solo si cambió
            btnConfirmar.disabled = (rol === rolActual);
        });
    });

    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', function() {
            formCambiarRol.submit();
        });
    }
});
</script>
@endsection
