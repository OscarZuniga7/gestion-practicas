<?php
require_once '../includes/db.php';
include '../includes/header.php';

// Cargar datos para los selects (usa los nombres REALES de tablas)
$estudiantes = $pdo->query("
    SELECT id, nombre, rut 
    FROM estudiantes 
    ORDER BY nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

$hitos = $pdo->query("
    SELECT id, descripcion 
    FROM hitos 
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$supervisoresExternos = $pdo->query("
    SELECT id, nombre 
    FROM supervisores 
    WHERE tipo = 'externo'
    ORDER BY nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Fecha por defecto = hoy
$hoy = date('Y-m-d');
?>

<div class="container">
    <h2>Registrar Entrevista con Supervisor Externo</h2>

    <form action="guardar.php" method="post">
        <div class="mb-3">
            <label class="form-label">Estudiante</label>
            <select name="estudiante_id" class="form-select" required>
                <option value="">Seleccione…</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>">
                        <?= htmlspecialchars($e['nombre']) ?> (<?= htmlspecialchars($e['rut']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hito asociado</label>
            <select name="hito_id" class="form-select" required>
                <option value="">Seleccione…</option>
                <?php foreach ($hitos as $h): ?>
                    <option value="<?= $h['id'] ?>">
                        <?= htmlspecialchars($h['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Supervisor externo</label>
            <select name="supervisor_id" class="form-select" required>
                <option value="">Seleccione…</option>
                <?php foreach ($supervisoresExternos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
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

<?php include '../includes/footer.php'; ?>
