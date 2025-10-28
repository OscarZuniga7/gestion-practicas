<?php
require_once('../includes/db.php');
include('../includes/header.php');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Traer evaluaciones
$sql = "SELECT ev.*,
               est.nombre AS nombre_estudiante,
               h.nombre   AS nombre_hito
        FROM evaluaciones ev
        JOIN estudiantes est ON ev.estudiante_id = est.id
        LEFT JOIN hitos h    ON ev.hito_id = h.id
        ORDER BY ev.fecha_evaluacion DESC";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  /* Si algún contenedor tapa el dropdown del header */
  .dropdown-menu { z-index: 2000; }

  /* Pastillas de estado */
  .chip {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.25rem .5rem; border-radius:999px; font-size:.85rem;
  }
  .chip .dot {
    width:.55rem; height:.55rem; border-radius:50%;
    display:inline-block;
  }
  .chip-success { background:#e8f7ee; color:#166534; border:1px solid #b7e1c3; }
  .chip-success .dot { background:#22c55e; }
  .chip-muted   { background:#f2f4f7; color:#475467; border:1px solid #e5e7eb; }
  .chip-muted .dot { background:#9ca3af; }
</style>

<div class="container mt-5">
  <div class="d-flex align-items-center justify-content-between">
    <h2 class="mb-3">Listado de Evaluaciones</h2>
    <div class="text-muted small">Total: <?= count($rows) ?></div>
  </div>

  <div class="d-flex gap-2 mb-3">
    <a href="crear.php" class="btn btn-primary">Nueva Evaluación</a>
    <a href="evaluacion_rubrica.php" class="btn btn-success">Evaluar con Rúbrica</a>
  </div>

  <?php if (!empty($_GET['ok']) && $_GET['ok']==='detalle_url'): ?>
    <div class="alert alert-success">URL de detalle guardada correctamente.</div>
  <?php endif; ?>
  <?php if (!empty($_GET['err'])): ?>
    <div class="alert alert-danger">No se pudo guardar la URL del detalle (código: <?= h($_GET['err']) ?>).</div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Estudiante</th>
          <th>Hito</th>
          <th>Supervisor</th>
          <th>Nota</th>
          <th>Fecha Evaluación</th>
          <th>Observaciones</th>
          <th>Detalle rúbrica</th>
          <th style="width:200px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($rows): foreach ($rows as $fila):
            $id    = (int)$fila['id'];
            $urlD  = trim((string)($fila['detalle_pdf_url'] ?? ''));
            $tiene = ($urlD !== '');
      ?>
        <tr>
          <td><?= $id ?></td>
          <td><?= h($fila['nombre_estudiante']) ?></td>
          <td><?= h($fila['nombre_hito'] ?? '-') ?></td>
          <td><?= h($fila['supervisor']) ?></td>
          <td><?= h($fila['nota']) ?></td>
          <td><?= h($fila['fecha_evaluacion']) ?></td>
          <td><?= h($fila['observaciones']) ?></td>

          <!-- Columna estado + acceso rápido -->
          <td class="text-nowrap">
            <?php if ($tiene): ?>
              <span class="chip chip-success me-2">
                <span class="dot"></span> URL cargada
              </span>
              <a class="btn btn-sm btn-outline-success"
                 href="<?= h($urlD) ?>" target="_blank" rel="noopener">Ver detalle</a>
            <?php else: ?>
              <span class="chip chip-muted me-2">
                <span class="dot"></span> Sin URL
              </span>
              <a class="btn btn-sm btn-outline-secondary disabled" tabindex="-1" aria-disabled="true">Ver detalle</a>
            <?php endif; ?>
          </td>

          <!-- Acciones -->
          <td class="d-flex flex-wrap gap-2">
            <a href="editar.php?id=<?= $id ?>" class="btn btn-sm btn-warning">Editar</a>

            <a href="eliminar.php?id=<?= $id ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('¿Seguro que deseas eliminar esta evaluación?');">Eliminar</a>

            <!-- Generación servidor del PDF de detalle -->
            <a class="btn btn-sm btn-outline-danger"
               href="pdf_detalle.php?evaluacion_id=<?= $id ?>&download=1">PDF rúbrica</a>

            <!-- Botón para cargar/editar la URL (modal) -->
            <button type="button"
                    class="btn btn-sm <?= $tiene ? 'btn-outline-primary' : 'btn-outline-secondary' ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDetalleURL"
                    data-evalid="<?= $id ?>"
                    data-url="<?= h($urlD) ?>">
              <?= $tiene ? 'Editar URL' : 'Agregar URL' ?>
            </button>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="9">Sin evaluaciones</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: guardar/editar URL de detalle de rúbrica -->
<div class="modal fade" id="modalDetalleURL" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="guardar_detalle_url.php">
      <div class="modal-header">
        <h5 class="modal-title">URL Detalle de Rúbrica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="evaluacion_id" id="mdl-evalid">
        <label class="form-label">Pega el enlace (SharePoint/OneDrive, https)</label>
        <input type="url" name="detalle_pdf_url" id="mdl-url" class="form-control"
               placeholder="https://tu-tenant.sharepoint.com/.../Detalle_Rubrica_....pdf" required>
        <div class="form-text">
          Guarda sólo enlaces <strong>HTTPS</strong>. Idealmente del SharePoint/OneDrive institucional.
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
// Rellena el modal con los datos del botón que lo abre
document.getElementById('modalDetalleURL')?.addEventListener('show.bs.modal', function (ev) {
  const btn = ev.relatedTarget;
  document.getElementById('mdl-evalid').value = btn?.getAttribute('data-evalid') || '';
  document.getElementById('mdl-url').value    = btn?.getAttribute('data-url')    || '';
});
</script>
