<?php
class Security {
    
    public static function detectSQLInjection($input) {
        $sql_patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\'|\"|;|--|\*|\/\*|\*\/)/i',
            '/(\bOR\b.*=.*)/i',
            '/(\bAND\b.*=.*)/i'
        ];
        
        foreach($sql_patterns as $pattern) {
            if(preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    public static function logSuspiciousActivity($usuario, $ip, $descripcion) {
        require_once 'database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO logs (usuario, accion, descripcion, ip_address, fecha) 
                  VALUES (:usuario, 'INTENTO_INYECCION_SQL', :descripcion, :ip, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    }
}
?>
