<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Mensajes opcionales
$ok  = $_GET['ok']  ?? null;
$msg = $_GET['msg'] ?? null;

// Traer entrevistas + contexto + estado de acta
$query = "
    SELECT 
        e.id,
        e.fecha,
        e.comentarios,
        e.evidencia_url,
        e.tipo_supervisor,
        est.id  AS estudiante_id,
        est.nombre AS nombre_estudiante,
        h.id   AS hito_id,
        h.descripcion AS descripcion_hito,
        s.nombre AS nombre_supervisor,
        -- Acta (si existe)
        a.id AS acta_id,
        a.tipo_entrevista AS acta_tipo,
        a.acta_pdf_url AS acta_pdf_url
    FROM entrevistas e
    JOIN estudiantes est ON e.estudiante_id = est.id
    JOIN hitos h ON e.hito_id = h.id
    JOIN supervisores s ON e.supervisor_id = s.id
    LEFT JOIN actas_entrevista a ON a.entrevista_id = e.id
    ORDER BY e.fecha DESC, e.id DESC
";
$entrevistas = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Listado de Entrevistas Registradas</h2>

    <?php if ($ok === 'acta_creada'): ?>
        <div class="alert alert-success">Acta creada correctamente.</div>
    <?php elseif ($ok): ?>
        <div class="alert alert-success"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <a href="crear.php" class="btn btn-primary mb-3">Registrar Nueva Entrevista</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Hito</th>
                <th>Supervisor</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Comentario</th>
                <th>Evidencia</th>
                <th>Acta</th>
                <th>Acciones</th>
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
                    <td><?= ucfirst($e['tipo_supervisor']) ?></td>
                    <td><?= htmlspecialchars($e['fecha']) ?></td>
                    <td style="max-width:280px"><?= nl2br(htmlspecialchars($e['comentarios'])) ?></td>
                    <td>
                        <?php if (!empty($e['evidencia_url'])): ?>
                            <a href="<?= htmlspecialchars($e['evidencia_url']) ?>" target="_blank">Ver evidencia</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($e['acta_id']): ?>
                            <span class="badge bg-success">Acta <?= htmlspecialchars($e['acta_tipo']) ?></span><br>
                            <?php if (!empty($e['acta_pdf_url'])): ?>
                                <a class="btn btn-sm btn-outline-success mt-1" href="<?= htmlspecialchars($e['acta_pdf_url']) ?>" target="_blank">PDF</a>
                            <?php else: ?>
                                <a class="btn btn-sm btn-outline-secondary mt-1" href="../actas/pdf.php?entrevista_id=<?= (int)$e['id'] ?>">Generar PDF</a>
                            <?php endif; ?>
                            <a class="btn btn-sm btn-outline-primary mt-1" href="../actas/editar.php?entrevista_id=<?= (int)$e['id'] ?>">Acta</a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-primary" href="../actas/crear.php?entrevista_id=<?= (int)$e['id'] ?>">Crear Acta</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Botón EVALUAR (manteniendo tu flujo existente).
                             Si tu evaluacion_rubrica.php usa otros parámetros, ajusta aquí. -->
                        <a href="../evaluaciones/evaluacion_rubrica.php?estudiante_id=<?= (int)$e['estudiante_id'] ?>&hito_id=<?= (int)$e['hito_id'] ?>&tipo=externo"
                           class="btn btn-sm btn-success">Evaluar</a>

                        <a href="editar.php?id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-primary">Editar</a>

                        <a href="eliminar.php?id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Eliminar esta entrevista? Esta acción no se puede deshacer.');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
