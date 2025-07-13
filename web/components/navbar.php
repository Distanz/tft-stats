<?php
if (!defined('BASE_URL')) {
  include_once __DIR__ . '/../includes/config.php';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
  <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
    <img src="<?= BASE_URL ?>../assets/images/logo.png" alt="TFT Stats Logo" width="30" height="30" class="me-2">
    <span class="fw-bold text-white">TFT Stats</span>
  </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/pages/composiciones.php">Composiciones</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/pages/aumentos.php">Aumentos</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/user/registrar_partida.php">Registrar Partida</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/composiciones_lista.php">Admin Panel</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/user/perfil.php">Mi Perfil</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/logout.php">Cerrar sesión</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">Registrar</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Iniciar sesión</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

