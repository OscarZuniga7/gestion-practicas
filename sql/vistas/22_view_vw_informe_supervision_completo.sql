USE gestion_practicas;

CREATE OR REPLACE VIEW vw_informe_supervision_completo AS
SELECT
  e.id            AS id_estudiante,
  e.nombre        AS estudiante,
  e.rut,
  e.email,
  em.nombre       AS empresa,
  e.asignatura    AS practica,
  h.id            AS hito_id,
  h.nombre        AS hito,
  e.fecha_inicio,
  e.fecha_fin,

  CASE WHEN i.estudiante_id IS NULL THEN 'Pendiente' ELSE 'Entregado' END AS estado_informe,
  i.fecha_entrega  AS informe_fecha,

  CASE WHEN ev.id IS NULL THEN 'No evaluado'
       ELSE CONCAT('Nota rúbrica: ', ROUND(v.suma_obtenida), '/', v.suma_max)
  END                AS estado_evaluacion,
  ev.fecha_registro  AS evaluacion_fecha,

  CONCAT('Nota rúbrica: ', ROUND(v.suma_obtenida), '/', v.suma_max) AS nota_rubrica_texto,
  CONCAT(ROUND(v.pct_obtenido,1), '%')                               AS nota_rubrica_pct,

  CASE WHEN en.estudiante_id IS NULL THEN 'Sin entrevista'
       ELSE COALESCE(en.modalidad,'Registrada')
  END              AS estado_entrevista,
  en.fecha         AS entrevista_fecha,

  GREATEST(
    COALESCE(i.fecha_entrega,   '1000-01-01'),
    COALESCE(ev.fecha_registro, '1000-01-01'),
    COALESCE(en.fecha,          '1000-01-01')
  ) AS ultima_actualizacion

FROM estudiantes e
CROSS JOIN hitos h
LEFT JOIN empresas em
  ON em.id = e.empresa_id

LEFT JOIN (
  SELECT estudiante_id, hito_id, MAX(fecha_entrega) AS max_fecha
  FROM informes
  GROUP BY estudiante_id, hito_id
) ui ON ui.estudiante_id = e.id AND ui.hito_id = h.id
LEFT JOIN informes i
  ON i.estudiante_id = e.id
 AND i.hito_id       = h.id
 AND i.fecha_entrega = ui.max_fecha

LEFT JOIN (
  SELECT estudiante_id, hito_id, MAX(fecha_registro) AS max_ev
  FROM evaluaciones
  GROUP BY estudiante_id, hito_id
) ue ON ue.estudiante_id = e.id AND ue.hito_id = h.id
LEFT JOIN evaluaciones ev
  ON ev.estudiante_id   = e.id
 AND ev.hito_id         = h.id
 AND ev.fecha_registro  = ue.max_ev
LEFT JOIN vw_calc_evaluacion v
  ON v.evaluacion_id    = ev.id

LEFT JOIN (
  SELECT estudiante_id, hito_id, MAX(fecha) AS max_fecha
  FROM entrevistas
  GROUP BY estudiante_id, hito_id
) un ON un.estudiante_id = e.id AND un.hito_id = h.id
LEFT JOIN entrevistas en
  ON en.estudiante_id = e.id
 AND en.hito_id       = h.id
 AND en.fecha         = un.max_fecha

ORDER BY e.nombre, h.id;
