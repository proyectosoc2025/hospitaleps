<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$rol = $_SESSION['rol'];
$nombres = $_SESSION['nombres'];
$apellidos = $_SESSION['apellidos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#0d6efd">
    <meta name="description" content="Dashboard - Sistema de Gestión Médica Hospital EPS">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Dashboard - Hospital EPS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-hospital fs-2"></i>
                <h3>Hospital EPS</h3>
            </div>
            
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                
                <?php if($rol == 'Administrador'): ?>
                <li>
                    <a href="pages/admin/usuarios.php"><i class="bi bi-people"></i> Gestión Usuarios</a>
                </li>
                <li>
                    <a href="pages/admin/doctores.php"><i class="bi bi-person-badge"></i> Gestión Doctores</a>
                </li>
                <li>
                    <a href="pages/admin/pacientes.php"><i class="bi bi-person-heart"></i> Gestión Pacientes</a>
                </li>
                <li>
                    <a href="pages/admin/historias.php"><i class="bi bi-file-medical"></i> Historias Clínicas</a>
                </li>
                <li>
                    <a href="pages/admin/logs.php"><i class="bi bi-journal-text"></i> Logs del Sistema</a>
                </li>
                <?php elseif($rol == 'Doctor'): ?>
                <li>
                    <a href="pages/doctor/perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a>
                </li>
                <li>
                    <a href="pages/doctor/pacientes.php"><i class="bi bi-person-heart"></i> Mis Pacientes</a>
                </li>
                <li>
                    <a href="pages/doctor/historias.php"><i class="bi bi-file-medical"></i> Historias Clínicas</a>
                </li>
                <?php elseif($rol == 'Paciente'): ?>
                <li>
                    <a href="pages/paciente/perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a>
                </li>
                <li>
                    <a href="pages/paciente/historias.php"><i class="bi bi-file-medical"></i> Mis Historias</a>
                </li>
                <li>
                    <a href="pages/paciente/visitas.php"><i class="bi bi-calendar-check"></i> Mis Visitas</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3">
                            <i class="bi bi-person-circle fs-5"></i>
                            <strong><?php echo $nombres . ' ' . $apellidos; ?></strong>
                            <span class="badge bg-primary ms-2"><?php echo $rol; ?></span>
                        </span>
                        <a href="api/logout.php" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid mt-4">
                <div class="row">
                    <div class="col-12">
                        <h1 class="mb-4">Bienvenido, <?php echo $nombres; ?></h1>
                    </div>
                </div>
                
                <?php if($rol == 'Administrador'): ?>
                    <?php include 'pages/admin/dashboard_content.php'; ?>
                <?php elseif($rol == 'Doctor'): ?>
                    <?php include 'pages/doctor/dashboard_content.php'; ?>
                <?php elseif($rol == 'Paciente'): ?>
                    <?php include 'pages/paciente/dashboard_content.php'; ?>
                <?php endif; ?>
            </div>
            
            <footer class="footer bg-dark text-white py-3 mt-5">
                <div class="container text-center">
                    <p class="mb-1">Jonathan Alexis Rodriguez</p>
                    <p class="mb-1">Especialización en Seguridad de la Información</p>
                    <p class="mb-1">Proyecto de Grado</p>
                    <p class="mb-1">jarodriguez11@libertadores.edu.co</p>
                    <p class="mb-1">2025</p>
                    <p class="mb-0">Todos los derechos reservados</p>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTables para las tablas del dashboard
            if($('#tablaActividadReciente').length) {
                $('#tablaActividadReciente').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                    order: [[2, 'desc']],
                    responsive: true,
                    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
                });
            }

            if($('#tablaUltimasVisitas').length) {
                $('#tablaUltimasVisitas').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                    order: [[2, 'desc']],
                    responsive: true,
                    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
                });
            }
        });
    </script>
</body>
</html>
