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

// Obtener datos del doctor
$query = "SELECT * FROM doctores WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $doctor_id);
$stmt->execute();
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);
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
                                <h4 class="mt-3"><?php echo safe_html($doctor['nombres'] . ' ' . $doctor['apellidos']); ?></h4>
                                <p class="text-muted"><?php echo safe_html($doctor['profesion']); ?></p>
                                <span class="badge bg-primary"><?php echo $doctor['rol']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-info-circle"></i> Información Personal
                            </div>
                            <div class="card-body">
                                <form id="formPerfil">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombres</label>
                                            <input type="text" class="form-control" name="nombres" value="<?php echo safe_html($doctor['nombres']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Apellidos</label>
                                            <input type="text" class="form-control" name="apellidos" value="<?php echo safe_html($doctor['apellidos']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Identificación</label>
                                            <input type="text" class="form-control" value="<?php echo safe_html($doctor['identificacion']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Edad</label>
                                            <input type="number" class="form-control" name="edad" value="<?php echo $doctor['edad']; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Género</label>
                                            <select class="form-select" name="genero">
                                                <option value="Masculino" <?php echo $doctor['genero'] == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                                <option value="Femenino" <?php echo $doctor['genero'] == 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" name="telefono" value="<?php echo safe_html($doctor['telefono']); ?>">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" class="form-control" name="direccion" value="<?php echo safe_html($doctor['direccion']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Profesión</label>
                                            <input type="text" class="form-control" name="profesion" value="<?php echo safe_html($doctor['profesion']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Título Profesional</label>
                                            <input type="text" class="form-control" name="titulo_profesional" value="<?php echo safe_html($doctor['titulo_profesional']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cargo</label>
                                            <input type="text" class="form-control" name="cargo" value="<?php echo safe_html($doctor['cargo']); ?>">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Actualizar Perfil
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
            
            fetch('../../api/doctores/actualizar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showAlert('Perfil actualizado exitosamente', 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            });
        });
    </script>
</body>
</html>
