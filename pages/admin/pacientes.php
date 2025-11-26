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

// Obtener todos los pacientes
$query = "SELECT * FROM pacientes ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pacientes - Hospital EPS</title>
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
                        <h2><i class="bi bi-person-heart"></i> Gestión de Pacientes</h2>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list"></i> Lista de Pacientes</span>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPaciente">
                            <i class="bi bi-plus-circle"></i> Nuevo Paciente
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaPacientes" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Identificación</th>
                                        <th>Edad</th>
                                        <th>Teléfono</th>
                                        <th>Grupo Sanguíneo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo safe_html($row['nombre'] . ' ' . $row['primer_apellido'] . ' ' . $row['segundo_apellido']); ?></td>
                                        <td><?php echo safe_html($row['identificacion']); ?></td>
                                        <td><?php echo $row['edad']; ?></td>
                                        <td><?php echo safe_html($row['telefono']); ?></td>
                                        <td><span class="badge bg-danger"><?php echo safe_html($row['grupo_sanguineo']); ?></span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="verPaciente(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarPaciente(<?php echo $row['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarPaciente(<?php echo $row['id']; ?>)">
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

    <!-- Modal Paciente -->
    <div class="modal fade" id="modalPaciente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Registrar Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formPaciente">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" name="primer_apellido" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" name="segundo_apellido">
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
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control" name="edad" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sexo</label>
                                <select class="form-select" name="sexo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Grupo Sanguíneo</label>
                                <select class="form-select" name="grupo_sanguineo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo_electronico">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" name="direccion">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profesión</label>
                                <input type="text" class="form-control" name="profesion">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Raza</label>
                                <input type="text" class="form-control" name="raza">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo de Piel</label>
                                <input type="text" class="form-control" name="tipo_piel">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estatura (m)</label>
                                <input type="number" step="0.01" class="form-control" name="estatura">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Peso (kg)</label>
                                <input type="number" step="0.1" class="form-control" name="peso">
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
            initDataTable('#tablaPacientes');
        });

        $('#formPaciente').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../../api/pacientes/crear.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showAlert('Paciente registrado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'danger');
                }
            });
        });

        function verPaciente(id) {
            fetch('../../api/pacientes/ver.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const p = data.paciente;
                    const contenido = `
                        <div class="row">
                            <div class="col-md-12 text-center mb-4">
                                <i class="bi bi-person-circle" style="font-size: 80px; color: #0d6efd;"></i>
                                <h4 class="mt-2">${p.nombre} ${p.primer_apellido} ${p.segundo_apellido || ''}</h4>
                                <span class="badge bg-danger">${p.grupo_sanguineo}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Identificación:</strong> ${p.identificacion}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Usuario:</strong> ${p.usuario}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Edad:</strong> ${p.edad} años
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Sexo:</strong> ${p.sexo}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Teléfono:</strong> ${p.telefono || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Correo:</strong> ${p.correo_electronico || 'N/A'}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Dirección:</strong> ${p.direccion || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Profesión:</strong> ${p.profesion || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Raza:</strong> ${p.raza || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Tipo de Piel:</strong> ${p.tipo_piel || 'N/A'}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Grupo Sanguíneo:</strong> <span class="badge bg-danger">${p.grupo_sanguineo}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Estatura:</strong> ${p.estatura || 'N/A'} m
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Peso:</strong> ${p.peso || 'N/A'} kg
                            </div>
                        </div>
                    `;
                    
                    const modalHtml = `
                        <div class="modal fade" id="modalVerPaciente" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="bi bi-person-heart"></i> Información del Paciente</h5>
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
                    
                    $('#modalVerPaciente').remove();
                    $('body').append(modalHtml);
                    $('#modalVerPaciente').modal('show');
                }
            });
        }

        function editarPaciente(id) {
            fetch('../../api/pacientes/ver.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const p = data.paciente;
                    
                    const modalHtml = `
                        <div class="modal fade" id="modalEditarPaciente" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Paciente</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form id="formEditarPaciente">
                                        <input type="hidden" name="id" value="${p.id}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" name="nombre" value="${p.nombre}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Primer Apellido</label>
                                                    <input type="text" class="form-control" name="primer_apellido" value="${p.primer_apellido}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Segundo Apellido</label>
                                                    <input type="text" class="form-control" name="segundo_apellido" value="${p.segundo_apellido || ''}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Identificación</label>
                                                    <input type="text" class="form-control" name="identificacion" value="${p.identificacion}" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Edad</label>
                                                    <input type="number" class="form-control" name="edad" value="${p.edad}" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Sexo</label>
                                                    <select class="form-select" name="sexo" required>
                                                        <option value="Masculino" ${p.sexo === 'Masculino' ? 'selected' : ''}>Masculino</option>
                                                        <option value="Femenino" ${p.sexo === 'Femenino' ? 'selected' : ''}>Femenino</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Grupo Sanguíneo</label>
                                                    <select class="form-select" name="grupo_sanguineo" required>
                                                        <option value="A+" ${p.grupo_sanguineo === 'A+' ? 'selected' : ''}>A+</option>
                                                        <option value="A-" ${p.grupo_sanguineo === 'A-' ? 'selected' : ''}>A-</option>
                                                        <option value="B+" ${p.grupo_sanguineo === 'B+' ? 'selected' : ''}>B+</option>
                                                        <option value="B-" ${p.grupo_sanguineo === 'B-' ? 'selected' : ''}>B-</option>
                                                        <option value="AB+" ${p.grupo_sanguineo === 'AB+' ? 'selected' : ''}>AB+</option>
                                                        <option value="AB-" ${p.grupo_sanguineo === 'AB-' ? 'selected' : ''}>AB-</option>
                                                        <option value="O+" ${p.grupo_sanguineo === 'O+' ? 'selected' : ''}>O+</option>
                                                        <option value="O-" ${p.grupo_sanguineo === 'O-' ? 'selected' : ''}>O-</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control" name="telefono" value="${p.telefono || ''}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Correo Electrónico</label>
                                                    <input type="email" class="form-control" name="correo_electronico" value="${p.correo_electronico || ''}">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Dirección</label>
                                                    <input type="text" class="form-control" name="direccion" value="${p.direccion || ''}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Profesión</label>
                                                    <input type="text" class="form-control" name="profesion" value="${p.profesion || ''}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Raza</label>
                                                    <input type="text" class="form-control" name="raza" value="${p.raza || ''}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Tipo de Piel</label>
                                                    <input type="text" class="form-control" name="tipo_piel" value="${p.tipo_piel || ''}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Estatura (m)</label>
                                                    <input type="number" step="0.01" class="form-control" name="estatura" value="${p.estatura || ''}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Peso (kg)</label>
                                                    <input type="number" step="0.1" class="form-control" name="peso" value="${p.peso || ''}">
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
                    
                    $('#modalEditarPaciente').remove();
                    $('body').append(modalHtml);
                    $('#modalEditarPaciente').modal('show');
                    
                    $('#formEditarPaciente').on('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        
                        fetch('../../api/pacientes/editar.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                $('#modalEditarPaciente').modal('hide');
                                showAlert('Paciente actualizado exitosamente', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        });
                    });
                }
            });
        }

        function eliminarPaciente(id) {
            if(confirmDelete('¿Está seguro de eliminar este paciente?')) {
                fetch('../../api/pacientes/eliminar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showAlert('Paciente eliminado exitosamente', 'success');
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
