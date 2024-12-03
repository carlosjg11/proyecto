<?php
// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('conexionn.php'); // Archivo de conexión a la base de datos

// Obtener los ejercicios asignados a la rutina
$rutinaNombre = $_GET['rutina'] ?? null;
if (!$rutinaNombre) {
    die("Rutina no proporcionada.");
}

// Obtener los ejercicios asignados a la rutina
$stmt = $conn->prepare("
    SELECT re.ejercicio_nombre AS ejercicio, e.descripcion, e.categoria, re.completado
    FROM rutina_ejercicios re
    JOIN ejercicios e ON re.ejercicio_nombre = e.nombre
    WHERE re.rutina_nombre = ?
");
$stmt->bind_param("s", $rutinaNombre);
$stmt->execute();
$ejercicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ejercicio = $_POST['ejercicio'];
    $completado = $_POST['completado'] ? 1 : 0;

    // Actualizar estado de completado en la base de datos
    $stmt = $conn->prepare("UPDATE rutina_ejercicios SET completado = ? WHERE rutina_nombre = ? AND ejercicio_nombre = ?");
    $stmt->bind_param("iss", $completado, $rutinaNombre, $ejercicio);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas - Rutina</title>
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
        h1 {
            text-align: center;
            color: #6A0DAD;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 5px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        li.completed {
            text-decoration: line-through;
            color: gray;
            background-color: #e0e0e0;
        }
        .btn {
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #5A0CAB;
        }
    </style>
    <script>
        function toggleCompletion(ejercicio, checkbox) {
            const completado = checkbox.checked ? 1 : 0;

            fetch("listaTareas.php?rutina=<?php echo urlencode($rutinaNombre); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `ejercicio=${encodeURIComponent(ejercicio)}&completado=${completado}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const li = checkbox.closest("li");
                    li.classList.toggle("completed", completado === 1);
                } else {
                    alert("Error al actualizar el estado del ejercicio.");
                }
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Lista de Tareas: <?php echo htmlspecialchars($rutinaNombre); ?></h1>
        <ul>
            <?php if (empty($ejercicios)): ?>
                <li>No hay ejercicios asignados a esta rutina.</li>
            <?php else: ?>
                <?php foreach ($ejercicios as $ejercicio): ?>
                    <li class="<?php echo $ejercicio['completado'] ? 'completed' : ''; ?>">
                        <span>
                            <strong><?php echo htmlspecialchars($ejercicio['ejercicio']); ?></strong> - 
                            <?php echo htmlspecialchars($ejercicio['categoria']); ?>
                        </span>
                        <input type="checkbox" 
                               <?php echo $ejercicio['completado'] ? 'checked' : ''; ?> 
                               onchange="toggleCompletion('<?php echo htmlspecialchars($ejercicio['ejercicio']); ?>', this)">
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
