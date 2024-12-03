<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {
  
}

$nombre_usuario = $_SESSION['nombre_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Ejercicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #7B2CBF;
            color: white;
            overflow-x: hidden;
            padding-top: 20px;
            transition: all 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-250px);
        }

        .sidebar h2 {
            text-align: center;
            color: white;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
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
            z-index: 1000;
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
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
       
        <a href="pag_soloadmin.php">Agregar Usuarios</a>
        <a href="lista_ejercicios.php">Lista de Ejercicios</a>
        <a href="../Model/rutinas_entrenador.php">Agregar Rutina</a>
        <a href="recomendaciones.php">Recomendaciones</a>
        <a href="anuncios.php">Agregar Avisos</a>
        <a href="agelmo.php">Modificar/Eliminar Usuarios</a>
        <a href="../Controller/respaldo.php">Respaldo de base de datos</a>
        <a href="Reports/verReportes.php">Ver reportes</a>
       
        <a href="../Controller/logout.php" onclick="return confirm('¿Deseas cerrar sesión?')">Cerrar sesión</a>
    </div>

    <div class="content" id="content">
        <h1>Bienvenido <?php echo htmlspecialchars($nombre_usuario); ?></h1>

    <div class="container">
            <!--  <div class="section">
                <a href="pag_admin.php">
                    <img src="View/Pictures/usuario.png" alt="Ver usuarios">
                    <h2>Ver Usuarios</h2>
                </a>
            </div>  -->

            <div class="section">
                <a href="pag_soloadmin.php">
                    <img src="Pictures/agregar.png" alt="Agregar usuario">
                    <h2>Agregar Usuarios</h2>
                </a>
            </div>
            <div class="section">
                <a href="lista_ejercicios.php">
                    <img src="Pictures/eje.jpg" alt="Lista de ejercicios">
                    <h2>Lista de Ejercicios</h2>
                </a>
            </div>
            <div class="section">
                <a href="../Model/rutinas_entrenador.php">
                    <img src="Pictures/rutina.png" alt="Agregar rutina">
                    <h2>Agregar Rutina</h2>
                </a>
            </div>
            <div class="section">
                <a href="recomendaciones.php">
                    <img src="Pictures/comentarios.png" alt="Agregar recomendación">
                    <h2>Recomendaciones</h2>
                </a>
            </div>
            <div class="section">
                <a href="anuncios.php">
                    <img src="Pictures/avisos.png" alt="Agregar recomendación">
                    <h2>Agregar Avisos</h2>
                </a>
            </div>

            <div class="section">
                <a href="agelmo.php">
                    <img src="Pictures/eliminar.png" alt="Modificar y eliminar usuarios">
                    <h2>Modificar/Eliminar Usuarios</h2>
                </a>
            </div>

            <!--  <div class="section">
                <a href="rutinascompletadas.php">
                    <img src="View/Pictures/avisos.png" alt="Agregar recomendación">
                    <h2>Rutinas completadas </h2>
                </a>
            </div> -->


            <div class="section">
                <a href="../Controller/respaldo.php">
                    <img src="Pictures/respaldo.png" alt="Modificar y eliminar usuarios">
                    <h2>Respaldo de base de datos </h2>
                </a>
            </div>

            <div class="section">
                <a href="Reports/verReportes.php">
                    <img src="Pictures/verReporte.png" alt="Agregar recomendación">
                    <h2>Ver  Reportes </h2>
                </a>
            </div>
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
