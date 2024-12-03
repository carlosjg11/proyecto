<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Entrenador</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos de la barra lateral */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
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
        <a href="pag_admin.php">Usuarios registrados</a>
        <a href="rutinascompletadas.php">Rutinas Completadas</a>
        <a href="ReporteEjeMas.php">Ejercicios más utilizados</a>
        <a href="ReporteAnunMas.php">Mayor actividad en anuncios</a>
        <a href="ReporteUsuDia.php">Usuarios registrados por día</a>
        <a href="ReporteEntreCom.php">Entrenador con más rutinas</a>
        <a href="ReporteCom.php">Rutinas hechas por entrenadores</a>
        <a href="View/../../admin.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <h1>Generar Reportes</h1>

        <div class="container">
            <!-- Usuarios registrados -->
            <div class="section">
                <a href="pag_admin.php">
                    <img src="../Pictures/usuario.png" alt="Ver usuarios">
                    <h2>Usuarios registrados</h2>
                </a>
            </div>

            <!-- Rutinas Completadas -->
            <div class="section">
                <a href="./rutinascompletadas.php">
                    <img src="../Pictures/rutina.png" alt="Rutinas completadas">
                    <h2>Rutinas Completadas por usuarios</h2>
                </a>
            </div>

            <!-- Ejercicios más utilizados -->
            <div class="section">
                <a href="./ReporteEjeMas.php">
                    <img src="../Pictures/eje.jpg" alt="Ejercicios más utilizados">
                    <h2>Ejercicios más utilizados</h2>
                </a>
            </div>

            <!-- Mayor actividad en anuncios -->
            <div class="section">
                <a href="./ReporteAnunMas.php">
                    <img src="../Pictures/avisos.png" alt="Mayor actividad en anuncios">
                    <h2>Mayor actividad en anuncios</h2>
                </a>
            </div>

            <!-- Usuarios registrados por día -->
            <div class="section">
                <a href="./ReporteUsuDia.php">
                    <img src="../Pictures/usu.png" alt="Usuarios registrados por día">
                    <h2>Usuarios registrados por día</h2>
                </a>
            </div>

            <!-- Entrenador con más rutinas -->
            <div class="section">
                <a href="./ReporteEntreCom.php">
                    <img src="../Pictures/list.png" alt="Entrenador con más rutinas">
                    <h2>Entrenador con más rutinas</h2>
                </a>
            </div>

            <!-- Rutinas hechas por entrenadores -->
            <div class="section">
                <a href="./ReporteCom.php">
                    <img src="../Pictures/paoloma.png" alt="Rutinas completadas por entrenadores">
                    <h2>Ejercicios puestos completados</h2>
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
