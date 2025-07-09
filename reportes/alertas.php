<?php
include('../includes/db.php');
include('../includes/header.php');

// Informes vencidos
$informes = $pdo->query("SELECT i.id, e.nombre AS estudiante, h.nombre AS hito, i.fecha_entrega,
    DATEDIFF(CURDATE(), i.fecha_entrega) AS dias_vencido
    FROM informes i
    JOIN estudiantes e ON i.estudiante_id = e.id
    JOIN hitos h ON i.hito_id = h.id
    WHERE i.fecha_entrega < CURDATE()")->fetchAll();

// Evaluaciones pendientes
$evaluaciones = $pdo->query("SELECT ev.id, e.nombre AS estudiante, h.nombre AS hito, ev.fecha_evaluacion,
    DATEDIFF(CURDATE(), ev.fecha_evaluacion) AS dias_vencido
    FROM evaluaciones ev
    JOIN estudiantes e ON ev.estudiante_id = e.id
    JOIN hitos h ON ev.hito_id = h.id
    WHERE ev.fecha_evaluacion < CURDATE()
    AND (ev.nota IS NULL OR ev.archivo IS NULL)")->fetchAll();

// Entrevistas vencidas sin evidencia
$entrevistas = $pdo->query("SELECT en.id, e.nombre AS estudiante, h.nombre AS hito, en.fecha,
    DATEDIFF(CURDATE(), en.fecha) AS dias_vencido,
    en.modalidad, en.evidencia_url, s.nombre AS supervisor
    FROM entrevistas en
    JOIN estudiantes e ON en.estudiante_id = e.id
    JOIN hitos h ON en.hito_id = h.id
    JOIN supervisores s ON en.supervisor_id = s.id
    WHERE en.fecha < CURDATE()
    AND (en.evidencia_url IS NULL OR en.evidencia_url = '')")->fetchAll();
?>

<div class="container mt-4">
  <h2 class="mb-4">Alertas de Tareas Vencidas</h2>

  <h4>Informes Vencidos</h4>
  <table class="table table-bordered">
    <thead><tr><th>Estudiante</th><th>Hito</th><th>Fecha Entrega</th><th>Días Vencido</th></tr></thead>
    <tbody>
      <?php foreach ($informes as $i): ?>
        <tr>
          <td><?= $i['estudiante'] ?></td>
          <td><?= $i['hito'] ?></td>
          <td><?= $i['fecha_entrega'] ?></td>
          <td class="text-danger fw-bold">+<?= $i['dias_vencido'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h4>Evaluaciones Pendientes</h4>
  <table class="table table-bordered">
    <thead><tr><th>Estudiante</th><th>Hito</th><th>Fecha Evaluación</th><th>Días Vencido</th></tr></thead>
    <tbody>
      <?php foreach ($evaluaciones as $e): ?>
        <tr>
          <td><?= $e['estudiante'] ?></td>
          <td><?= $e['hito'] ?></td>
          <td><?= $e['fecha_evaluacion'] ?></td>
          <td class="text-danger fw-bold">+<?= $e['dias_vencido'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h4>Entrevistas sin Evidencia</h4>
  <table class="table table-bordered">
    <thead><tr><th>Estudiante</th><th>Hito</th><th>Fecha</th><th>Modalidad</th><th>Supervisor</th><th>Días Vencido</th></tr></thead>
    <tbody>
      <?php foreach ($entrevistas as $en): ?>
        <tr>
          <td><?= $en['estudiante'] ?></td>
          <td><?= $en['hito'] ?></td>
          <td><?= $en['fecha'] ?></td>
          <td><?= $en['modalidad'] ?></td>
          <td><?= $en['supervisor'] ?></td>
          <td class="text-danger fw-bold">+<?= $en['dias_vencido'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include('../includes/footer.php'); ?>
