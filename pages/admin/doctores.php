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

$query = "SELECT * FROM doctores ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Doctores - Hospital EPS</title>
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
                        <h2><i class="bi bi-person-badge"></i> Gestión de Doctores</h2>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list"></i> Lista de Doctores</span>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalDoctor">
                            <i class="bi bi-plus-circle"></i> Nuevo Doctor
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaDoctores" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Identificación</th>
                                        <th>Profesión</th>
                                        <th>Teléfono</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo safe_html($row['nombres'] . ' ' . $row['apellidos']); ?></td>
                                        <td><?php echo safe_html($row['identificacion']); ?></td>
                                        <td><?php echo safe_html($row['profesion']); ?></td>
                                        <td><?php echo safe_html($row['telefono']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="verDoctor(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarDoctor(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarDoctor(<?php echo $row['id']; ?>)">
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

    <!-- Modal Doctor -->
    <div class="modal fade" id="modalDoctor" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Registrar Doctor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formDoctor">
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
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" required>
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
                                <label class="form-label">Profesión</label>
                                <input type="text" class="form-control" name="profesion" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Título Profesional</label>
                                <input type="text" class="form-control" name="titulo_profesional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo</label>
                                <input type="text" class="form-control" name="cargo">
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
            initDataTable('#tablaDoctores');
        });

        $('#formDoctor').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../../api/doctores/crear.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showAlert('Doctor registrado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'danger');
                }
            });
        });

        function verDoctor(id) {
            fetch('../../api/doctores/ver.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const d = data.doctor;
                    const contenido = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Nombres:</strong> ${d.nombres}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Apellidos:</strong> ${d.apellidos}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Identificación:</strong> ${d.identificacion}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Usuario:</strong> ${d.usuario}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Teléfono:</strong> ${d.telefono || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Edad:</strong> ${d.edad || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Género:</strong> ${d.genero || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Profesión:</strong> ${d.profesion || 'N/A'}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Dirección:</strong> ${d.direccion || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Título Profesional:</strong> ${d.titulo_profesional || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Cargo:</strong> ${d.cargo || 'N/A'}
                            </div>
                        </div>
                    `;
                    
                    // Crear modal dinámico
                    const modalHtml = `
                        <div class="modal fade" id="modalVerDoctor" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="bi bi-person-badge"></i> Información del Doctor</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">${contenido}</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Eliminar modal anterior si existe
                    $('#modalVerDoctor').remove();
                    $('body').append(modalHtml);
                    $('#modalVerDoctor').modal('show');
                }
            });
        }

        function editarDoctor(id) {
            fetch('../../api/doctores/ver.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const d = data.doctor;
                    
                    // Crear modal de edición
                    const modalHtml = `
                        <div class="modal fade" id="modalEditarDoctor" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Doctor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form id="formEditarDoctor">
                                        <input type="hidden" name="id" value="${d.id}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nombres</label>
                                                    <input type="text" class="form-control" name="nombres" value="${d.nombres}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Apellidos</label>
                                                    <input type="text" class="form-control" name="apellidos" value="${d.apellidos}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Identificación</label>
                                                    <input type="text" class="form-control" name="identificacion" value="${d.identificacion}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control" name="telefono" value="${d.telefono || ''}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Edad</label>
                                                    <input type="number" class="form-control" name="edad" value="${d.edad || ''}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Género</label>
                                                    <select class="form-select" name="genero">
                                                        <option value="">Seleccione...</option>
                                                        <option value="Masculino" ${d.genero === 'Masculino' ? 'selected' : ''}>Masculino</option>
                                                        <option value="Femenino" ${d.genero === 'Femenino' ? 'selected' : ''}>Femenino</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Dirección</label>
                                                    <input type="text" class="form-control" name="direccion" value="${d.direccion || ''}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Profesión</label>
                                                    <input type="text" class="form-control" name="profesion" value="${d.profesion || ''}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Título Profesional</label>
                                                    <input type="text" class="form-control" name="titulo_profesional" value="${d.titulo_profesional || ''}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Cargo</label>
                                                    <input type="text" class="form-control" name="cargo" value="${d.cargo || ''}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Eliminar modal anterior si existe
                    $('#modalEditarDoctor').remove();
                    $('body').append(modalHtml);
                    $('#modalEditarDoctor').modal('show');
                    
                    // Manejar envío del formulario
                    $('#formEditarDoctor').on('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        
                        fetch('../../api/doctores/editar.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                $('#modalEditarDoctor').modal('hide');
                                showAlert('Doctor actualizado exitosamente', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        });
                    });
                }
            });
        }

        function eliminarDoctor(id) {
            if(confirmDelete('¿Está seguro de eliminar este doctor?')) {
                fetch('../../api/doctores/eliminar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showAlert('Doctor eliminado exitosamente', 'success');
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
