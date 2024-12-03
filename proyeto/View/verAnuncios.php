<?php
include('../Controller/conexionn.php');

// Recuperar todos los avisos para mostrarlos
$sql = "SELECT * FROM avisos ORDER BY fecha DESC";
$resultado = mysqli_query($conn, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Avisos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
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

        h1 {
            text-align: center;
            color: #6A0DAD;
            margin-top: 20px;
        }

        .avisos-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 10px;
        }

        .aviso {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .aviso h3 {
            color: #6A0DAD;
            margin: 0 0 10px;
        }

        .aviso p {
            margin: 0 0 10px;
        }

        .aviso small {
            color: #555;
        }
    </style>
</head>
<body>
    <!-- Botón para mostrar/ocultar la barra lateral -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <a href="infoalumno.php">Ver mi información</a>
        <a href="../Model/verejer.php">Lista de Ejercicios</a>
        <a href="verrecomendacion.php">Ver Recomendaciones</a>
        <a href="vermiprogreso.php">Ver mi progreso</a>
        <a href="listarRutinas.php">Rutinas</a>
        <a href="banco.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <h1>Avisos</h1>

        <!-- Lista de avisos -->
        <div class="avisos-container">
            <?php while ($aviso = mysqli_fetch_assoc($resultado)): ?>
                <div class="aviso">
                    <h3><?php echo htmlspecialchars($aviso['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($aviso['contenido']); ?></p>
                    <small>Publicado por: <?php echo htmlspecialchars($aviso['autor']); ?> el <?php echo htmlspecialchars($aviso['fecha']); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Script para ocultar/mostrar la barra lateral -->
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
