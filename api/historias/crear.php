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

$paciente_id = $_POST['paciente_id'];
$fecha_consulta = $_POST['fecha_consulta'];
$motivo_consulta = Security::sanitizeInput($_POST['motivo_consulta']);
$sintomas = Security::sanitizeInput($_POST['sintomas'] ?? '');
$diagnostico = Security::sanitizeInput($_POST['diagnostico']);
$tratamiento = Security::sanitizeInput($_POST['tratamiento'] ?? '');
$medicamentos = Security::sanitizeInput($_POST['medicamentos'] ?? '');
$examenes_realizados = Security::sanitizeInput($_POST['examenes_realizados'] ?? '');
$observaciones = Security::sanitizeInput($_POST['observaciones'] ?? '');

$doctor_id = null;
$admin_id = null;

if($_SESSION['rol'] == 'Doctor') {
    $doctor_id = $_SESSION['user_id'];
} else {
    $admin_id = $_SESSION['user_id'];
}

try {
    $query = "INSERT INTO historias_clinicas (paciente_id, doctor_id, admin_id, fecha_consulta, motivo_consulta, 
              sintomas, diagnostico, tratamiento, medicamentos, examenes_realizados, observaciones) 
              VALUES (:paciente_id, :doctor_id, :admin_id, :fecha_consulta, :motivo_consulta, 
              :sintomas, :diagnostico, :tratamiento, :medicamentos, :examenes_realizados, :observaciones)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':paciente_id', $paciente_id);
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':fecha_consulta', $fecha_consulta);
    $stmt->bindParam(':motivo_consulta', $motivo_consulta);
    $stmt->bindParam(':sintomas', $sintomas);
    $stmt->bindParam(':diagnostico', $diagnostico);
    $stmt->bindParam(':tratamiento', $tratamiento);
    $stmt->bindParam(':medicamentos', $medicamentos);
    $stmt->bindParam(':examenes_realizados', $examenes_realizados);
    $stmt->bindParam(':observaciones', $observaciones);
    
    if($stmt->execute()) {
        $historia_id = $db->lastInsertId();
        
        // Registrar visita médica
        $query_visita = "INSERT INTO visitas_medicas (paciente_id, doctor_id, historia_clinica_id, fecha_visita, motivo) 
                         VALUES (:paciente_id, :doctor_id, :historia_id, NOW(), :motivo)";
        $stmt_visita = $db->prepare($query_visita);
        $stmt_visita->bindParam(':paciente_id', $paciente_id);
        $stmt_visita->bindParam(':doctor_id', $doctor_id);
        $stmt_visita->bindParam(':historia_id', $historia_id);
        $stmt_visita->bindParam(':motivo', $motivo_consulta);
        $stmt_visita->execute();
        
        // Log
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, tabla_afectada, registro_id, ip_address, fecha) 
                      VALUES (:usuario, 'CREAR', 'Historia clínica creada', 'historias_clinicas', :registro_id, :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $_SESSION['usuario']);
        $log_stmt->bindParam(':registro_id', $historia_id);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Historia clínica creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear historia clínica']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
