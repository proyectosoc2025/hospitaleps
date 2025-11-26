<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';
require_once '../../config/security.php';

$database = new Database();
$db = $database->getConnection();

$doctor_id = $_SESSION['user_id'];
$nombres = Security::sanitizeInput($_POST['nombres']);
$apellidos = Security::sanitizeInput($_POST['apellidos']);
$edad = $_POST['edad'];
$genero = $_POST['genero'];
$telefono = Security::sanitizeInput($_POST['telefono']);
$direccion = Security::sanitizeInput($_POST['direccion']);
$profesion = Security::sanitizeInput($_POST['profesion']);
$titulo_profesional = Security::sanitizeInput($_POST['titulo_profesional']);
$cargo = Security::sanitizeInput($_POST['cargo']);

try {
    $query = "UPDATE doctores SET nombres = :nombres, apellidos = :apellidos, edad = :edad, 
              genero = :genero, telefono = :telefono, direccion = :direccion, profesion = :profesion,
              titulo_profesional = :titulo_profesional, cargo = :cargo WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->bindParam(':titulo_profesional', $titulo_profesional);
    $stmt->bindParam(':cargo', $cargo);
    $stmt->bindParam(':id', $doctor_id);
    
    if($stmt->execute()) {
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'MODIFICAR', 'Doctor actualizó su perfil', 'doctores', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $doctor_id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Perfil actualizado exitosamente']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
