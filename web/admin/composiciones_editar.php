<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: composiciones_lista.php');
    exit;
}

$id = $_GET['id'];
$mensaje = '';

// Obtener los datos actuales
$stmt = $pdo->prepare("SELECT * FROM composiciones WHERE id = ?");
$stmt->execute([$id]);
$compo = $stmt->fetch();

if (!$compo) {
    header('Location: composiciones_lista.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $tipo = $_POST['tipo'];
    $estilo = $_POST['estilo'];
    $descripcion = trim($_POST['descripcion']);
    $imagen = $compo['imagen'];

    // Reemplazar imagen si se sube una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $target_dir = "../assets/images/composiciones/";
        $target_file = $target_dir . basename($_FILES['imagen']['name']);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $imagen = $target_file;
        } else {
            $mensaje = "Error al subir la imagen.";
        }
    }

    // Actualizar en base de datos
    $stmt = $pdo->prepare("UPDATE composiciones SET nombre = ?, tipo = ?, estilo = ?, descripcion = ?, imagen = ? WHERE id = ?");
    $stmt->execute([$nombre, $tipo, $estilo, $descripcion, $imagen, $id]);

    $mensaje = "Composición actualizada correctamente.";
    // Refrescar datos actualizados
    $compo = ['nombre' => $nombre, 'tipo' => $tipo, 'estilo' => $estilo, 'descripcion' => $descripcion, 'imagen' => $imagen];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Composición - TFT Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../components/navbar.php'; ?>

<div class="container my-5">
    <h2 class="mb-4 text-light">Editar Composición</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
        <label class="form-label">Nombre:</label>
        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($compo['nombre']) ?>">
        </div>

        <div class="mb-3">
        <label class="form-label">Tipo de Daño:</label>
        <select name="tipo" class="form-select" required>
            <option value="AD" <?= $compo['tipo'] === 'AD' ? 'selected' : '' ?>>AD</option>
            <option value="AP" <?= $compo['tipo'] === 'AP' ? 'selected' : '' ?>>AP</option>
            <option value="Mixto" <?= $compo['tipo'] === 'Mixto' ? 'selected' : '' ?>>Mixto</option>
        </select>
        </div>

        <div class="mb-3">
        <label class="form-label">Estilo:</label>
        <select name="estilo" class="form-select" required>
            <option value="Reroll" <?= $compo['estilo'] === 'Reroll' ? 'selected' : '' ?>>Reroll</option>
            <option value="Fast 8" <?= $compo['estilo'] === 'Fast 8' ? 'selected' : '' ?>>Fast 8</option>
            <option value="Fast 9" <?= $compo['estilo'] === 'Fast 9' ? 'selected' : '' ?>>Fast 9</option>
        </select>
        </div>

        <div class="mb-3">
        <label class="form-label">Descripción:</label>
        <textarea name="descripcion" class="form-control" rows="4" required><?= htmlspecialchars($compo['descripcion']) ?></textarea>
        </div>

        <div class="mb-3">
        <label class="form-label">Imagen actual:</label><br>
        <?php if ($compo['imagen']): ?>
            <img src="<?= htmlspecialchars($compo['imagen']) ?>" style="max-width: 150px;" alt="Imagen actual"><br>
        <?php else: ?>
            <p>Sin imagen cargada.</p>
        <?php endif; ?>
        </div>

        <div class="mb-3">
        <label class="form-label">Cambiar imagen:</label>
        <input type="file" name="imagen" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="composiciones_lista.php" class="btn btn-secondary">Volver</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
