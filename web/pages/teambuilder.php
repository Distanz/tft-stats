<?php
session_start();
require_once '../includes/db.php';

// Obtener todas las fichas desde la base de datos
$stmt = $pdo->query("SELECT id, nombre, coste, imagen FROM fichas ORDER BY coste, nombre");
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por coste
$fichas_por_coste = [];
foreach ($fichas as $ficha) {
    $coste = $ficha['coste'];
    if (!isset($fichas_por_coste[$coste])) {
        $fichas_por_coste[$coste] = [];
    }
    $fichas_por_coste[$coste][] = $ficha;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Team Builder - TFT Stats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>

<main>
    <section class="mt-5">
    <h2 class="text-center mb-4 text-light">Team Builder</h2>
    <div class="row">
        <!-- Tablero -->
        <div class="col-lg-8 mb-4">
        <div class="card shadow bg-dark">
            <div class="card-header bg-primary text-white">Tablero</div>
            <div class="card-body p-3">
            <div id="tablero" class="tablero-grid">
                <!-- Hexágonos generados con JS -->
            </div>
            </div>
        </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-4">
        <!-- Sinergias activas -->
        <div class="card mb-3 shadow">
            <div class="card-header bg-success text-white">Sinergias activas</div>
            <ul id="sinergias-lista" class="list-group list-group-flush">
            </ul>
        </div>

        <!-- Fichas por coste -->
        <div class="card mb-3 shadow">
            <div class="card-header bg-info text-white">Fichas por coste</div>
            <div class="card-body p-2">
                <?php foreach ($fichas_por_coste as $coste => $grupo): ?>
                <div class="mb-2">
                    <h6 class="text-light px-2 py-1 rounded coste-color-<?= $coste ?>">Coste <?= $coste ?></h6>
                    <div class="d-flex flex-wrap gap-2" id="fichas-coste-<?= $coste ?>">
                        <?php foreach ($grupo as $ficha): ?>
                        <img src="<?= htmlspecialchars($ficha['imagen']) ?>" 
                            alt="<?= htmlspecialchars($ficha['nombre']) ?>" 
                            class="ficha-miniatura"
                            title="<?= htmlspecialchars($ficha['nombre']) ?>"
                            draggable="true"
                            data-nombre="<?= htmlspecialchars($ficha['nombre']) ?>"
                            data-id="<?= $ficha['id'] ?>">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Ítems -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">Ítems</div>
            <div class="card-body p-2 d-flex flex-wrap gap-2" id="lista-items">
            <!-- Por implementar -->
            </div>
        </div>
        </div>
    </div>
    </section>
</main>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
const tablero = document.getElementById("tablero");

  // Crear el tablero
for (let fila = 0; fila < 4; fila++) {
    for (let columna = 0; columna < 7; columna++) {
        const celda = document.createElement("div");
        celda.classList.add("hex-celda");
        if (fila === 1 || fila === 3) {
        celda.classList.add("shifted");
        }
        celda.dataset.fila = fila;
        celda.dataset.columna = columna;

      // Permitir soltar
        celda.addEventListener("dragover", e => e.preventDefault());
        celda.addEventListener("drop", handleDrop);
        tablero.appendChild(celda);
    }
}

  // Permitir arrastrar desde el panel lateral
document.querySelectorAll(".ficha-miniatura").forEach(ficha => {
    ficha.addEventListener("dragstart", e => {
    e.dataTransfer.setData("text/plain", JSON.stringify({
        nombre: ficha.dataset.nombre,
        id: ficha.dataset.id,
        src: ficha.src,
        origen: "panel"
    }));
    });
});

  // Permitir mover fichas dentro del tablero
function prepararFichaParaMover(ficha) {
    ficha.draggable = true;
    ficha.addEventListener("dragstart", e => {
    e.dataTransfer.setData("text/plain", JSON.stringify({
        nombre: ficha.dataset.nombre,
        id: ficha.dataset.id,
        src: ficha.src,
        origen: "tablero"
    }));
      // Marcar el origen para eliminar luego si viene del tablero
        ficha.classList.add("moviendose");
    });

    // Eliminar ficha con clic derecho
    ficha.addEventListener("contextmenu", e => {
        e.preventDefault();
        ficha.remove();
    });
}

function handleDrop(e) {
    e.preventDefault();
    const data = JSON.parse(e.dataTransfer.getData("text/plain"));

    // Si ya hay una ficha en la celda, no permitir
    if (this.querySelector("img")) {
        return;
    }

    let ficha;

    // Si viene del panel lateral, clonamos
    if (data.origen === "panel") {
        ficha = document.createElement("img");
        ficha.src = data.src;
        ficha.alt = data.nombre;
        ficha.title = data.nombre;
        ficha.classList.add("ficha-miniatura");
        ficha.dataset.id = data.id;
        ficha.dataset.nombre = data.nombre;
        ficha.dataset.fila = this.dataset.fila;
        ficha.dataset.columna = this.dataset.columna;

        prepararFichaParaMover(ficha);
    }

    // Si viene del tablero, simplemente movemos el nodo
    if (data.origen === "tablero") {
        ficha = document.querySelector(".ficha-miniatura.moviendose");
        ficha.classList.remove("moviendose");
    }

    // Establecer nuevas coordenadas
    ficha.dataset.fila = this.dataset.fila;
    ficha.dataset.columna = this.dataset.columna;

    this.appendChild(ficha);
}
});
</script>


</body>
</html>
