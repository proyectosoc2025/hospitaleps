<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Paciente') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';
require_once '../../config/security.php';

$database = new Database();
$db = $database->getConnection();

$paciente_id = $_SESSION['user_id'];
$telefono = Security::sanitizeInput($_POST['telefono']);
$correo_electronico = Security::sanitizeInput($_POST['correo_electronico']);
$direccion = Security::sanitizeInput($_POST['direccion']);

try {
    $query = "UPDATE pacientes SET telefono = :telefono, correo_electronico = :correo_electronico, 
              direccion = :direccion";
    
    $params = [
        ':telefono' => $telefono,
        ':correo_electronico' => $correo_electronico,
        ':direccion' => $direccion,
        ':id' => $paciente_id
    ];
    
    // Si se proporciona nueva contraseña
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password = :password";
        $params[':password'] = $password;
    }
    
    $query .= " WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    if($stmt->execute()) {
        // Log
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'MODIFICAR', 'Paciente actualizó su perfil', 'pacientes', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $paciente_id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Información actualizada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar información']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
