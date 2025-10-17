<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Preselección opcional (por si llegas desde otra vista)
$preEst = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : 0;
$preHito = isset($_GET['hito_id']) ? (int)$_GET['hito_id'] : 0;

// Estudiantes con empresa_id
$estudiantes = $pdo->query("
    SELECT id, nombre, rut, empresa_id
    FROM estudiantes
    ORDER BY nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Hitos
$hitos = $pdo->query("
    SELECT id, nombre
    FROM hitos
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Supervisores externos con empresa_id
$supervisoresExternos = $pdo->query("
    SELECT id, nombre, empresa_id, email
    FROM supervisores
    WHERE tipo = 'externo'
    ORDER BY empresa_id ASC, nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Si viene estudiante por GET, intenta sugerir supervisor (primer externo de la misma empresa)
$preSup = 0;
if ($preEst) {
    $stmt = $pdo->prepare("
        SELECT s.id
        FROM supervisores s
        JOIN estudiantes e ON e.empresa_id = s.empresa_id
        WHERE e.id = ? AND s.tipo = 'externo'
        ORDER BY s.nombre ASC
        LIMIT 1
    ");
    $stmt->execute([$preEst]);
    $preSup = (int)($stmt->fetchColumn() ?: 0);
}

// Fecha por defecto = hoy
$hoy = date('Y-m-d');
?>
<div class="container">
  <h2>Registrar Entrevista con Supervisor Externo</h2>

  <form action="guardar.php" method="post" id="frmEntrevista">
    <div class="mb-3">
      <label class="form-label">Estudiante</label>
      <select name="estudiante_id" id="estudiante_id" class="form-select" required>
        <option value="">Seleccione…</option>
        <?php foreach ($estudiantes as $e): ?>
          <option 
            value="<?= $e['id'] ?>"
            data-empresa="<?= (int)$e['empresa_id'] ?>"
            <?= $preEst === (int)$e['id'] ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($e['nombre']) ?> (<?= htmlspecialchars($e['rut']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <div class="form-text">Se usará la empresa del estudiante para sugerir el supervisor externo.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Hito asociado</label>
      <select name="hito_id" class="form-select" required>
        <option value="">Seleccione…</option>
        <?php foreach ($hitos as $h): ?>
          <option value="<?= $h['id'] ?>" <?= $preHito === (int)$h['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($h['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Supervisor externo</label>
      <select name="supervisor_id" id="supervisor_id" class="form-select" required>
        <option value="">Seleccione…</option>
        <?php foreach ($supervisoresExternos as $s): ?>
          <option 
            value="<?= $s['id'] ?>"
            data-empresa="<?= (int)$s['empresa_id'] ?>"
            data-email="<?= htmlspecialchars($s['email'] ?? '') ?>"
            <?= $preSup === (int)$s['id'] ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($s['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div id="ayudaSupervisor" class="form-text"></div>
    </div>

    <div class="row g-3">
      <div class="col-sm-4">
        <label class="form-label">Fecha de entrevista</label>
        <input type="date" name="fecha" value="<?= $hoy ?>" class="form-control" required>
      </div>
      <div class="col-sm-4">
        <label class="form-label">Modalidad (opcional)</label>
        <select name="modalidad" class="form-select">
          <option value="">—</option>
          <option value="presencial">Presencial</option>
          <option value="online">Online</option>
          <option value="mixta">Mixta</option>
        </select>
      </div>
      <div class="col-sm-4">
        <label class="form-label">Tipo de supervisor</label>
        <input type="text" class="form-control" value="externo" disabled>
        <input type="hidden" name="tipo_supervisor" value="externo">
      </div>
    </div>

    <div class="mb-3 mt-3">
      <label class="form-label">Comentarios</label>
      <textarea name="comentarios" class="form-control" rows="3" placeholder="Resumen de la conversación, acuerdos, compromisos, etc."></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">URL de evidencia (PDF/Doc/Audio en SharePoint)</label>
      <input type="url" name="evidencia_url" class="form-control" placeholder="https://…">
    </div>

    <button type="submit" class="btn btn-primary">Guardar entrevista</button>
    <a href="listar.php" class="btn btn-secondary">Volver</a>
  </form>
</div>

<script>
// Filtra el combo de supervisores por empresa del estudiante seleccionado
(function() {
  const selEst = document.getElementById('estudiante_id');
  const selSup = document.getElementById('supervisor_id');
  const ayuda = document.getElementById('ayudaSupervisor');

  // Guardamos todas las opciones originales
  const allOptions = Array.from(selSup.querySelectorAll('option')).map(o => ({
    value: o.value,
    text: o.textContent,
    empresa: o.getAttribute('data-empresa'),
    email: o.getAttribute('data-email') || '',
    selected: o.selected
  }));

  function filtrarSupervisores() {
    const optSel = selEst.options[selEst.selectedIndex];
    const empresaId = optSel ? optSel.getAttribute('data-empresa') : '';

    // reconstruir opciones
    selSup.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Seleccione…';
    selSup.appendChild(placeholder);

    if (!empresaId) {
      ayuda.textContent = 'Primero seleccione un estudiante para sugerir supervisores por empresa.';
      return;
    }

    const candidatos = allOptions.filter(o => o.value && o.empresa === empresaId);
    if (candidatos.length === 0) {
      ayuda.textContent = 'No hay supervisores externos registrados para la empresa de este estudiante.';
      // También puedes dejar TODA la lista como fallback:
      allOptions.forEach(o => {
        if (o.value) {
          const opt = document.createElement('option');
          opt.value = o.value;
          opt.textContent = o.text;
          opt.setAttribute('data-empresa', o.empresa || '');
          opt.setAttribute('data-email', o.email || '');
          selSup.appendChild(opt);
        }
      });
      return;
    }

    // Hay candidatos de la misma empresa → listarlos
    candidatos.forEach(o => {
      const opt = document.createElement('option');
      opt.value = o.value;
      opt.textContent = o.text;
      opt.setAttribute('data-empresa', o.empresa || '');
      opt.setAttribute('data-email', o.email || '');
      selSup.appendChild(opt);
    });

    // Si hay uno solo, preseleccionar
    if (candidatos.length === 1) {
      selSup.value = candidatos[0].value;
      ayuda.textContent = `Sugerido: ${candidatos[0].text} (misma empresa)`;
    } else {
      ayuda.textContent = `Se muestran ${candidatos.length} supervisores externos de la misma empresa.`;
    }
  }

  selEst.addEventListener('change', filtrarSupervisores);

  // Si venía preseleccionado el estudiante (por GET), filtra al cargar
  if (selEst.value) {
    filtrarSupervisores();
  }
})();
</script>

<?php include '../includes/footer.php'; ?>
