<?php
session_start();
require_once '../includes/db.php';

function obtenerParcheActual() {
    $fecha_actual = new DateTime('now', new DateTimeZone('America/Santiago'));

    $parches = [
        '15.1' => ['2025-07-30 04:00:00', '2025-08-13 03:59:59'],
        '15.2' => ['2025-08-13 04:00:00', '2025-08-27 03:59:59'],
        '15.3' => ['2025-08-27 04:00:00', '2025-09-10 03:59:59'],
        '15.4' => ['2025-09-10 04:00:00', '2025-09-24 03:59:59'],
        '15.5' => ['2025-09-24 04:00:00', '2025-10-08 03:59:59'],
        '15.6' => ['2025-10-08 04:00:00', '2025-10-22 03:59:59'],
        '15.7' => ['2025-10-22 04:00:00', '2025-11-05 03:59:59'],
        '15.8' => ['2025-11-05 04:00:00', '2025-11-19 03:59:59'],
    ];

    foreach ($parches as $nombre => [$inicio, $fin]) {
        $inicio_dt = new DateTime($inicio, new DateTimeZone('America/Santiago'));
        $fin_dt = new DateTime($fin, new DateTimeZone('America/Santiago'));
        if ($fecha_actual >= $inicio_dt && $fecha_actual <= $fin_dt) {
            return $nombre;
        }
    }

    return 'desconocido';
}

// Ordenamiento
$columna_valida = ['nombre', 'frecuencia', 'promedio_total', 'promedio_2_1', 'promedio_3_2', 'promedio_4_2'];
$sort = in_array($_GET['sort'] ?? '', $columna_valida) ? $_GET['sort'] : 'frecuencia';
$dir = ($_GET['dir'] ?? '') === 'asc' ? 'ASC' : 'DESC';

// Parche seleccionado (por GET), o el actual por defecto
$parche = $_GET['parche'] ?? obtenerParcheActual();

// Lista de todos los parches válidos
$parches_disponibles = ['15.1', '15.2', '15.3', '15.4', '15.5', '15.6', '15.7', '15.8'];

// Query aumentos con filtro por parche
$stmt_aumentos = $pdo->prepare("
    SELECT aumento AS nombre,
        COUNT(*) AS frecuencia,
        ROUND(AVG(posicion), 2) AS promedio_total,
        ROUND(AVG(CASE WHEN stage = '2-1' THEN posicion END), 2) AS promedio_2_1,
        ROUND(AVG(CASE WHEN stage = '3-2' THEN posicion END), 2) AS promedio_3_2,
        ROUND(AVG(CASE WHEN stage = '4-2' THEN posicion END), 2) AS promedio_4_2
    FROM (
        SELECT aumento_2_1 AS aumento, '2-1' AS stage, posicion FROM partidas WHERE parche = ?
        UNION ALL
        SELECT aumento_3_2 AS aumento, '3-2' AS stage, posicion FROM partidas WHERE parche = ?
        UNION ALL
        SELECT aumento_4_2 AS aumento, '4-2' AS stage, posicion FROM partidas WHERE parche = ?
    ) AS todos_aumentos
    GROUP BY aumento
    ORDER BY $sort $dir
");
$stmt_aumentos->execute([$parche, $parche, $parche]);
$aumentos = $stmt_aumentos->fetchAll();

// Composiciones más populares (sin filtro por parche)
$stmt_composiciones = $pdo->query("
    SELECT composicion, COUNT(*) AS cantidad_veces_jugada
    FROM partidas
    GROUP BY composicion
    ORDER BY cantidad_veces_jugada DESC
    LIMIT 10
");
$composiciones_populares = $stmt_composiciones->fetchAll();

function link_orden($label, $columna, $actual, $dir_actual, $parche) {
    $dir = ($actual === $columna && $dir_actual === 'ASC') ? 'desc' : 'asc';
    $icono = '';

    if ($actual === $columna) {
        $icono = $dir_actual === 'ASC' ? '<i class="bi bi-caret-up-fill ms-1"></i>' : '<i class="bi bi-caret-down-fill ms-1"></i>';
    }

    return "<a href='?sort=$columna&dir=$dir&parche=$parche' class='text-decoration-none text-dark'>$label $icono</a>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estadísticas Generales - TFT Stats</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../components/navbar.php'; ?>

<div class="container mt-5">
  <h2 class="text-center mb-4 text-light">Estadísticas Generales</h2>

  <!-- Selector de parche -->
  <form method="get" class="mb-4 text-center">
    <label for="parche" class="text-light me-2">Selecciona un parche:</label>
    <select name="parche" id="parche" onchange="this.form.submit()" class="form-select d-inline-block w-auto">
      <?php foreach ($parches_disponibles as $p): ?>
        <option value="<?= $p ?>" <?= $p === $parche ? 'selected' : '' ?>><?= $p ?></option>
      <?php endforeach; ?>
    </select>
  </form>

  <div class="row my-5">
    <!-- Estadísticas de aumentos -->
    <div class="col-lg-8 mb-4">
      <div class="card shadow h-100">
        <div class="card-header bg-primary text-white">
          Promedio de Posición por Aumento (Parche <?= $parche ?>)
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead class="table-light">
              <tr>
                <th><?= link_orden('Aumento', 'nombre', $sort, $dir, $parche) ?></th>
                <th><?= link_orden('Frecuencia', 'frecuencia', $sort, $dir, $parche) ?></th>
                <th><?= link_orden('Prom. Total', 'promedio_total', $sort, $dir, $parche) ?></th>
                <th><?= link_orden('Prom. en 2-1', 'promedio_2_1', $sort, $dir, $parche) ?></th>
                <th><?= link_orden('Prom. en 3-2', 'promedio_3_2', $sort, $dir, $parche) ?></th>
                <th><?= link_orden('Prom. en 4-2', 'promedio_4_2', $sort, $dir, $parche) ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($aumentos as $aumento): ?>
                <tr>
                  <td><?= htmlspecialchars($aumento['nombre']) ?></td>
                  <td><?= $aumento['frecuencia'] ?></td>
                  <td><?= $aumento['promedio_total'] ?? '-' ?></td>
                  <td><?= $aumento['promedio_2_1'] ?? '-' ?></td>
                  <td><?= $aumento['promedio_3_2'] ?? '-' ?></td>
                  <td><?= $aumento['promedio_4_2'] ?? '-' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Compos populares -->
    <div class="col-lg-4">
      <div class="card shadow h-100">
        <div class="card-header bg-dark text-white">
          Composiciones Más Populares
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th>Composición</th>
                <th>Veces Jugada</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($composiciones_populares as $compo): ?>
                <tr>
                  <td><?= htmlspecialchars($compo['composicion']) ?></td>
                  <td><?= $compo['cantidad_veces_jugada'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
