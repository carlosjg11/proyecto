<?php
session_start();

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyecto";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la lista de ejercicios
$result = $conn->query("SELECT nombre, descripcion, categoria FROM ejercicios");
$ejercicios = [];
while ($row = $result->fetch_assoc()) {
    $ejercicios[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        /* Estilos de la barra lateral */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #7B2CBF;
            color: white;
            padding-top: 20px;
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
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            transition: background-color 0.3s;
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
            transition: margin-left 0.3s ease;
        }

        .content.expanded {
            margin-left: 0;
        }

        .card {
            margin: 10px;
            padding: 10px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 200px;
            display: inline-block;
            vertical-align: top;
            background-color: #ffffff;
        }

        .card-title {
            font-size: 1rem;
            color: #6A0DAD;
            margin-bottom: 5px;
        }

        .card-text {
            font-size: 0.85rem;
            margin-bottom: 10px;
            color: #333;
        }

        .text-muted {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .container {
            margin-top: 30px;
            text-align: center;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
            color: #6A0DAD;
        }
    </style>
</head>
<body>
    <!-- Botón para mostrar/ocultar la barra lateral -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <a href="../View/infoalumno.php">Ver mi información</a>
        <a href="../View/verAnuncios.php">Ver Anuncios</a>
        <a href="../View/verrecomendacion.php">Ver Recomendaciones</a>
        <a href="../View/vermiprogreso.php">Ver mi progreso</a>
        <a href="../View/listarRutinas.php">Rutinas</a>
        <a href="../View/banco.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <div class="container">
            <h1 class="title">Ejercicios</h1>

            <?php if (!empty($ejercicios)): ?>
                <?php foreach ($ejercicios as $ejercicio): ?>
                    <div class="card">
                        <h5 class="card-title"><?php echo htmlspecialchars($ejercicio['nombre']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($ejercicio['descripcion']); ?></p>
                        <small class="text-muted">Categoría: <?php echo htmlspecialchars($ejercicio['categoria']); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No hay ejercicios disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
