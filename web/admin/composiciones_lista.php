<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM composiciones");
$composiciones = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Composiciones - TFT Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../components/navbar.php'; ?>

<div class="container my-5">
    <h2 class="mb-4 text-light">Panel de Administración - Composiciones</h2>
    <a href="composiciones_nuevas.php" class="btn btn-success mb-3">Agregar Nueva Composición</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Estilo</th>
            <th>Descripción</th>
            <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($composiciones as $compo): ?>
            <tr>
                <td>
                <?php if ($compo['imagen']): ?>
                    <img src="<?= htmlspecialchars($compo['imagen']) ?>" alt="Imagen" style="width: 60px; height: auto;">
                <?php else: ?>
                    Sin imagen
                <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($compo['nombre']) ?></td>
                <td><?= htmlspecialchars($compo['tipo']) ?></td>
                <td><?= htmlspecialchars($compo['estilo']) ?></td>
                <td><?= htmlspecialchars($compo['descripcion']) ?></td>
                <td>
                <a href="composiciones_editar.php?id=<?= $compo['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                <a href="composiciones_eliminar.php?id=<?= $compo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta composición?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
