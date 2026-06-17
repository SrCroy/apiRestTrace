<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: white;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #0d6efd;
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        .navbar-top {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-none d-md-block sidebar py-3">
            <h4 class="text-center mb-4 text-white fw-bold">AdminPanel</h4>
            
            <div class="px-2">
                <small class="text-uppercase text-muted fw-bold mb-2 d-block">Principal</small>
                <a href="#" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                
                <small class="text-uppercase text-muted fw-bold mt-4 mb-2 d-block">Supervisión</small>
                <a href="#"><i class="bi bi-people me-2"></i> Usuarios</a>
                <a href="#"><i class="bi bi-map me-2"></i> Rutas</a>
                <a href="#"><i class="bi bi-bicycle me-2"></i> Actividades</a>
                
                <small class="text-uppercase text-muted fw-bold mt-4 mb-2 d-block">Moderación</small>
                <a href="#"><i class="bi bi-flag me-2"></i> Reportes</a>
                <a href="#"><i class="bi bi-person-x me-2"></i> Baneos</a>
                
                <small class="text-uppercase text-muted fw-bold mt-4 mb-2 d-block">Configuración</small>
                <a href="#"><i class="bi bi-trophy me-2"></i> Logros</a>
            </div>
        </div>

        <div class="col-md-9 col-lg-10 ms-sm-auto px-0 main-content">
            <nav class="navbar navbar-expand-lg navbar-top px-4 py-2 mb-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">@yield('page_title', 'Dashboard')</h5>
                
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="px-4">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>