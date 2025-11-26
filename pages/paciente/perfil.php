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

// Obtener datos del paciente
$query = "SELECT * FROM pacientes WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $paciente_id);
$stmt->execute();
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Hospital EPS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
                        <h2><i class="bi bi-person-circle"></i> Mi Perfil</h2>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-person-circle fs-1 text-primary" style="font-size: 120px;"></i>
                                <h4 class="mt-3"><?php echo safe_html($paciente['nombre'] . ' ' . $paciente['primer_apellido']); ?></h4>
                                <p class="text-muted">Paciente</p>
                                <span class="badge bg-danger"><?php echo safe_html($paciente['grupo_sanguineo']); ?></span>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-info-circle"></i> Información Médica
                            </div>
                            <div class="card-body">
                                <p><strong>Grupo Sanguíneo:</strong> <?php echo safe_html($paciente['grupo_sanguineo']); ?></p>
                                <p><strong>Edad:</strong> <?php echo $paciente['edad']; ?> años</p>
                                <p><strong>Estatura:</strong> <?php echo $paciente['estatura']; ?> m</p>
                                <p><strong>Peso:</strong> <?php echo $paciente['peso']; ?> kg</p>
                                <p><strong>Tipo de Piel:</strong> <?php echo safe_html($paciente['tipo_piel']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-pencil"></i> Actualizar Información
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Solo puede modificar: contraseña, dirección, correo electrónico y teléfono
                                </div>
                                <form id="formPerfil">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" class="form-control" value="<?php echo safe_html($paciente['nombre']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Apellidos</label>
                                            <input type="text" class="form-control" value="<?php echo safe_html($paciente['primer_apellido'] . ' ' . $paciente['segundo_apellido']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Identificación</label>
                                            <input type="text" class="form-control" value="<?php echo safe_html($paciente['identificacion']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Edad</label>
                                            <input type="text" class="form-control" value="<?php echo $paciente['edad']; ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="telefono" value="<?php echo safe_html($paciente['telefono']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="correo_electronico" value="<?php echo safe_html($paciente['correo_electronico']); ?>">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Dirección <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="direccion" value="<?php echo safe_html($paciente['direccion']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" placeholder="Dejar en blanco para no cambiar">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" name="password_confirm" placeholder="Confirmar nueva contraseña">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Actualizar Información
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include '../../includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script>
        $('#formPerfil').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            const password = formData.get('password');
            const password_confirm = formData.get('password_confirm');
            
            if(password && password !== password_confirm) {
                showAlert('Las contraseñas no coinciden', 'danger');
                return;
            }
            
            fetch('../../api/pacientes/actualizar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showAlert('Información actualizada exitosamente', 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            });
        });
    </script>
</body>
</html>
