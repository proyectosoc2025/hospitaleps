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

// Obtener historias clínicas del paciente
$query = "SELECT hc.*, d.nombres, d.apellidos, d.profesion, d.titulo_profesional
          FROM historias_clinicas hc
          LEFT JOIN doctores d ON hc.doctor_id = d.id
          WHERE hc.paciente_id = :paciente_id
          ORDER BY hc.fecha_consulta DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':paciente_id', $paciente_id);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Historias Clínicas - Hospital EPS</title>
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
                        <h2><i class="bi bi-file-medical"></i> Mis Historias Clínicas</h2>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list"></i> Historial Médico
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaHistorias" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Doctor</th>
                                        <th>Motivo</th>
                                        <th>Diagnóstico</th>
                                        <th>Medicamentos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_consulta'])); ?></td>
                                        <td>
                                            <?php echo safe_html($row['nombres'] . ' ' . $row['apellidos']); ?><br>
                                            <small class="text-muted"><?php echo safe_html($row['profesion']); ?></small>
                                        </td>
                                        <td><?php echo safe_html(substr($row['motivo_consulta'], 0, 40)) . '...'; ?></td>
                                        <td><?php echo safe_html(substr($row['diagnostico'], 0, 40)) . '...'; ?></td>
                                        <td><?php echo safe_html(substr($row['medicamentos'], 0, 30)) . '...'; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="verHistoria(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-eye"></i> Ver Detalle
                                            </button>
                                        </td>
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

    <!-- Modal Ver Historia -->
    <div class="modal fade" id="modalVerHistoria" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-file-medical"></i> Detalle de Historia Clínica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoHistoria">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
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
            initDataTable('#tablaHistorias');
        });

        function verHistoria(id) {
            fetch('../../api/historias/ver.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const h = data.historia;
                    const contenido = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Fecha de Consulta:</strong> ${h.fecha_consulta}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Doctor:</strong> ${h.doctor}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Motivo de Consulta:</strong><br>
                                <p>${h.motivo_consulta}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Síntomas:</strong><br>
                                <p>${h.sintomas}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Diagnóstico:</strong><br>
                                <p>${h.diagnostico}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Tratamiento:</strong><br>
                                <p>${h.tratamiento}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Medicamentos:</strong><br>
                                <p>${h.medicamentos}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Exámenes Realizados:</strong><br>
                                <p>${h.examenes_realizados}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Observaciones:</strong><br>
                                <p>${h.observaciones}</p>
                            </div>
                        </div>
                    `;
                    $('#contenidoHistoria').html(contenido);
                    $('#modalVerHistoria').modal('show');
                }
            });
        }
    </script>
</body>
</html>
