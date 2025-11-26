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
    $query = "SELECT * FROM doctores WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // No enviar la contraseña
        unset($doctor['password']);
        
        echo json_encode(['success' => true, 'doctor' => $doctor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Doctor no encontrado']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
