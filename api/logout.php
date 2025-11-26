<?php
session_start();

require_once '../config/database.php';

if(isset($_SESSION['usuario'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
              VALUES (:usuario, 'LOGOUT', 'Cierre de sesión', :ip, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':usuario', $_SESSION['usuario']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bindParam(':ip', $ip);
    $stmt->execute();
}

session_destroy();
header('Location: ../index.php');
exit();
?>
