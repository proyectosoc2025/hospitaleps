<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Verificar permisos
if($_SESSION['rol'] == 'Paciente') {
    $query = "SELECT hc.*, d.nombres, d.apellidos, d.profesion, d.titulo_profesional
              FROM historias_clinicas hc
              LEFT JOIN doctores d ON hc.doctor_id = d.id
              WHERE hc.id = :id AND hc.paciente_id = :paciente_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':paciente_id', $_SESSION['user_id']);
} elseif($_SESSION['rol'] == 'Doctor') {
    $query = "SELECT hc.*, d.nombres, d.apellidos, d.profesion, d.titulo_profesional,
              p.nombre, p.primer_apellido, p.segundo_apellido
              FROM historias_clinicas hc
              LEFT JOIN doctores d ON hc.doctor_id = d.id
              LEFT JOIN pacientes p ON hc.paciente_id = p.id
              WHERE hc.id = :id AND hc.doctor_id = :doctor_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':doctor_id', $_SESSION['user_id']);
} else {
    $query = "SELECT hc.*, d.nombres, d.apellidos, d.profesion, d.titulo_profesional,
              p.nombre, p.primer_apellido, p.segundo_apellido
              FROM historias_clinicas hc
              LEFT JOIN doctores d ON hc.doctor_id = d.id
              LEFT JOIN pacientes p ON hc.paciente_id = p.id
              WHERE hc.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
}

$stmt->execute();

if($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Construir nombre del paciente
    $paciente = '';
    if(isset($row['nombre'])) {
        $paciente = $row['nombre'] . ' ' . $row['primer_apellido'];
        if(!empty($row['segundo_apellido'])) {
            $paciente .= ' ' . $row['segundo_apellido'];
        }
    } else {
        $paciente = 'N/A';
    }
    
    // Construir nombre del doctor
    $doctor = 'N/A';
    if(!empty($row['nombres']) && !empty($row['apellidos'])) {
        $doctor = $row['nombres'] . ' ' . $row['apellidos'];
        if(!empty($row['profesion'])) {
            $doctor .= ' - ' . $row['profesion'];
        }
    }
    
    $historia = [
        'paciente' => $paciente,
        'fecha_consulta' => date('d/m/Y', strtotime($row['fecha_consulta'])),
        'doctor' => $doctor,
        'motivo_consulta' => $row['motivo_consulta'] ?? 'No especificado',
        'sintomas' => $row['sintomas'] ?? 'No especificado',
        'diagnostico' => $row['diagnostico'] ?? 'No especificado',
        'tratamiento' => $row['tratamiento'] ?? 'No especificado',
        'medicamentos' => $row['medicamentos'] ?? 'No especificado',
        'examenes_realizados' => $row['examenes_realizados'] ?? 'No especificado',
        'observaciones' => $row['observaciones'] ?? 'No especificado'
    ];
    
    echo json_encode(['success' => true, 'historia' => $historia]);
} else {
    echo json_encode(['success' => false, 'message' => 'Historia no encontrada']);
}
?>
