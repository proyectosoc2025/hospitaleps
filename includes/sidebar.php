<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-hospital fs-2"></i>
        <h3>Hospital EPS</h3>
    </div>
    
    <ul class="list-unstyled components">
        <li>
            <a href="../../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        
        <?php if($_SESSION['rol'] == 'Administrador'): ?>
        <li>
            <a href="../../pages/admin/pacientes.php"><i class="bi bi-person-heart"></i> Gestión Pacientes</a>
        </li>
        <li>
            <a href="../../pages/admin/doctores.php"><i class="bi bi-person-badge"></i> Gestión Doctores</a>
        </li>
        <li>
            <a href="../../pages/admin/usuarios.php"><i class="bi bi-people"></i> Gestión Usuarios</a>
        </li>
        <li>
            <a href="../../pages/admin/historias.php"><i class="bi bi-file-medical"></i> Historias Clínicas</a>
        </li>
        <li>
            <a href="../../pages/admin/logs.php"><i class="bi bi-journal-text"></i> Logs del Sistema</a>
        </li>
        <?php elseif($_SESSION['rol'] == 'Doctor'): ?>
        <li>
            <a href="../../pages/doctor/perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a>
        </li>
        <li>
            <a href="../../pages/doctor/pacientes.php"><i class="bi bi-person-heart"></i> Mis Pacientes</a>
        </li>
        <li>
            <a href="../../pages/doctor/historias.php"><i class="bi bi-file-medical"></i> Historias Clínicas</a>
        </li>
        <?php elseif($_SESSION['rol'] == 'Paciente'): ?>
        <li>
            <a href="../../pages/paciente/perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a>
        </li>
        <li>
            <a href="../../pages/paciente/historias.php"><i class="bi bi-file-medical"></i> Mis Historias</a>
        </li>
        <li>
            <a href="../../pages/paciente/visitas.php"><i class="bi bi-calendar-check"></i> Mis Visitas</a>
        </li>
        <?php endif; ?>
    </ul>
    
</nav>
