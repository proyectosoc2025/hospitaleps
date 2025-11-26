<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Doctor')) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';
require_once '../../config/security.php';

$database = new Database();
$db = $database->getConnection();

$nombre = Security::sanitizeInput($_POST['nombre']);
$primer_apellido = Security::sanitizeInput($_POST['primer_apellido']);
$segundo_apellido = Security::sanitizeInput($_POST['segundo_apellido'] ?? '');
$identificacion = Security::sanitizeInput($_POST['identificacion']);
$usuario = Security::sanitizeInput($_POST['usuario']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$edad = $_POST['edad'];
$sexo = $_POST['sexo'];
$grupo_sanguineo = $_POST['grupo_sanguineo'];
$telefono = Security::sanitizeInput($_POST['telefono']);
$correo_electronico = Security::sanitizeInput($_POST['correo_electronico'] ?? '');
$direccion = Security::sanitizeInput($_POST['direccion'] ?? '');
$profesion = Security::sanitizeInput($_POST['profesion'] ?? '');
$raza = Security::sanitizeInput($_POST['raza'] ?? '');
$tipo_piel = Security::sanitizeInput($_POST['tipo_piel'] ?? '');
$estatura = $_POST['estatura'] ?? null;
$peso = $_POST['peso'] ?? null;

// Si es un doctor, guardar su ID para rastrear qué doctor registró al paciente
$doctor_id = ($_SESSION['rol'] == 'Doctor') ? $_SESSION['user_id'] : null;

try {
    $query = "INSERT INTO pacientes (doctor_id, nombre, primer_apellido, segundo_apellido, identificacion, usuario, password, 
              edad, sexo, grupo_sanguineo, telefono, correo_electronico, direccion, profesion, raza, tipo_piel, estatura, peso) 
              VALUES (:doctor_id, :nombre, :primer_apellido, :segundo_apellido, :identificacion, :usuario, :password, 
              :edad, :sexo, :grupo_sanguineo, :telefono, :correo_electronico, :direccion, :profesion, :raza, :tipo_piel, :estatura, :peso)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':primer_apellido', $primer_apellido);
    $stmt->bindParam(':segundo_apellido', $segundo_apellido);
    $stmt->bindParam(':identificacion', $identificacion);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':sexo', $sexo);
    $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':correo_electronico', $correo_electronico);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->bindParam(':raza', $raza);
    $stmt->bindParam(':tipo_piel', $tipo_piel);
    $stmt->bindParam(':estatura', $estatura);
    $stmt->bindParam(':peso', $peso);
    
    if($stmt->execute()) {
        $paciente_id = $db->lastInsertId();
        
        // Log
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'CREAR', 'Paciente creado', 'pacientes', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $paciente_id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Paciente creado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear paciente']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
