<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ordenamiento por columnas
$columna_valida = ['nombre', 'frecuencia', 'promedio_total', 'promedio_2_1', 'promedio_3_2', 'promedio_4_2'];
$sort = in_array($_GET['sort'] ?? '', $columna_valida) ? $_GET['sort'] : 'frecuencia';
$dir = ($_GET['dir'] ?? '') === 'asc' ? 'ASC' : 'DESC';

// Estadísticas de aumentos por usuario, agrupadas por nombre y etapa
$stmt = $pdo->prepare("
    SELECT aumento AS nombre,
          COUNT(*) AS frecuencia,
          ROUND(AVG(posicion), 2) AS promedio_total,
          ROUND(AVG(CASE WHEN stage = '2-1' THEN posicion END), 2) AS promedio_2_1,
          ROUND(AVG(CASE WHEN stage = '3-2' THEN posicion END), 2) AS promedio_3_2,
          ROUND(AVG(CASE WHEN stage = '4-2' THEN posicion END), 2) AS promedio_4_2
    FROM (
        SELECT aumento_2_1 AS aumento, '2-1' AS stage, posicion FROM partidas WHERE user_id = ?
        UNION ALL
        SELECT aumento_3_2 AS aumento, '3-2' AS stage, posicion FROM partidas WHERE user_id = ?
        UNION ALL
        SELECT aumento_4_2 AS aumento, '4-2' AS stage, posicion FROM partidas WHERE user_id = ?
    ) AS aumentos_usuario
    GROUP BY aumento
    ORDER BY $sort $dir
");
$stmt->execute([$user_id, $user_id, $user_id]);
$estadisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);




$stmt_composiciones = $pdo->prepare("SELECT composicion, AVG(posicion) AS promedio_posicion_compo FROM partidas WHERE user_id = ? GROUP BY composicion");
$stmt_composiciones->execute([$user_id]);
$composiciones = $stmt_composiciones->fetchAll();

function link_orden($label, $columna, $actual, $dir_actual) {
    $dir = ($actual === $columna && $dir_actual === 'ASC') ? 'desc' : 'asc';
    $icono = '';

    if ($actual === $columna) {
        $icono = $dir_actual === 'ASC' ? '<i class="bi bi-caret-up-fill ms-1"></i>' : '<i class="bi bi-caret-down-fill ms-1"></i>';
    }

    return "<a href='?sort=$columna&dir=$dir' class='text-decoration-none text-dark'>$label $icono</a>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil - TFT Stats</title>
  <!-- Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include '../components/navbar.php'; ?>

  <main>

    <div class="container my-5">
      <h2 class="text-center mb-4 text-light">Mi Perfil</h2>

    <!-- Estadísticas por Aumento (Agrupadas) -->
      <div class="card shadow mb-5">
        <div class="card-header bg-primary text-white">
          Promedio de Posiciones por Aumento (por etapa)
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-light">
              <tr>
                <th><?= link_orden('Aumento', 'nombre', $sort, $dir) ?></th>
                <th><?= link_orden('Frecuencia', 'frecuencia', $sort, $dir) ?></th>
                <th><?= link_orden('Prom. Total', 'promedio_total', $sort, $dir) ?></th>
                <th><?= link_orden('Prom. en 2-1', 'promedio_2_1', $sort, $dir) ?></th>
                <th><?= link_orden('Prom. en 3-2', 'promedio_3_2', $sort, $dir) ?></th>
                <th><?= link_orden('Prom. en 4-2', 'promedio_4_2', $sort, $dir) ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($estadisticas as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['nombre']) ?></td>
                  <td><?= $row['frecuencia'] ?></td>
                  <td><?= $row['promedio_total'] ?? '-' ?></td>
                  <td><?= $row['promedio_2_1'] ?? '-' ?></td>
                  <td><?= $row['promedio_3_2'] ?? '-' ?></td>
                  <td><?= $row['promedio_4_2'] ?? '-' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>

            </table>
          </div>
        </div>
      </div>


        <!-- Estadísticas por Composición -->
        <div class="card shadow">
          <div class="card-header bg-success text-white">
            Posición Promedio por Composición
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Composición</th>
                    <th>Posición Promedio</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($composiciones as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['composicion']) ?></td>
                      <td><?= number_format($row['promedio_posicion_compo'], 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>


  </main>

  <?php include '../components/footer.php'; ?>
  <!-- Bootstrap JS y Popper desde CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
