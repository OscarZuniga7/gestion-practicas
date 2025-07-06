-- Crear base de datos
CREATE DATABASE IF NOT EXISTS gestion_practicas;
USE gestion_practicas;

-- Eliminar tablas si existen (por reinicio)
DROP TABLE IF EXISTS supervisores;
DROP TABLE IF EXISTS estudiantes;
DROP TABLE IF EXISTS empresas;

-- Tabla de empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    rut VARCHAR(15) UNIQUE,
    rubro VARCHAR(100),
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    email_contacto VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de estudiantes
CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rut VARCHAR(12) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    carrera VARCHAR(100),
    telefono VARCHAR(20),
    programa VARCHAR(20),
    asignatura VARCHAR(50),
    empresa_id INT,
    fecha_inicio DATE,
    fecha_fin DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Tabla de supervisores
CREATE TABLE supervisores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cargo VARCHAR(100),
    email VARCHAR(100),
    telefono VARCHAR(20),
    empresa_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Insertar empresas
INSERT INTO empresas (nombre, rut, rubro, direccion, telefono, email_contacto)
VALUES 
('MOLYMETNOS S.A.', '76845690-1', 'Industria química', 'Av. Las Industrias 123, Santiago', '22223333', 'contacto@moly.cl'),
('UNIVERSIDAD ANDRES BELLO', '60803000-0', 'Educación superior', 'Av. República 239, Santiago', '226123456', 'info@unab.cl');

-- Insertar estudiantes
INSERT INTO estudiantes (rut, nombre, email, carrera, telefono, programa, asignatura, empresa_id, fecha_inicio, fecha_fin)
VALUES 
('20981169-3', 'ZAPATA GUEVARA, CONSTANZA BELÉN', 'c.zapataguevara@uandresbello.edu', 'Ingeniería Civil Industrial', '912345678', 'UNAB12100', 'PRACTICA II', 1, '2025-06-02', '2025-08-20'),
('20269725-9', 'BAEZA PEREIRA, NICOLÁS ANDRÉS', 'n.baezapereira@uandresbello.edu', 'Ingeniería Civil Industrial', '911223344', 'UNAB12100', 'PRACTICA I', 2, '2025-03-10', '2025-06-02');

-- Insertar supervisor
INSERT INTO supervisores (nombre, cargo, email, telefono, empresa_id)
VALUES ('Armando Tamponi', 'Docente UNAB / Supervisor Externo', 'arm.munoz@uandresbello.edu', '+56993997982', 2);
