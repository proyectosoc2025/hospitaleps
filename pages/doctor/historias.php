<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Doctor') {
    header('Location: ../../index.php');
    exit();
}

require_once '../../config/database.php';
require_once '../../config/helpers.php';
$database = new Database();
$db = $database->getConnection();

$doctor_id = $_SESSION['user_id'];

// Obtener historias clínicas del doctor
$query = "SELECT hc.*, p.nombre, p.primer_apellido, p.segundo_apellido, p.identificacion
          FROM historias_clinicas hc
          INNER JOIN pacientes p ON hc.paciente_id = p.id
          WHERE hc.doctor_id = :doctor_id
          ORDER BY hc.fecha_consulta DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':doctor_id', $doctor_id);
$stmt->execute();

// Obtener pacientes registrados por el doctor
$query_pacientes = "SELECT * FROM pacientes WHERE doctor_id = :doctor_id ORDER BY nombre, primer_apellido";
$stmt_pacientes = $db->prepare($query_pacientes);
$stmt_pacientes->bindParam(':doctor_id', $doctor_id);
$stmt_pacientes->execute();
$pacientes = $stmt_pacientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historias Clínicas - Hospital EPS</title>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list"></i> Lista de Historias Clínicas</span>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalHistoria">
                            <i class="bi bi-plus-circle"></i> Nueva Historia Clínica
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaHistorias" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Paciente</th>
                                        <th>Identificación</th>
                                        <th>Fecha Consulta</th>
                                        <th>Diagnóstico</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo safe_html($row['nombre'] . ' ' . $row['primer_apellido'] . ' ' . $row['segundo_apellido']); ?></td>
                                        <td><?php echo safe_html($row['identificacion']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_consulta'])); ?></td>
                                        <td><?php echo safe_html(substr($row['diagnostico'], 0, 50)) . '...'; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="verHistoria(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarHistoria(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarHistoria(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-trash"></i>
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

    <!-- Modal Historia Clínica -->
    <div class="modal fade" id="modalHistoria" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-file-medical-fill"></i> Registrar Historia Clínica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formHistoria">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Paciente</label>
                                <select class="form-select" name="paciente_id" required>
                                    <option value="">Seleccione un paciente...</option>
                                    <?php foreach($pacientes as $pac): ?>
                                    <option value="<?php echo $pac['id']; ?>">
                                        <?php echo safe_html($pac['nombre'] . ' ' . $pac['primer_apellido'] . ' - ' . $pac['identificacion']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Consulta</label>
                                <input type="date" class="form-control" name="fecha_consulta" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Motivo de Consulta</label>
                                <textarea class="form-control" name="motivo_consulta" rows="3" required></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Síntomas</label>
                                <textarea class="form-control" name="sintomas" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Diagnóstico</label>
                                <textarea class="form-control" name="diagnostico" rows="4" required></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Tratamiento</label>
                                <textarea class="form-control" name="tratamiento" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Medicamentos</label>
                                <textarea class="form-control" name="medicamentos" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Exámenes Realizados</label>
                                <textarea class="form-control" name="examenes_realizados" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" name="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
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

        $('#formHistoria').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../../api/historias/crear.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showAlert('Historia clínica registrada exitosamente', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'danger');
                }
            });
        });

        function eliminarHistoria(id) {
            if(confirmDelete('¿Está seguro de eliminar esta historia clínica?')) {
                fetch('../../api/historias/eliminar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showAlert('Historia clínica eliminada exitosamente', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                });
            }
        }
    </script>
</body>
</html>
