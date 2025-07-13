<!-- index.php -->
<?php
    session_start();
    include_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TFT Stats</title>
    <!-- Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'components/navbar.php'; ?>

<main class="container my-5">
  <!-- Hero principal -->
  <section class="text-center py-5 bg-light rounded shadow">
    <h1 class="display-4">Bienvenido a <strong>TFT Stats</strong></h1>
    <p class="lead mt-3">
      Registra tus partidas de Teamfight Tactics y analiza tus estadísticas personales según aumentos y composiciones.
    </p>
    <a href="<?= isset($_SESSION['user_id']) ? BASE_URL . '/user/registrar_partida.php' : BASE_URL . '/register.php' ?>" class="btn btn-primary btn-lg mt-3">Comienza ahora</a>
  </section>

  <!-- Visión del sitio -->
  <section class="mt-5">
    <h2 class="text-center mb-4 text-light">Nuestra visión</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card bg-dark text-light shadow">
          <div class="card-body">
            <p>
              En TFT Stats creemos que conocer tus estadísticas personales es clave para mejorar. Riot Games no muestra cómo impactan tus aumentos en tu rendimiento. 
              Aquí puedes registrar tus partidas, ver estadísticas reales y tomar decisiones basadas en datos.
            </p>
            <p>
              Además, podrás descubrir composiciones populares, aprender de ellas y ver qué funciona mejor según tu estilo de juego.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>


    <?php include 'components/footer.php'; ?>
    <!-- Bootstrap JS y Popper desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>