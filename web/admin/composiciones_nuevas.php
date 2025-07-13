<?php
session_start();
require_once '../includes/db.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $tipo = $_POST['tipo'];
    $estilo = $_POST['estilo'];
    $descripcion = trim($_POST['descripcion']);
    
    // Subir la imagen del carry
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $target_dir = "../assets/images/composiciones/";
        $target_file = $target_dir . basename($_FILES['imagen']['name']);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $imagen = $target_file;
        } else {
            $mensaje = "Hubo un error al cargar la imagen.";
        }
    }

    // Insertar la composición en la base de datos
    $stmt = $pdo->prepare("INSERT INTO composiciones (nombre, tipo, estilo, descripcion, imagen) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $tipo, $estilo, $descripcion, $imagen]);

    $mensaje = 'Composición agregada con éxito.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Composición - TFT Stats</title>
  <!-- Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include '../components/navbar.php'; ?>

  <main>
    <div class="container my-5">
      <h2 class="text-center mb-4 text-light">Agregar nueva composición</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <form method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded shadow">
            
            <?php if ($mensaje): ?>
              <div class="alert alert-info"><?= $mensaje ?></div>
            <?php endif; ?>

            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre de la composición:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="tipo" class="form-label">Tipo de daño:</label>
              <select name="tipo" id="tipo" class="form-select" required>
                <option value="AD">Daño Físico (AD)</option>
                <option value="AP">Daño Mágico (AP)</option>
                <option value="Mixto">Mixto</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="estilo" class="form-label">Estilo de composición:</label>
              <select name="estilo" id="estilo" class="form-select" required>
                <option value="Reroll">Reroll</option>
                <option value="Fast 8">Fast 8</option>
                <option value="Fast 9">Fast 9</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción breve:</label>
              <textarea name="descripcion" id="descripcion" rows="4" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
              <label for="imagen" class="form-label">Imagen del carry principal:</label>
              <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary w-100">Agregar Composición</button>
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
