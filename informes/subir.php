<?php
// subir.php
require_once '../includes/db.php';
require_once '../includes/header.php';

// Obtener todos los estudiantes
$stmt = $pdo->query("SELECT id, nombre, rut FROM estudiantes ORDER BY nombre ASC");
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables para formularios
$anio_actual = date('Y');
$mes_en_actual = date('F'); // Nombre del mes en inglés (puedes traducir si prefieres)
$hito_nombre = 'Hito_1'; // Por ahora fijo, se puede extender a más hitos
$mensaje = '';

$meses = [
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //$nombre = $_POST['nombre'];
    $estudiante_id = $_POST['estudiante_id'];

    $stmt = $pdo->prepare("SELECT nombre, rut FROM estudiantes WHERE id = ?");
    $stmt->execute([$estudiante_id]);
    $datosEstudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombre = $datosEstudiante['nombre'];
    $nombre = str_replace(',', '', $nombre); // Elimina la coma
    $rut_estudiante = $datosEstudiante['rut'];

    $rut = $_POST['rut_estudiante'];
    $fecha = $_POST['fecha'];
    $anio = date('Y', strtotime($fecha));
    $mes_en = date('F', strtotime($fecha)); // Ej: "July"
    $mes_es = $meses[$mes_en];
    $practica = $_POST['practica'];
    $hito = $_POST['hito'];
    
    $archivo = $_FILES['archivo'];
    $dir_base = __DIR__ . "/../documentos/$anio/$mes_es/$practica/$hito";
    if (!file_exists($dir_base)) {
        mkdir($dir_base, 0777, true);
    }

    $nombre_archivo = strtoupper("{$practica}_{$hito}_{$fecha}_{$nombre}_{$rut_estudiante}.pdf");
    $nombre_archivo = str_replace(' ', '_', $nombre_archivo);
    $ruta_final = "$dir_base/$nombre_archivo";

    if (move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
        $mensaje = "Archivo subido correctamente: $nombre_archivo";
    } else {
        $mensaje = "Error al subir el archivo.";
    }
}
?>

<div class="container mt-4">
    <h2>Subir Informe con Nomenclatura</h2>
    <?php if ($mensaje): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($mensaje) ?> </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="estudiante_id" class="form-label">Estudiante</label>
            <select class="form-select" name="estudiante_id" id="estudiante_id" required onchange="mostrarRut()">
                <option value="">Seleccione un estudiante</option>
                <?php foreach ($estudiantes as $est): ?>
                <option value="<?= $est['id'] ?>" data-rut="<?= $est['rut'] ?>">
                <?= htmlspecialchars($est['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="rut_estudiante" class="form-label">RUT del Estudiante</label>
            <input type="text" class="form-control" name="rut_estudiante" id="rut_estudiante" readonly>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha (AAAA-MM-DD)</label>
            <input type="date" class="form-control" name="fecha" required>
        </div>
        <div class="mb-3">
            <label for="practica" class="form-label">Práctica</label>
            <select name="practica" class="form-select">
                <option value="Practica_I">Práctica I</option>
                <option value="Practica_II">Práctica II</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="hito" class="form-label">Hito</label>
            <select name="hito" class="form-select">
                <option value="Hito_1">Hito 1</option>
                <option value="Hito_2">Hito 2</option>
                <option value="Evaluacion_Final">Evaluación Final</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="archivo" class="form-label">Selecciona el informe PDF:</label>
            <input type="file" class="form-control" name="archivo" accept=".pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir</button>
    </form>
</div>
<script>
function mostrarRut() {
  const select = document.getElementById('estudiante_id');
  const rut = select.options[select.selectedIndex].getAttribute('data-rut');
  document.getElementById('rut_estudiante').value = rut || '';
}
</script>
<?php require_once '../includes/footer.php'; ?>
