<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-primary">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3">
                <i class="bi bi-person-circle fs-5"></i>
                <strong><?php echo $_SESSION['nombres'] . ' ' . $_SESSION['apellidos']; ?></strong>
                <span class="badge bg-primary ms-2"><?php echo $_SESSION['rol']; ?></span>
            </span>
            <a href="../../api/logout.php" class="btn btn-danger">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</nav>
