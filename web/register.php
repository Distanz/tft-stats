<?php
session_start();
require_once 'includes/db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $mensaje = 'El nombre de usuario ya existe.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        $mensaje = 'Usuario registrado con éxito. Puedes iniciar sesión.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - TFT Stats</title>
  <!-- Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'components/navbar.php'; ?>

  <main>
    
    <div class="container my-5">
      <h2 class="text-center mb-4 text-light">Registrarse</h2>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <form action="<?= BASE_URL ?>/register.php" method="POST" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
              <label for="username" class="form-label">Nombre de usuario</label>
              <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
          </form>
        </div>
      </div>
    </div>

    <p><?= $mensaje ?></p>
  </main>

  <?php include 'components/footer.php'; ?>
  <!-- Bootstrap JS y Popper desde CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
