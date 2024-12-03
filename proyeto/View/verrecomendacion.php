<?php
// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../Controller/conexionn.php'); // Archivo para la conexión a la base de datos

class VerRecomendaciones {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para obtener recomendaciones asignadas al usuario
    public function obtenerRecomendacionesPorAlumno($alumno) {
        $stmt = $this->conn->prepare("SELECT * FROM recomendaciones WHERE alumno = ? ORDER BY fecha DESC");
        $stmt->bind_param("s", $alumno);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['nombre_usuario'])) {
    $_SESSION['error'] = "Debes iniciar sesión para ver tus recomendaciones.";
    header("Location: index.php");
    exit();
}

// Obtener el nombre del usuario logueado
$alumno = $_SESSION['nombre_usuario'];

// Crear instancia de la clase VerRecomendaciones
$verRecomendaciones = new VerRecomendaciones($conn);

// Obtener recomendaciones para el alumno logueado
$listaRecomendaciones = $verRecomendaciones->obtenerRecomendacionesPorAlumno($alumno);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Recomendaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
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
            color: #6A0DAD;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
        }

        .container {
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .recomendacion {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .recomendacion h3 {
            color: #6A0DAD;
            margin: 0 0 10px;
            font-weight: 600;
        }

        .meta-info {
            color: #6A0DAD;
            font-size: 14px;
        }

        .no-recomendaciones {
            text-align: center;
            color: #6A0DAD;
            font-size: 16px;
            font-weight: 600;
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
        <a href="vermiprogreso.php">Ver mi progreso</a>
        <a href="listarRutinas.php">Rutinas</a>
        <a href="banco.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <h1>Mis Recomendaciones</h1>

        <div class="container">
            <?php if (empty($listaRecomendaciones)): ?>
                <p class="no-recomendaciones">No tienes recomendaciones asignadas.</p>
            <?php else: ?>
                <?php foreach ($listaRecomendaciones as $recomendacion): ?>
                    <div class="recomendacion">
                        <h3>Recomendación</h3>
                        <p><?php echo htmlspecialchars($recomendacion['comentario']); ?></p>
                        <p class="meta-info">Por: <?php echo htmlspecialchars($recomendacion['autor']); ?> | Fecha: <?php echo htmlspecialchars($recomendacion['fecha']); ?></p>
                        <a href="./chat_recomendaciones.php?id=<?php echo $recomendacion['id']; ?>">Abrir recomendaciones</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
