<?php
session_start();
require_once '../includes/db.php';

// Consultar todas las composiciones populares
$stmt = $pdo->query("SELECT * FROM composiciones");
$composiciones = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Composiciones Populares - TFT Stats</title>
  <!-- Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include '../components/navbar.php'; ?>

  <main>
    <div class="container my-5">
      <h2 class="text-center mb-4 text-light">Composiciones Populares</h2>

      <!-- Composiciones Populares -->
      <div class="card shadow mb-5">
        <div class="card-header bg-warning text-dark">
          Composiciones Más Populares
        </div>
        <div class="card-body">
          <div class="row">
            <?php foreach ($composiciones as $row): ?>
              <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                  <img src="<?= htmlspecialchars($row['imagen']) ?>" class="card-img-top" alt="Imagen de <?= htmlspecialchars($row['nombre']) ?>">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
                    <p class="card-text"><strong>Tipo:</strong> <?= htmlspecialchars($row['tipo']) ?></p>
                    <p class="card-text"><strong>Estilo:</strong> <?= htmlspecialchars($row['estilo']) ?></p>
                    <p class="card-text"><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($row['descripcion'])) ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
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
