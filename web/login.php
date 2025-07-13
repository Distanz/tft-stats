<?php
session_start();
require_once 'includes/db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password, rol FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $username;
        $_SESSION['rol'] = $usuario['rol'];
        header('Location: index.php');
        exit;
    } else {
        $mensaje = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión - TFT Stats</title>
  <!-- Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'components/navbar.php'; ?>

  <main class="container my-5">
    <div class="container mt-4">
      <h2 class="text-center mb-4 text-light">Iniciar Sesión</h2>
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5">
          <div class="text-center mb-4">
            <img src="assets/images/logo.png" alt="TFT Stats Logo" width="100">
          </div>
          <form action="<?= BASE_URL ?>/login.php" method="POST" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
              <label for="username" class="form-label">Nombre de usuario</label>
              <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Entrar</button>
          </form>

          <?php if ($mensaje): ?>
            <div class="alert alert-danger mt-3" role="alert">
              <?= htmlspecialchars($mensaje) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>



  <?php include 'components/footer.php'; ?>
  <!-- Bootstrap JS y Popper desde CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

