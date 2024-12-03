<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../Controller/conexionn.php');

// Obtener el ID de la recomendación
$idRecomendacion = $_GET['id'] ?? null;

// Verificar si el ID de la recomendación está presente
if (!$idRecomendacion) {
    die("ID de recomendación no proporcionado.");
}

// Obtener la recomendación
$stmt = $conn->prepare("SELECT * FROM recomendaciones WHERE id = ?");
$stmt->bind_param("i", $idRecomendacion);
$stmt->execute();
$recomendacion = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si no existe la recomendación, mostrar error
if (!$recomendacion) {
    die("Recomendación no encontrada.");
}

// Manejar envío de observación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_observacion'])) {
    $autor = $_SESSION['nombre_usuario'] ?? 'Anónimo'; // Autor de la observación
    $comentario = trim($_POST['comentario']);

    if (!empty($comentario)) {
        $stmt = $conn->prepare("INSERT INTO observaciones (id_recomendacion, autor, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $idRecomendacion, $autor, $comentario);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Observación añadida correctamente.";
        } else {
            $_SESSION['error'] = "Error al añadir la observación.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "El comentario no puede estar vacío.";
    }
    header("Location: chat_recomendaciones.php?id=$idRecomendacion");
    exit();
}

// Obtener observaciones relacionadas
$stmt = $conn->prepare("SELECT * FROM observaciones WHERE id_recomendacion = ? ORDER BY fecha ASC");
$stmt->bind_param("i", $idRecomendacion);
$stmt->execute();
$observaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Recomendaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
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
        .recomendacion h3, .observacion h4 {
            color: #6A0DAD;
            margin: 0 0 10px;
            font-weight: 600;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Poppins', Arial, sans-serif;
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
    <div class="container">
        <div class="recomendacion">
            <h3>Recomendación</h3>
            <p><strong>Para:</strong> <?php echo htmlspecialchars($recomendacion['alumno']); ?></p>
            <p><?php echo htmlspecialchars($recomendacion['comentario']); ?></p>
            <p class="meta-info">Por: <?php echo htmlspecialchars($recomendacion['autor']); ?> | Fecha: <?php echo htmlspecialchars($recomendacion['fecha']); ?></p>
        </div>

        <div class="container">
            <h3>Observaciones</h3>
            <?php if (empty($observaciones)): ?>
                <p>No hay observaciones aún.</p>
            <?php else: ?>
                <?php foreach ($observaciones as $observacion): ?>
                    <div class="observacion">
                        <h4><?php echo htmlspecialchars($observacion['autor']); ?></h4>
                        <p><?php echo htmlspecialchars($observacion['comentario']); ?></p>
                        <p class="meta-info">Fecha: <?php echo htmlspecialchars($observacion['fecha']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="POST" action="">
            <h3>Agregar Observación</h3>
            <textarea name="comentario" placeholder="Escribe tu comentario aquí..." required></textarea>
            <button type="submit" name="agregar_observacion" class="btn">Enviar</button>
        </form>
    </div>
</body>
</html>
