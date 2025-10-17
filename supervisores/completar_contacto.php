<?php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

$solo_pend = isset($_GET['pend']) ? (int)$_GET['pend'] : 1; // por defecto, sólo pendientes

$sql = "SELECT *
        FROM vw_contacto_supervisor_desde_informe";
if ($solo_pend) {
  $sql .= " WHERE (supervisor_id IS NULL
                OR supervisor_email IS NULL OR supervisor_email = ''
                OR supervisor_telefono IS NULL OR supervisor_telefono = '')";
}
$sql .= " ORDER BY empresa, estudiante, hito_id";

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container my-4">
  <h3>Completar datos de Supervisores Externos</h3>
  <div class="mb-3">
    <a class="btn btn-sm btn-outline-secondary"
       href="?pend=<?= $solo_pend ? 0 : 1 ?>">
       <?= $solo_pend ? 'Ver todos' : 'Ver sólo pendientes' ?>
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-sm table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Empresa</th>
          <th>Estudiante</th>
          <th>Práctica/Hito</th>
          <th>Informe</th>
          <th>Supervisor actual</th>
          <th>Contacto</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="7" class="text-center">Sin registros.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($r['empresa']) ?></div>
            <div class="text-muted small"><?= htmlspecialchars($r['email_empresa'] ?? '') ?></div>
          </td>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($r['estudiante']) ?></div>
            <div class="text-muted small"><?= htmlspecialchars($r['email_estudiante']) ?></div>
          </td>
          <td><?= htmlspecialchars($r['practica']) ?> · Hito <?= (int)$r['hito_id'] ?></td>
          <td>
            <?php if (!empty($r['informe_archivo'])): ?>
              <a class="btn btn-sm btn-outline-primary" target="_blank"
                 href="<?= htmlspecialchars($r['informe_archivo']) ?>">Abrir informe</a><br>
              <small class="text-muted"><?= htmlspecialchars($r['informe_fecha'] ?? '') ?></small>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td>
            <?php if ($r['supervisor_id']): ?>
              <div class="fw-semibold"><?= htmlspecialchars($r['supervisor'] ?: '[sin nombre]') ?></div>
              <div class="text-muted small"><?= htmlspecialchars($r['supervisor_cargo'] ?: '') ?></div>
            <?php else: ?>
              <span class="badge bg-warning text-dark">No creado</span>
            <?php endif; ?>
          </td>
          <td class="small">
            Email: <?= htmlspecialchars($r['supervisor_email'] ?: '—') ?><br>
            Fono:  <?= htmlspecialchars($r['supervisor_telefono'] ?: '—') ?>
          </td>
          <td class="d-flex flex-wrap gap-2">
            <?php if ($r['supervisor_id']): ?>
              <a class="btn btn-sm btn-primary"
                 href="editar.php?id=<?= (int)$r['supervisor_id'] ?>">Editar</a>
            <?php else: ?>
              <a class="btn btn-sm btn-success"
                 href="crear.php?empresa_id=<?= (int)$r['empresa_id'] ?>&tipo=externo&prefijo=<?= urlencode($r['estudiante'].' / '.$r['empresa']) ?>">
                 Crear supervisor
              </a>
            <?php endif; ?>

            <?php if (!empty($r['email_empresa']) || !empty($r['supervisor_email'])):
              $to = $r['supervisor_email'] ?: $r['email_empresa'];
              $cc = $r['email_estudiante'] ?? '';
              $subject = "Entrevista de práctica – Hito {$r['hito_id']} – {$r['estudiante']} / {$r['empresa']}";
              $body = "Estimada/o,\n\nPara cerrar el hito necesitamos una breve entrevista (15-20 min).\n"
                    . "Opciones (hora CL):\n- Hoy 15:00-16:30\n- Mañana 09:00-10:30\n- Mié 15:00-16:30\n\n"
                    . "Quedo atenta/o. Gracias.";
              $mailto = "mailto:" . rawurlencode($to)
                      . ($cc ? "?cc=" . rawurlencode($cc) : "")
                      . "&subject=" . rawurlencode($subject)
                      . "&body=" . rawurlencode($body);
            ?>
              <a class="btn btn-sm btn-outline-secondary" href="<?= $mailto ?>">Contactar</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
