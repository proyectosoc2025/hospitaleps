<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
$database = new Database();
$db = $database->getConnection();

$paciente_id = $_SESSION['user_id'];

// Obtener estadísticas del paciente
$query = "SELECT COUNT(*) as total FROM historias_clinicas WHERE paciente_id = :paciente_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':paciente_id', $paciente_id);
$stmt->execute();
$total_historias = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM visitas_medicas WHERE paciente_id = :paciente_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':paciente_id', $paciente_id);
$stmt->execute();
$total_visitas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(DISTINCT doctor_id) as total FROM historias_clinicas WHERE paciente_id = :paciente_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':paciente_id', $paciente_id);
$stmt->execute();
$total_doctores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="stat-card blue">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_historias; ?></h3>
                    <p class="mb-0">Mis Historias Clínicas</p>
                </div>
                <i class="bi bi-file-medical fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="stat-card green">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_visitas; ?></h3>
                    <p class="mb-0">Visitas Médicas</p>
                </div>
                <i class="bi bi-calendar-check fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="stat-card orange">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_doctores; ?></h3>
                    <p class="mb-0">Doctores que me han atendido</p>
                </div>
                <i class="bi bi-person-badge fs-1"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-medical"></i> Mis Últimas Historias Clínicas
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT hc.*, d.nombres, d.apellidos, d.profesion
                          FROM historias_clinicas hc
                          LEFT JOIN doctores d ON hc.doctor_id = d.id
                          WHERE hc.paciente_id = :paciente_id
                          ORDER BY hc.fecha_consulta DESC
                          LIMIT 10";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':paciente_id', $paciente_id);
                $stmt->execute();
                ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Doctor</th>
                                <th>Diagnóstico</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['fecha_consulta'])); ?></td>
                                <td><?php echo safe_html($row['nombres'] . ' ' . $row['apellidos']); ?><br>
                                    <small class="text-muted"><?php echo safe_html($row['profesion']); ?></small>
                                </td>
                                <td><?php echo safe_html(substr($row['diagnostico'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <a href="pages/paciente/ver_historia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
