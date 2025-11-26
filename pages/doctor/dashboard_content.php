<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
$database = new Database();
$db = $database->getConnection();

$doctor_id = $_SESSION['user_id'];

// Obtener estadísticas del doctor
$query = "SELECT COUNT(DISTINCT paciente_id) as total FROM historias_clinicas WHERE doctor_id = :doctor_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':doctor_id', $doctor_id);
$stmt->execute();
$total_pacientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM historias_clinicas WHERE doctor_id = :doctor_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':doctor_id', $doctor_id);
$stmt->execute();
$total_historias = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM visitas_medicas WHERE doctor_id = :doctor_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':doctor_id', $doctor_id);
$stmt->execute();
$total_visitas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="stat-card blue">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_pacientes; ?></h3>
                    <p class="mb-0">Mis Pacientes</p>
                </div>
                <i class="bi bi-person-heart fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="stat-card green">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_historias; ?></h3>
                    <p class="mb-0">Historias Clínicas</p>
                </div>
                <i class="bi bi-file-medical fs-1"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="stat-card orange">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $total_visitas; ?></h3>
                    <p class="mb-0">Visitas Realizadas</p>
                </div>
                <i class="bi bi-calendar-check fs-1"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-heart"></i> Mis Últimos Pacientes Atendidos
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT DISTINCT p.*, MAX(hc.fecha_consulta) as ultima_consulta
                          FROM pacientes p
                          INNER JOIN historias_clinicas hc ON p.id = hc.paciente_id
                          WHERE hc.doctor_id = :doctor_id
                          GROUP BY p.id
                          ORDER BY ultima_consulta DESC
                          LIMIT 10";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':doctor_id', $doctor_id);
                $stmt->execute();
                ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Identificación</th>
                                <th>Teléfono</th>
                                <th>Última Consulta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo safe_html($row['nombre'] . ' ' . $row['primer_apellido']); ?></td>
                                <td><?php echo safe_html($row['identificacion']); ?></td>
                                <td><?php echo safe_html($row['telefono']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['ultima_consulta'])); ?></td>
                                <td>
                                    <a href="pages/doctor/ver_paciente.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
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
