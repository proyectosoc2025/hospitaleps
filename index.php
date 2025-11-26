<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#0d6efd">
    <meta name="description" content="Sistema de Gestión Médica - Hospital EPS">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Hospital EPS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Lado Izquierdo - Imagen -->
            <div class="col-12 col-md-6 p-0">
                <div class="hospital-image">
                    <img src="imagenes/hospital.jpg" alt="Hospital">
                    <div class="overlay">
                        <h1 class="text-white fw-bold">Hospital EPS</h1>
                        <p class="text-white fs-5">Sistema de Gestión Médica</p>
                    </div>
                </div>
            </div>
            
            <!-- Lado Derecho - Login -->
            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center bg-light">
                <div class="login-container p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-hospital fs-1 text-primary"></i>
                        <h2 class="mt-3 fw-bold text-primary">Iniciar Sesión</h2>
                        <p class="text-muted">Ingrese sus credenciales</p>
                    </div>
                    
                    <div id="alertContainer"></div>
                    
                    <form id="loginForm" method="POST" autocomplete="on">
                        <div class="mb-4">
                            <label for="usuario" class="form-label">
                                <i class="bi bi-person-circle me-1"></i>Usuario
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="usuario" 
                                    name="usuario" 
                                    placeholder="Ingrese su usuario"
                                    autocomplete="username"
                                    required
                                    minlength="3"
                                    aria-label="Usuario">
                            </div>
                            <div class="invalid-feedback">
                                El usuario debe tener al menos 3 caracteres
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-shield-lock me-1"></i>Contraseña
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Ingrese su contraseña"
                                    autocomplete="current-password"
                                    required
                                    minlength="4"
                                    aria-label="Contraseña">
                                <button 
                                    class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword"
                                    aria-label="Mostrar/Ocultar contraseña">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 4 caracteres
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Conexión segura
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer bg-dark text-white py-3">
        <div class="container text-center">
            <p class="mb-1">Jonathan Alexis Rodriguez</p>
            <p class="mb-1">Especialización en Seguridad de la Información</p>
            <p class="mb-1">Proyecto de Grado</p>
            <p class="mb-1">jarodriguez11@libertadores.edu.co</p>
            <p class="mb-1">2025</p>
            <p class="mb-0">Todos los derechos reservados</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>
