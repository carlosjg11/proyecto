<?php
// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../Controller/conexionn.php'); // Conexión a la base de datos

class ProgresoRutinas {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para obtener el progreso de las rutinas del usuario
    public function obtenerProgresoPorUsuario($matricula) {
        $sql = "
            SELECT 
                r.nombre AS rutina,
                COUNT(re.ejercicio_nombre) AS total_ejercicios,
                SUM(re.completado) AS ejercicios_completados,
                ROUND((SUM(re.completado) / COUNT(re.ejercicio_nombre)) * 100, 2) AS porcentaje_completado
            FROM rutinas r
            LEFT JOIN rutina_ejercicios re ON r.nombre = re.rutina_nombre
            WHERE r.matricula = ?
            GROUP BY r.nombre
            ORDER BY porcentaje_completado DESC, r.fecha DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['matricula'])) {
    die("Usuario no logueado.");
}

// Obtener la matrícula del usuario logueado
$matricula = $_SESSION['matricula'];

// Crear instancia de la clase ProgresoRutinas
$progresoRutinas = new ProgresoRutinas($conn);

// Obtener el progreso de las rutinas del usuario logueado
$progresos = $progresoRutinas->obtenerProgresoPorUsuario($matricula);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Progreso</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Barra lateral */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #7B2CBF;
            color: white;
            padding-top: 20px;
            text-align: center;
        }
        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px 0;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #5A0CAB;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #6A0DAD;
            margin-bottom: 20px;
        }

        .rutina {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .rutina h3 {
            color: #6A0DAD;
            margin: 0 0 10px;
            font-weight: 600;
        }
        .meta-info {
            font-size: 14px;
            color: #333;
        }
        .progreso-bar {
            width: 100%;
            height: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progreso-bar-inner {
            height: 100%;
            background-color: #6A0DAD;
            width: 0;
            transition: width 0.5s ease-in-out;
        }
        .completado {
            font-weight: bold;
            color: green;
        }
        .pendiente {
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menú</h2>
        <a href="infoalumno.php">Ver mi información</a>
        <a href="../Model/verejer.php">Lista de Ejercicios</a>
        <a href="verAnuncios.php">Ver Anuncios</a>
        <a href="verrecomendacion.php">Ver Recomendaciones</a>
        <a href="../Model/misRutinas.php">Rutinas</a>
       
        <a href="banco.php">Volver</a>
    </div>

    <div class="content">
        <div class="container">
            <h1>Mi Progreso</h1>
            <?php if (empty($progresos)): ?>
                <p>No tienes rutinas registradas.</p>
            <?php else: ?>
                <?php foreach ($progresos as $progreso): ?>
                    <div class="rutina">
                        <h3><?php echo htmlspecialchars($progreso['rutina']); ?></h3>
                        <p class="meta-info">
                            Ejercicios Completados: <?php echo htmlspecialchars($progreso['ejercicios_completados']); ?> / <?php echo htmlspecialchars($progreso['total_ejercicios']); ?>
                        </p>
                        <p class="meta-info">
                            Progreso: <?php echo htmlspecialchars($progreso['porcentaje_completado']); ?>%
                        </p>
                        <div class="progreso-bar">
                            <div class="progreso-bar-inner" style="width: <?php echo htmlspecialchars($progreso['porcentaje_completado']); ?>%;"></div>
                        </div>
                        <p class="meta-info">
                            Estado: 
                            <?php if ($progreso['porcentaje_completado'] == 100): ?>
                                <span class="completado">Completado</span>
                            <?php else: ?>
                                <span class="pendiente">Pendiente</span>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
