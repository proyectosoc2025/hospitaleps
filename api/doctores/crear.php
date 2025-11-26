<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';
require_once '../../config/security.php';

$database = new Database();
$db = $database->getConnection();

$nombres = Security::sanitizeInput($_POST['nombres']);
$apellidos = Security::sanitizeInput($_POST['apellidos']);
$identificacion = Security::sanitizeInput($_POST['identificacion']);
$usuario = Security::sanitizeInput($_POST['usuario']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$telefono = Security::sanitizeInput($_POST['telefono']);
$edad = $_POST['edad'] ?? null;
$genero = $_POST['genero'] ?? null;
$direccion = Security::sanitizeInput($_POST['direccion'] ?? '');
$profesion = Security::sanitizeInput($_POST['profesion']);
$titulo_profesional = Security::sanitizeInput($_POST['titulo_profesional'] ?? '');
$cargo = Security::sanitizeInput($_POST['cargo'] ?? '');

try {
    $query = "INSERT INTO doctores (nombres, apellidos, identificacion, usuario, password, telefono, 
              edad, genero, direccion, profesion, titulo_profesional, cargo, rol) 
              VALUES (:nombres, :apellidos, :identificacion, :usuario, :password, :telefono, 
              :edad, :genero, :direccion, :profesion, :titulo_profesional, :cargo, 'Doctor')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':identificacion', $identificacion);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->bindParam(':titulo_profesional', $titulo_profesional);
    $stmt->bindParam(':cargo', $cargo);
    
    if($stmt->execute()) {
        $doctor_id = $db->lastInsertId();
        
        // Log
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'CREAR', 'Doctor creado', 'doctores', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $doctor_id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Doctor creado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear doctor']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
