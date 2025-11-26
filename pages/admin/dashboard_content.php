<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas
$query = "SELECT COUNT(*) as total FROM pacientes";
$stmt = $db->prepare($query);
$stmt->execute();
$total_pacientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM doctores";
$stmt = $db->prepare($query);
$stmt->execute();
$total_doctores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM historias_clinicas";
$stmt = $db->prepare($query);
$stmt->execute();
$total_historias = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM administradores";
$stmt = $db->prepare($query);
$stmt->execute();
$total_admins = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="stat-card blue">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_pacientes; ?></h3>
                    <p class="mb-0">Pacientes</p>
                </div>
                <i class="bi bi-person-heart fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card green">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_doctores; ?></h3>
                    <p class="mb-0">Doctores</p>
                </div>
                <i class="bi bi-person-badge fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card orange">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_historias; ?></h3>
                    <p class="mb-0">Historias Clínicas</p>
                </div>
                <i class="bi bi-file-medical fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card red">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_admins; ?></h3>
                    <p class="mb-0">Administradores</p>
                </div>
                <i class="bi bi-people fs-1"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-activity"></i> Actividad Reciente</span>
                <a href="pages/admin/logs.php" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT * FROM logs ORDER BY fecha DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                ?>
                <div class="table-responsive">
                    <table id="tablaActividadReciente" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo safe_html($row['usuario'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'bg-secondary';
                                    if($row['accion'] == 'LOGIN') $badge_class = 'bg-success';
                                    elseif($row['accion'] == 'LOGOUT') $badge_class = 'bg-info';
                                    elseif($row['accion'] == 'CREAR') $badge_class = 'bg-primary';
                                    elseif($row['accion'] == 'MODIFICAR') $badge_class = 'bg-warning';
                                    elseif($row['accion'] == 'ELIMINAR') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo safe_html($row['accion'] ?? ''); ?></span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-check"></i> Últimas Visitas
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT v.*, p.nombre, p.primer_apellido, d.nombres, d.apellidos 
                          FROM visitas_medicas v
                          LEFT JOIN pacientes p ON v.paciente_id = p.id
                          LEFT JOIN doctores d ON v.doctor_id = d.id
                          ORDER BY v.fecha_visita DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                ?>
                <div class="table-responsive">
                    <table id="tablaUltimasVisitas" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Doctor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo safe_html(($row['nombre'] ?? 'N/A') . ' ' . ($row['primer_apellido'] ?? '')); ?></td>
                                <td><?php echo safe_html(($row['nombres'] ?? 'N/A') . ' ' . ($row['apellidos'] ?? '')); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_visita'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
