<?php
session_start(); // Inicia la sesión

// Verificar si el usuario está logueado
if (!isset($_SESSION['matricula'])) {
    header("Location: index.php"); // Redirige al inicio de sesión si no está logueado
    exit();
}

// Obtiene el nombre completo del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Entrenador</title>
    <link rel="stylesheet" href="View/Css/style.css">
    <style>
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
        }

        .section:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
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

        <a href="../Model/rutinas_entrenador2.php">Agregar Rutinas</a>
        <a href="lista_ejercicios2.php">Lista de Ejercicios</a>
        <a href="recomendacion2.php">Agregar Recomendación</a>
        <a href="anuncios2.php">Agregar Anuncios</a>

        <a href="../Controller/logout.php" onclick="return confirm('¿Deseas cerrar sesión?')">Cerrar sesión</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <h1>Bienvenido <?php echo htmlspecialchars($nombre_usuario); ?></h1>

        <div class="container">
            <!-- Ver Usuarios Section 
            <div class="section">
                <a href="pag_admin.php">
                    <img src="View/Pictures/usuario.png" alt="Ver usuarios">
                    <h2>Ver Usuarios</h2>
                </a>
            </div>
-->
            <!-- Agregar Rutinas -->
            <div class="section">
                <a href="../Model/rutinas_entrenador2.php">
                    <img src="Pictures/rutina.png" alt="Agregar rutina">
                    <h2>Agregar Rutina</h2>
                </a>
            </div>

            <!-- Lista de Ejercicios Section -->
            <div class="section">
                <a href="lista_ejercicios2.php">
                    <img src="Pictures/eje.jpg" alt="Lista de ejercicios">
                    <h2>Lista de Ejercicios</h2>
                </a>
            </div>

            <!-- Recomendaciones -->
            <div class="section">
                <a href="recomendaciones2.php">
                    <img src="Pictures/comentarios.png" alt="Agregar recomendación">
                    <h2>Agregar Recomendación</h2>
                </a>
            </div>

            <!-- Agregar Anuncios -->
            <div class="section">
                <a href="anuncios2.php">
                    <img src="Pictures/avisos.png" alt="Agregar anuncio">
                    <h2>Agregar Anuncios</h2>
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
