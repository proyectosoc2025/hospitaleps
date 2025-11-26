<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../config/security.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->usuario) || !isset($data->password)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$usuario = Security::sanitizeInput($data->usuario);
$password = $data->password;

// Detectar inyección SQL
if (Security::detectSQLInjection($usuario) || Security::detectSQLInjection($password)) {
    $ip = $_SERVER['REMOTE_ADDR'];
    Security::logSuspiciousActivity($usuario, $ip, "Intento de inyección SQL detectado");
    
    // Registrar intento de inyección SQL en logs
    $database = new Database();
    $db = $database->getConnection();
    $log_query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
                  VALUES (:usuario, 'LOGIN_FALLIDO', 'Intento de inyección SQL detectado', :ip, NOW())";
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(':usuario', $usuario);
    $log_stmt->bindParam(':ip', $ip);
    $log_stmt->execute();
    
    echo json_encode([
        'success' => false,
        'message' => 'Usted está intentando realizar una operación sospechosa prohibida. Intento de acceso bloqueado.'
    ]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Buscar en administradores
$query = "SELECT * FROM administradores WHERE usuario = :usuario LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

$usuario_encontrado = false;
if ($stmt->rowCount() > 0) {
    $usuario_encontrado = true;
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar contraseña: primero intenta con hash, luego con texto plano
    $password_valida = false;
    if (password_verify($password, $row['password'])) {
        // Contraseña encriptada válida
        $password_valida = true;
    } elseif ($password === $row['password']) {
        // Contraseña en texto plano válida (para usuarios creados directamente en BD)
        $password_valida = true;
    }
    
    if ($password_valida) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['nombres'] = $row['nombres'];
        $_SESSION['apellidos'] = $row['apellidos'];
        
        // Log de acceso exitoso
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
                      VALUES (:usuario, 'LOGIN', 'Acceso exitoso', :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $usuario);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true]);
        exit();
    }
}

// Buscar en doctores
$query = "SELECT * FROM doctores WHERE usuario = :usuario LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $usuario_encontrado = true;
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar contraseña: primero intenta con hash, luego con texto plano
    $password_valida = false;
    if (password_verify($password, $row['password'])) {
        // Contraseña encriptada válida
        $password_valida = true;
    } elseif ($password === $row['password']) {
        // Contraseña en texto plano válida (para usuarios creados directamente en BD)
        $password_valida = true;
    }
    
    if ($password_valida) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['nombres'] = $row['nombres'];
        $_SESSION['apellidos'] = $row['apellidos'];
        
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
                      VALUES (:usuario, 'LOGIN', 'Acceso exitoso', :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $usuario);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true]);
        exit();
    }
}

// Buscar en pacientes
$query = "SELECT * FROM pacientes WHERE usuario = :usuario LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $usuario_encontrado = true;
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar contraseña: primero intenta con hash, luego con texto plano
    $password_valida = false;
    if (password_verify($password, $row['password'])) {
        // Contraseña encriptada válida
        $password_valida = true;
    } elseif ($password === $row['password']) {
        // Contraseña en texto plano válida (para usuarios creados directamente en BD)
        $password_valida = true;
    }
    
    if ($password_valida) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['nombres'] = $row['nombre'];
        $_SESSION['apellidos'] = $row['primer_apellido'] . ' ' . $row['segundo_apellido'];
        
        $log_query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
                      VALUES (:usuario, 'LOGIN', 'Acceso exitoso', :ip, NOW())";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->bindParam(':usuario', $usuario);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bindParam(':ip', $ip);
        $log_stmt->execute();
        
        echo json_encode(['success' => true]);
        exit();
    }
}

// Registrar intento de login fallido
$ip = $_SERVER['REMOTE_ADDR'];
$descripcion = $usuario_encontrado ? 'Contraseña incorrecta' : 'Usuario no encontrado';
$log_query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
              VALUES (:usuario, 'LOGIN_FALLIDO', :descripcion, :ip, NOW())";
$log_stmt = $db->prepare($log_query);
$log_stmt->bindParam(':usuario', $usuario);
$log_stmt->bindParam(':descripcion', $descripcion);
$log_stmt->bindParam(':ip', $ip);
$log_stmt->execute();

echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
?>
