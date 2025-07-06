USE gestion_practicas;

-- Crear tabla Hitos (catálogo fijo)
CREATE TABLE IF NOT EXISTS hitos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);

-- Insertar hitos estándar
INSERT INTO hitos (nombre, descripcion)
VALUES 
('Hito 1', 'Entrega inicial del plan de trabajo'),
('Hito 2', 'Avance intermedio de la práctica'),
('Evaluación Final', 'Evaluación del desempeño al finalizar la práctica');

-- Crear tabla Informes
CREATE TABLE IF NOT EXISTS informes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    hito_id INT NOT NULL,
    fecha_entrega DATE,
    archivo VARCHAR(255),
    comentarios TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
        ON DELETE CASCADE,
    FOREIGN KEY (hito_id) REFERENCES hitos(id)
        ON DELETE CASCADE
);

-- Ejemplo de informe entregado por Constanza (id estudiante = 1, Hito 1)
INSERT INTO informes (estudiante_id, hito_id, fecha_entrega, archivo, comentarios)
VALUES 
(1, 1, '2025-06-15', 'hito1_constanza.pdf', 'Entregado puntualmente con plan claro');

-- Crear tabla Evaluaciones (con campo archivo)
CREATE TABLE IF NOT EXISTS evaluaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    hito_id INT,
    supervisor TEXT,
    nota DECIMAL(4,2),
    observaciones TEXT,
    archivo VARCHAR(255),  -- NUEVO: URL de PDF o ruta
    fecha_evaluacion DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
        ON DELETE CASCADE,
    FOREIGN KEY (hito_id) REFERENCES hitos(id)
        ON DELETE SET NULL
);


-- Ejemplo de evaluación realizada por Oscar a Nicolás (id estudiante = 2, Evaluación Final)
INSERT INTO evaluaciones (estudiante_id, hito_id, supervisor, nota, observaciones, fecha_evaluacion)
VALUES 
(2, 3, 'Oscar Zúñiga', 6.5, 'Buen cierre de práctica, presentación clara.', '2025-06-10');
