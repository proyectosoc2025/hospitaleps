-- Crear base de datos
CREATE DATABASE IF NOT EXISTS EPS CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE EPS;

-- Tabla de administradores
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    identificacion VARCHAR(20) UNIQUE NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'Administrador',
    cargo VARCHAR(100),
    edad INT,
    genero VARCHAR(20),
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de doctores
CREATE TABLE IF NOT EXISTS doctores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    identificacion VARCHAR(20) UNIQUE NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol VARCHAR(20) DEFAULT 'Doctor',
    direccion VARCHAR(255),
    profesion VARCHAR(100),
    edad INT,
    genero VARCHAR(20),
    titulo_profesional VARCHAR(150),
    cargo VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de pacientes
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    primer_apellido VARCHAR(100) NOT NULL,
    segundo_apellido VARCHAR(100),
    identificacion VARCHAR(20) UNIQUE NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    edad INT,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    profesion VARCHAR(100),
    raza VARCHAR(50),
    tipo_piel VARCHAR(50),
    sexo VARCHAR(20),
    grupo_sanguineo VARCHAR(10),
    estatura DECIMAL(5,2),
    peso DECIMAL(5,2),
    correo_electronico VARCHAR(100),
    rol VARCHAR(20) DEFAULT 'Paciente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de historias clínicas
CREATE TABLE IF NOT EXISTS historias_clinicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    doctor_id INT,
    admin_id INT,
    fecha_consulta DATE NOT NULL,
    motivo_consulta TEXT,
    diagnostico TEXT,
    tratamiento TEXT,
    medicamentos TEXT,
    observaciones TEXT,
    sintomas TEXT,
    examenes_realizados TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctores(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES administradores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de visitas médicas
CREATE TABLE IF NOT EXISTS visitas_medicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    doctor_id INT,
    historia_clinica_id INT,
    fecha_visita DATETIME NOT NULL,
    motivo VARCHAR(255),
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctores(id) ON DELETE SET NULL,
    FOREIGN KEY (historia_clinica_id) REFERENCES historias_clinicas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de logs
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50),
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    ip_address VARCHAR(45),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_fecha (fecha),
    INDEX idx_accion (accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar administrador por defecto
INSERT INTO administradores (nombres, apellidos, identificacion, usuario, password, rol, cargo) 
VALUES ('Administrador', 'Principal', '1000000000', 'jarodriguez11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Administrador del Sistema');
-- Contraseña: Blink182

-- Log de creación de base de datos
INSERT INTO logs (usuario, accion, descripcion, ip_address) 
VALUES ('SYSTEM', 'CREACION_BD', 'Base de datos EPS creada exitosamente', '127.0.0.1');
