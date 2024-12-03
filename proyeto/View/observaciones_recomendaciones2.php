<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../Controller/conexionn.php');

// Obtener todas las recomendaciones
$stmt = $conn->prepare("SELECT * FROM recomendaciones ORDER BY fecha DESC");
$stmt->execute();
$recomendaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Manejar envío de una observación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_observacion'])) {
    $idRecomendacion = $_POST['id_recomendacion'];
    $autor = $_SESSION['nombre_usuario'] ?? 'Usuario Anónimo';
    $comentario = $_POST['comentario'];

    if (!empty($idRecomendacion) && !empty($comentario)) {
        $stmt = $conn->prepare("INSERT INTO observaciones (id_recomendacion, autor, comentario, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $idRecomendacion, $autor, $comentario);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Observación añadida correctamente.";
        } else {
            $_SESSION['error'] = "Error al añadir la observación.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    }
    header("Location: observaciones_recomendaciones2.php");
    exit();
}

// Obtener observaciones relacionadas con las recomendaciones
function obtenerObservaciones($conn, $idRecomendacion) {
    $stmt = $conn->prepare("SELECT * FROM observaciones WHERE id_recomendacion = ? ORDER BY fecha ASC");
    $stmt->bind_param("i", $idRecomendacion);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recomendaciones y Observaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #7B2CBF;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-250px);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #5A0CAB;
        }

        .toggle-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1100;
        }

        .toggle-btn:hover {
            background-color: #5A0CAB;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f4f4f4;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .content.expanded {
            margin-left: 0;
            width: 100%;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .recomendacion, .observacion {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        textarea, select, input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #5A0CAB;
        }

        .meta-info {
            color: #6A0DAD;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Botón para mostrar/ocultar la barra lateral -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
    
        <a href="./recomendaciones2.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <div class="container">
            <h1>Recomendaciones y Observaciones</h1>
            <?php foreach ($recomendaciones as $recomendacion): ?>
                <div class="recomendacion">
                    <h3>Para: <?php echo htmlspecialchars($recomendacion['alumno']); ?></h3>
                    <p><?php echo htmlspecialchars($recomendacion['comentario']); ?></p>
                    <p class="meta-info">Por: <?php echo htmlspecialchars($recomendacion['autor']); ?> | Fecha: <?php echo htmlspecialchars($recomendacion['fecha']); ?></p>

                    <h4>Observaciones</h4>
                    <?php
                    $observaciones = obtenerObservaciones($conn, $recomendacion['id']);
                    if (empty($observaciones)): ?>
                        <p>No hay observaciones aún.</p>
                    <?php else: ?>
                        <?php foreach ($observaciones as $observacion): ?>
                            <div class="observacion">
                                <p><?php echo htmlspecialchars($observacion['comentario']); ?></p>
                                <p class="meta-info">Por: <?php echo htmlspecialchars($observacion['autor']); ?> | Fecha: <?php echo htmlspecialchars($observacion['fecha']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <textarea name="comentario" placeholder="Escribe una observación aquí..." required></textarea>
                        <input type="hidden" name="id_recomendacion" value="<?php echo $recomendacion['id']; ?>">
                        <button type="submit" name="nueva_observacion" class="btn">Agregar Observación</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('hidden');
            content.classList.toggle('expanded');
        }
    </script>
</body>
</html>
