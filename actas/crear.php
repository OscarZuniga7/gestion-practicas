<?php
// actas/crear.php
require_once '../includes/db.php';
include '../includes/header.php';

// 1) Validar entrevista_id
$entrevista_id = isset($_GET['entrevista_id']) ? (int) $_GET['entrevista_id'] : 0;
if ($entrevista_id <= 0) {
    echo '<div class="container"><div class="alert alert-danger">Falta entrevista_id.</div></div>';
    include '../includes/footer.php'; exit;
}

// 2) Traer contexto de la entrevista (JOINs livianos y seguros)
$sql = "
SELECT 
    e.id AS entrevista_id,
    e.fecha AS fecha_entrevista,
    e.modalidad,
    e.comentarios AS comentarios_entrevista,
    est.id AS estudiante_id,
    est.nombre AS estudiante_nombre,
    est.rut AS estudiante_rut,
    emp.id AS empresa_id,
    emp.nombre AS empresa_nombre,
    sup.id AS supervisor_id,
    sup.nombre AS supervisor_nombre,
    sup.cargo AS supervisor_cargo,
    sup.email AS supervisor_email,
    sup.telefono AS supervisor_telefono,
    h.id AS hito_id,
    h.descripcion AS hito_descripcion
FROM entrevistas e
JOIN estudiantes est   ON est.id = e.estudiante_id
LEFT JOIN empresas emp ON emp.id = est.empresa_id
JOIN supervisores sup  ON sup.id = e.supervisor_id
JOIN hitos h           ON h.id = e.hito_id
WHERE e.id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$entrevista_id]);
$ctx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ctx) {
    echo '<div class="container"><div class="alert alert-danger">No se encontró la entrevista.</div></div>';
    include '../includes/footer.php'; exit;
}

// Sugerencia auto: si el hito contiene "Hito 1" -> intermedia; si no, final (solo como valor por defecto)
$tipo_sugerido = (stripos($ctx['hito_descripcion'] ?? '', '1') !== false) ? 'intermedia' : 'final';
$hoy = date('Y-m-d');
?>

<div class="container">
  <h2>Crear Acta de Entrevista</h2>

  <!-- Contexto solo lectura -->
  <div class="card mb-3">
    <div class="card-header">Datos generales</div>
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <p class="mb-1"><strong>Estudiante:</strong> <?= htmlspecialchars($ctx['estudiante_nombre']) ?> (<?= htmlspecialchars($ctx['estudiante_rut']) ?>)</p>
          <p class="mb-1"><strong>Empresa:</strong> <?= htmlspecialchars($ctx['empresa_nombre'] ?? '—') ?></p>
          <p class="mb-1"><strong>Hito:</strong> <?= htmlspecialchars($ctx['hito_descripcion']) ?></p>
        </div>
        <div class="col-sm-6">
          <p class="mb-1"><strong>Supervisor Empresa:</strong> <?= htmlspecialchars($ctx['supervisor_nombre']) ?></p>
          <p class="mb-1"><strong>Cargo:</strong> <?= htmlspecialchars($ctx['supervisor_cargo'] ?? '—') ?></p>
          <p class="mb-1"><strong>Fecha Entrevista:</strong> <?= htmlspecialchars($ctx['fecha_entrevista']) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulario de ACTA -->
  <form action="guardar.php" method="post">
    <input type="hidden" name="entrevista_id" value="<?= (int)$ctx['entrevista_id'] ?>">

    <div class="card mb-3">
      <div class="card-header">Datos de la Entrevista</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label d-block">Tipo de entrevista</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipo_entrevista" id="tipo_intermedia" value="intermedia" <?= $tipo_sugerido==='intermedia'?'checked':'' ?>>
              <label class="form-check-label" for="tipo_intermedia">Intermedia (post‑Hito 1)</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipo_entrevista" id="tipo_final" value="final" <?= $tipo_sugerido==='final'?'checked':'' ?>>
              <label class="form-check-label" for="tipo_final">Final (post‑Hito 2)</label>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Link reunión videoconferencia (opcional)</label>
            <input type="url" name="link_vc" class="form-control" placeholder="https://..." >
          </div>
          <div class="col-md-4">
            <label class="form-label">Fecha de firma (opcional)</label>
            <input type="date" name="fecha_firma" value="<?= $hoy ?>" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Temas abordados</div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">1) Evaluación general del desempeño</label>
          <textarea name="eval_general" class="form-control" rows="4" placeholder="Breve resumen de lo comentado por el supervisor de práctica"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">2) Fortalezas observadas</label>
          <textarea name="fortalezas" class="form-control" rows="4" placeholder="Habilidades técnicas, blandas, etc."></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">3) Áreas de mejora / falencias</label>
          <textarea name="mejoras" class="form-control" rows="4" placeholder="Observaciones para retroalimentación académica"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">4) Sugerencias o recomendaciones del supervisor</label>
          <textarea name="sugerencias" class="form-control" rows="4" placeholder="Opcional, útil para ajustar programas de asignaturas"></textarea>
        </div>
      </div>
    </div>

    <!-- Vinculación futura: solo si tipo = final -->
    <div id="bloqueVinculacion" class="card mb-3">
      <div class="card-header">Vinculación futura (solo entrevistas finales)</div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label d-block">¿Recibiría nuevamente practicantes UNAB?</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="recibi_practicantes" value="si" id="recibi_si">
            <label class="form-check-label" for="recibi_si">Sí</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="recibi_practicantes" value="no" id="recibi_no">
            <label class="form-check-label" for="recibi_no">No</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="recibi_practicantes" value="tal_vez" id="recibi_tv">
            <label class="form-check-label" for="recibi_tv">Tal vez</label>
          </div>
          <textarea name="recibi_obs" class="form-control mt-2" rows="2" placeholder="Observaciones (opcional)"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">¿Le interesaría un convenio de colaboración?</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="convenio" value="si" id="conv_si">
            <label class="form-check-label" for="conv_si">Sí</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="convenio" value="no" id="conv_no">
            <label class="form-check-label" for="conv_no">No</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="convenio" value="tal_vez" id="conv_tv">
            <label class="form-check-label" for="conv_tv">Tal vez</label>
          </div>
          <textarea name="convenio_obs" class="form-control mt-2" rows="2" placeholder="Observaciones (opcional)"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">¿Interés en otras formas de vinculación?</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="otras_vinc" value="si" id="ov_si">
            <label class="form-check-label" for="ov_si">Sí</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="otras_vinc" value="no" id="ov_no">
            <label class="form-check-label" for="ov_no">No</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="otras_vinc" value="tal_vez" id="ov_tv">
            <label class="form-check-label" for="ov_tv">Tal vez</label>
          </div>
          <textarea name="otras_vinc_det" class="form-control mt-2" rows="2" placeholder="Especifique (charlas, proyectos, capacitaciones, etc.)"></textarea>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Observaciones adicionales del docente</div>
      <div class="card-body">
        <textarea name="obs_docente" class="form-control" rows="3" placeholder="Detalles relevantes, dificultades, sugerencias"></textarea>
      </div>
    </div>

    <div class="mb-4">
      <button type="submit" class="btn btn-primary">Guardar Acta</button>
      <a href="../entrevistas/listar.php" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>

<script>
  // Mostrar/ocultar bloque de vinculación según tipo de entrevista
  function toggleVinculacion() {
    const finalSel = document.getElementById('tipo_final').checked;
    document.getElementById('bloqueVinculacion').style.display = finalSel ? 'block' : 'none';
  }
  document.getElementById('tipo_intermedia').addEventListener('change', toggleVinculacion);
  document.getElementById('tipo_final').addEventListener('change', toggleVinculacion);
  // estado inicial
  toggleVinculacion();
</script>

<?php include '../includes/footer.php'; ?>
