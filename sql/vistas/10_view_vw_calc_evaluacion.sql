USE gestion_practicas;

CREATE OR REPLACE VIEW vw_calc_evaluacion AS
SELECT
  ev.id              AS evaluacion_id,
  ev.estudiante_id,
  ev.hito_id,
  r.id               AS rubrica_id,
  r.tipo_practica,
  r.practica,
  r.usa_porcentaje,

  SUM(c.puntaje_max) AS suma_max,

  SUM(
    CASE
      WHEN r.usa_porcentaje = 1
        THEN IFNULL(ec.puntaje_obtenido,0) / 100.0 * c.puntaje_max
      ELSE
        IFNULL(ec.puntaje_obtenido,0)
    END
  ) AS suma_obtenida,

  ROUND(
    100.0 * SUM(
      CASE
        WHEN r.usa_porcentaje = 1
          THEN IFNULL(ec.puntaje_obtenido,0) / 100.0 * c.puntaje_max
        ELSE
          IFNULL(ec.puntaje_obtenido,0)
      END
    ) / NULLIF(SUM(c.puntaje_max),0)
  , 1) AS pct_obtenido

FROM evaluaciones ev
JOIN evaluaciones_criterios ec ON ec.evaluacion_id = ev.id
JOIN criterios c               ON c.id = ec.criterio_id
JOIN rubricas r                ON r.id = c.rubrica_id
GROUP BY ev.id, ev.estudiante_id, ev.hito_id, r.id, r.tipo_practica, r.practica, r.usa_porcentaje;
