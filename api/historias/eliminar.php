<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Doctor')) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;

$database = new Database();
$db = $database->getConnection();

// Verificar que el doctor solo pueda eliminar sus propias historias
if($_SESSION['rol'] == 'Doctor') {
    $query_check = "SELECT doctor_id FROM historias_clinicas WHERE id = :id";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();
    $historia = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if($historia['doctor_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'No tiene permiso para eliminar esta historia']);
        exit();
    }
}

try {
    $query = "DELETE FROM historias_clinicas WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if($stmt->execute()) {
        // Log
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'ELIMINAR', 'Historia clínica eliminada', 'historias_clinicas', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Historia clínica eliminada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar historia clínica']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
