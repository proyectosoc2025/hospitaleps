<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;

$database = new Database();
$db = $database->getConnection();

try {
    $query = "DELETE FROM doctores WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if($stmt->execute()) {
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'ELIMINAR', 'Doctor eliminado', 'doctores', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Doctor eliminado exitosamente']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
