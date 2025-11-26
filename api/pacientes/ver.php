<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'Administrador') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM pacientes WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // No enviar la contraseña
        unset($paciente['password']);
        
        echo json_encode(['success' => true, 'paciente' => $paciente]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Paciente no encontrado']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
