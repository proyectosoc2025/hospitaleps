<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Administrador') {
    header('Location: ../../index.php');
    exit();
}

require_once '../../config/database.php';
require_once '../../config/helpers.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM administradores ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores - Hospital EPS</title>
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
                        <h2><i class="bi bi-people"></i> Gestión de Administradores</h2>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list"></i> Lista de Administradores</span>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdmin">
                            <i class="bi bi-plus-circle"></i> Nuevo Administrador
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaAdmins" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Identificación</th>
                                        <th>Usuario</th>
                                        <th>Cargo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo safe_html($row['nombres'] . ' ' . $row['apellidos']); ?></td>
                                        <td><?php echo safe_html($row['identificacion']); ?></td>
                                        <td><?php echo safe_html($row['usuario']); ?></td>
                                        <td><?php echo safe_html($row['cargo']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarAdmin(<?php echo $row['id']; ?>)">
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

    <!-- Modal Administrador -->
    <div class="modal fade" id="modalAdmin" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Registrar Administrador</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAdmin">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombres</label>
                                <input type="text" class="form-control" name="nombres" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" name="apellidos" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Identificación</label>
                                <input type="text" class="form-control" name="identificacion" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" class="form-control" name="usuario" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo</label>
                                <input type="text" class="form-control" name="cargo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control" name="edad">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Género</label>
                                <select class="form-select" name="genero">
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" name="direccion">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono">
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
            initDataTable('#tablaAdmins');
        });

        $('#formAdmin').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../../api/administradores/crear.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showAlert('Administrador registrado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'danger');
                }
            });
        });

        function eliminarAdmin(id) {
            if(confirmDelete('¿Está seguro de eliminar este administrador?')) {
                fetch('../../api/administradores/eliminar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showAlert('Administrador eliminado exitosamente', 'success');
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
