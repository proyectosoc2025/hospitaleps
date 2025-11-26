<?php
// Configuración para Hosting (InfinityFree, 000webhost, etc.)
// INSTRUCCIONES: Renombra este archivo a database.php después de editar

class Database {
    // ===== EDITA ESTOS VALORES CON LOS DATOS DE TU HOSTING =====
    
    // Hostname de MySQL (ejemplo: sql123.epizy.com para InfinityFree)
    private $host = "TU_HOSTNAME_AQUI";
    
    // Nombre de la base de datos (ejemplo: epiz_12345678_EPS)
    private $db_name = "TU_DATABASE_NAME_AQUI";
    
    // Usuario de MySQL (ejemplo: epiz_12345678)
    private $username = "TU_USERNAME_AQUI";
    
    // Contraseña de MySQL
    private $password = "TU_PASSWORD_AQUI";
    
    // ============================================================
    
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $exception) {
            // En producción, no mostrar detalles del error
            error_log("Error de conexión: " . $exception->getMessage());
            die("Error al conectar con la base de datos. Contacte al administrador.");
        }
        return $this->conn;
    }
}
?>
