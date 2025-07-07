-- Crear tabla entrevistas
CREATE TABLE IF NOT EXISTS entrevistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    hito_id INT NOT NULL,
    fecha DATE NOT NULL,
    modalidad VARCHAR(50), -- presencial, online, híbrida, etc.
    evidencia_url TEXT,
    comentarios TEXT,
    supervisor_id INT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    FOREIGN KEY (hito_id) REFERENCES hitos(id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES supervisores(id) ON DELETE SET NULL
);

-- Insertar entrevista de ejemplo
INSERT INTO entrevistas (estudiante_id, hito_id, fecha, modalidad, evidencia_url, comentarios, supervisor_id)
VALUES 
(1, 1, '2025-06-20', 'presencial', 'https://uandresbelloedu.sharepoint.com/.../entrevista_hito1.pdf', 'Primera entrevista completada.', 1);
