<?php
// Habilita la visualización de errores para la depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Inicia la sesión para acceder a $_SESSION

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyecto"; // Cambia al nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica si la conexión a la base de datos fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para obtener la información del usuario logueado
function getUsuarioLogueado($conn) {
    if (!isset($_SESSION['matricula'])) {
        return null; // No hay usuario logueado
    }

    $matricula = $_SESSION['matricula'];
    $query = "SELECT matricula, pass, rol, sexo, fechanacimiento, apellidopaterno, apellidomaterno, nombres, carrera FROM login WHERE matricula = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Retorna la información del usuario logueado
    } else {
        return null; // No se encontró el usuario
    }
}

// Obtener la información del usuario logueado
$usuario = getUsuarioLogueado($conn);

// Cierra la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi información</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #4a148c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        table thead tr {
            background-color: #4a148c;
            color: #ffffff;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table tbody tr {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #e1bee7;
        }

        p {
            text-align: center;
            color: #333;
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
    </style>
</head>
<body>
    <!-- Botón para mostrar/ocultar la barra lateral -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
        
        <a href="../Model/verejer.php">Lista de Ejercicios</a>
        <a href="verAnuncios.php">Ver Anuncios</a>
        <a href="verrecomendacion.php">Ver Recomendaciones</a>
        <a href="vermiprogreso.php">Ver mi progreso</a>
        <a href="listarRutinas.php">Rutinas</a>
        <a href="banco.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <h1>Mi información</h1>

        <?php if ($usuario): ?>
            <table>
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Contraseña</th>
                        <th>Rol</th>
                        <th>Sexo</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Nombres</th>
                        <th>Carrera</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($usuario['matricula']) ?></td>
                        <td><?= htmlspecialchars($usuario['pass']) ?></td>
                        <td><?= htmlspecialchars($usuario['rol']) ?></td>
                        <td><?= htmlspecialchars($usuario['sexo']) ?></td>
                        <td><?= htmlspecialchars($usuario['fechanacimiento']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellidopaterno']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellidomaterno']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombres']) ?></td>
                        <td><?= htmlspecialchars($usuario['carrera']) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay usuario logueado o no se encontró información.</p>
        <?php endif; ?>
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
