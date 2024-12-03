<?php
session_start(); // Inicia la sesión

// Verifica si el alumno está logueado
if (!isset($_SESSION['nombre_usuario'])) {
    echo "<script>alert('Debes iniciar sesión primero.'); window.location.href='index.php';</script>";
    exit();
}

// Captura el nombre completo del alumno desde la sesión
$nombreAlumno = $_SESSION['nombre_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Alumno</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> <!-- Fuente Roboto -->
    <style>
        /* Aplicar fuente global */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
            font-weight: 700;
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
            background-color: #f4f4f4;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        .content.expanded {
            margin-left: 0;
            width: 100%;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .section img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .section h2 {
            font-size: 16px;
            margin-top: 10px;
            color: #333;
            font-weight: 700;
        }

        .section:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #6A0DAD;
            font-weight: 700;
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
        <a href="verAnuncios.php">Ver Anuncios</a>
        <a href="verrecomendacion.php">Ver Recomendaciones</a>
        <a href="vermiprogreso.php">Ver mi progreso</a>
        <a href="listarRutinas.php">Rutinas</a>
        <a href="../Controller/logout.php" onclick="return confirm('¿Deseas cerrar sesión?')">Cerrar sesión</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <h1>Bienvenido <?php echo htmlspecialchars($nombreAlumno); ?></h1>

        <div class="container">
            <!-- Ver mi información -->
            <div class="section">
                <a href="infoalumno.php">
                    <img src="Pictures/usuario.png" alt="Ver mi información">
                    <h2>Ver mi información</h2>
                </a>
            </div>

            <!-- Lista de Ejercicios -->
            <div class="section">
                <a href="../Model/verejer.php">
                    <img src="Pictures/eje.jpg" alt="Lista de ejercicios">
                    <h2>Lista de Ejercicios</h2>
                </a>
            </div>

            <!-- Ver Anuncios -->
            <div class="section">
                <a href="verAnuncios.php">
                    <img src="Pictures/avisos.png" alt="Ver anuncios">
                    <h2>Ver Anuncios</h2>
                </a>
            </div>

            <!-- Ver Recomendaciones -->
            <div class="section">
                <a href="verrecomendacion.php">
                    <img src="Pictures/comentarios.png" alt="Lista de ejercicios">
                    <h2>Ver Recomendaciones</h2>
                </a>
            </div>

            <!-- Ver mi progreso -->
            <div class="section">
                <a href="vermiprogreso.php">
                    <img src="Pictures/progreso.jpg" alt="Lista de ejercicios">
                    <h2>Ver mi progreso</h2>
                </a>
            </div>

            <!-- Rutinas -->
            <div class="section">
                <a href="../Model/misRutinas.php">
                    <img src="Pictures/rutina.png" alt="Lista de ejercicios">
                    <h2>Rutinas</h2>
                </a>
            </div>
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
