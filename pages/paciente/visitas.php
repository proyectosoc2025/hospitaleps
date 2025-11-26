<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Paciente') {
    header('Location: ../../index.php');
    exit();
}

require_once '../../config/database.php';
require_once '../../config/helpers.php';
$database = new Database();
$db = $database->getConnection();

$paciente_id = $_SESSION['user_id'];

// Obtener visitas del paciente
$query = "SELECT v.*, d.nombres, d.apellidos, d.profesion
          FROM visitas_medicas v
          LEFT JOIN doctores d ON v.doctor_id = d.id
          WHERE v.paciente_id = :paciente_id
          ORDER BY v.fecha_visita DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':paciente_id', $paciente_id);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Visitas - Hospital EPS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div id="content">
            <?php include '../../includes/navbar.php'; ?>
            
            <div class="container-fluid mt-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2><i class="bi bi-calendar-check"></i> Mis Visitas Médicas</h2>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Historial de Visitas
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaVisitas" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Doctor</th>
                                        <th>Profesión</th>
                                        <th>Motivo</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_visita'])); ?></td>
                                        <td><?php echo safe_html($row['nombres'] . ' ' . $row['apellidos']); ?></td>
                                        <td><?php echo safe_html($row['profesion']); ?></td>
                                        <td><?php echo safe_html($row['motivo']); ?></td>
                                        <td><?php echo safe_html($row['observaciones']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include '../../includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/datatable-config.js"></script>
    <script>
        $(document).ready(function() {
            initDataTable('#tablaVisitas');
        });
    </script>
</body>
</html>
