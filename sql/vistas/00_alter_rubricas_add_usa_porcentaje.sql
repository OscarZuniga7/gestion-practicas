USE gestion_practicas;

ALTER TABLE rubricas
  ADD COLUMN IF NOT EXISTS usa_porcentaje TINYINT(1) NOT NULL DEFAULT 0 AFTER practica;

-- Marca las rúbricas de Práctica II (H1, H2) como “por porcentaje”
UPDATE rubricas
SET usa_porcentaje = 1
WHERE practica = 'II' AND hito_id IN (1,2);

-- (Opcional) si la Evaluación Final de P-II también es por porcentaje:
-- UPDATE rubricas SET usa_porcentaje = 1 WHERE practica='II' AND hito_id=3;
