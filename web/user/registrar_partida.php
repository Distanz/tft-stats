<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $posicion = $_POST['posicion'];
  $composicion = $_POST['composicion'];
  $aumento_2_1 = $_POST['aumento_2_1'];
  $aumento_3_2 = $_POST['aumento_3_2'];
  $aumento_4_2 = $_POST['aumento_4_2'];

  $stmt = $pdo->prepare("INSERT INTO partidas (user_id, posicion, composicion, aumento_2_1, aumento_3_2, aumento_4_2) 
                         VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$user_id, $posicion, $composicion, $aumento_2_1, $aumento_3_2, $aumento_4_2]);

  $mensaje = 'Partida registrada correctamente.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Partida - TFT Stats</title>
  <!-- Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include '../components/navbar.php'; ?>

  <main>
    <div class="container my-5">
      <h2 class="text-center mb-4 text-light">Registrar Partida</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <form action="registrar_partida.php" method="POST" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
              <label for="posicion" class="form-label">Posición Final</label>
              <input type="number" name="posicion" id="posicion" class="form-control" min="1" max="8" required>
            </div>

            <div class="mb-3">
              <label for="composicion" class="form-label">Composición Jugada</label>
              <input type="text" name="composicion" id="composicion" class="form-control" required>
            </div>

            <h5 class="mt-4">Aumentos</h5>
            <div class="mb-3">
              <label class="form-label">Aumento 2-1</label>
              <input type="text" name="aumento_2_1" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Aumento 3-2</label>
              <input type="text" name="aumento_3_2" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Aumento 4-2</label>
              <input type="text" name="aumento_4_2" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar Partida</button>
          </form>
        </div>
      </div>
    </div>


  </main>

  <?php include '../components/footer.php'; ?>
  <!-- Bootstrap JS y Popper desde CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
