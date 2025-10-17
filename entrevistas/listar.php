<?php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

// Mensajes opcionales
$ok  = $_GET['ok']  ?? null;
$msg = $_GET['msg'] ?? null;

/*
 Seleccionamos 1 rúbrica por entrevista:
 - Coincidencia exacta por tipo_supervisor -> prioridad 0
 - Si no hay, cae a 'común' -> prioridad 1
 - Filtramos además por práctica del estudiante (I/II) según est.asignatura
*/
$sql = "
WITH rubrica_candidata AS (
    SELECT
        e.id AS entrevista_id,
        r.id AS rubrica_id,
        ROW_NUMBER() OVER (
            PARTITION BY e.id
            ORDER BY
                CASE WHEN r.tipo_practica = e.tipo_supervisor THEN 0 ELSE 1 END,
                r.id
        ) AS rn
    FROM entrevistas e
    JOIN estudiantes est ON est.id = e.estudiante_id
    JOIN rubricas r
      ON r.hito_id = e.hito_id
     AND r.practica = CASE
                        WHEN est.asignatura LIKE '%II%' THEN 'II'
                        ELSE 'I'
                      END
     AND (r.tipo_practica = e.tipo_supervisor OR r.tipo_practica = 'común')
)

SELECT 
    e.id,
    DATE_FORMAT(e.fecha,'%Y-%m-%d') AS fecha,
    e.comentarios,
    e.evidencia_url,
    e.tipo_supervisor,                          -- 'interno' | 'externo'

    est.id  AS estudiante_id,
    est.nombre AS nombre_estudiante,
    est.asignatura AS practica_estudiante,      -- útil para debug/validación

    h.id   AS hito_id,
    h.descripcion AS descripcion_hito,

    COALESCE(s.nombre, '—') AS nombre_supervisor,

    -- Acta (si existe)
    a.id              AS acta_id,
    a.tipo_entrevista AS acta_tipo,
    a.acta_pdf_url    AS acta_pdf_url,

    -- Rúbrica sugerida (única)
    rc.rubrica_id
FROM entrevistas e
JOIN estudiantes est     ON est.id = e.estudiante_id
JOIN hitos h             ON h.id   = e.hito_id
LEFT JOIN supervisores s ON s.id   = e.supervisor_id
LEFT JOIN actas_entrevista a ON a.entrevista_id = e.id
LEFT JOIN rubrica_candidata rc
       ON rc.entrevista_id = e.id
      AND rc.rn = 1
ORDER BY e.fecha DESC, e.id DESC
";

$entrevistas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
  <h2 class="mb-3">Listado de Entrevistas Registradas</h2>

  <?php if ($ok === 'acta_creada'): ?>
    <div class="alert alert-success">Acta creada correctamente.</div>
  <?php elseif ($ok): ?>
    <div class="alert alert-success"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>

  <?php if ($msg): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <a href="crear.php" class="btn btn-primary mb-3">Registrar Nueva Entrevista</a>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th>Estudiante</th>
          <th>Hito</th>
          <th>Supervisor</th>
          <th>Tipo</th>
          <th>Fecha</th>
          <th style="min-width:200px;">Comentario</th>
          <th>Evidencia</th>
          <th>Acta</th>
          <th style="min-width:240px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$entrevistas): ?>
        <tr><td colspan="9" class="text-center">No hay entrevistas registradas.</td></tr>
      <?php else: ?>
        <?php foreach ($entrevistas as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['nombre_estudiante']) ?></td>
            <td><?= htmlspecialchars($e['descripcion_hito']) ?></td>
            <td><?= htmlspecialchars($e['nombre_supervisor']) ?></td>
            <td><span class="badge bg-dark"><?= htmlspecialchars(ucfirst($e['tipo_supervisor'])) ?></span></td>
            <td><?= htmlspecialchars($e['fecha']) ?></td>
            <td style="max-width:360px"><?= nl2br(htmlspecialchars($e['comentarios'])) ?></td>

            <td>
              <?php if (!empty($e['evidencia_url'])): ?>
                <a href="<?= htmlspecialchars($e['evidencia_url']) ?>" target="_blank" rel="noopener noreferrer">Ver evidencia</a>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>

            <td>
              <?php if ($e['acta_id']): ?>
                <span class="badge bg-success">Acta <?= htmlspecialchars($e['acta_tipo']) ?></span><br>
                <?php if (!empty($e['acta_pdf_url'])): ?>
                  <a class="btn btn-sm btn-outline-success mt-1" href="<?= htmlspecialchars($e['acta_pdf_url']) ?>" target="_blank" rel="noopener noreferrer">PDF</a>
                <?php else: ?>
                  <a class="btn btn-sm btn-outline-secondary mt-1" href="../actas/pdf.php?entrevista_id=<?= (int)$e['id'] ?>">Generar PDF</a>
                <?php endif; ?>
                <a class="btn btn-sm btn-outline-primary mt-1" href="../actas/ver.php?id=<?= (int)$e['acta_id'] ?>">Ver acta</a>
                <a class="btn btn-sm btn-warning mt-1" href="../actas/editar.php?id=<?= (int)$e['acta_id'] ?>">Editar</a>
              <?php else: ?>
                <a class="btn btn-sm btn-primary" href="../actas/crear.php?entrevista_id=<?= (int)$e['id'] ?>">Crear Acta</a>
              <?php endif; ?>
            </td>

            <td class="d-flex flex-wrap gap-2">
              <!-- Flujo recomendado → va al listado de evaluaciones prefiltrado -->
              <a class="btn btn-sm btn-success"
                 href="../evaluaciones/listar.php?estudiante_id=<?= (int)$e['estudiante_id'] ?>&hito_id=<?= (int)$e['hito_id'] ?>&tipo=<?= urlencode($e['tipo_supervisor']) ?>">
                Evaluar
              </a>

              <!-- Acceso directo a la rúbrica única seleccionada -->
              <a class="btn btn-sm btn-outline-success"
                 href="../evaluaciones/evaluacion_rubrica.php?estudiante_id=<?= (int)$e['estudiante_id'] ?>&hito_id=<?= (int)$e['hito_id'] ?>&tipo=<?= urlencode($e['tipo_supervisor']) ?>&rubrica_id=<?= (int)($e['rubrica_id'] ?? 0) ?>">
                Evaluar con rúbrica
              </a>

              <a href="editar.php?id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-primary">Editar</a>

              <a href="eliminar.php?id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('¿Eliminar esta entrevista? Esta acción no se puede deshacer.');">
                 Eliminar
              </a>
              <a class="btn btn-sm btn-dark"
                 href="../actas/express.php?entrevista_id=<?= (int)$e['id'] ?>">
                 Resumen exprés
              </a>

            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
