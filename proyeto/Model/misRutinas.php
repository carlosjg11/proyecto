<?php
// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../Controller/conexionn.php'); // Archivo de conexión a la base de datos

class ListaRutinas {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para obtener las rutinas del usuario logeado
    public function obtenerRutinasPorUsuario($matricula) {
        $sql = "
            SELECT r.nombre AS rutina, r.descripcion, r.fecha
            FROM rutinas r
            WHERE r.matricula = ?
            ORDER BY r.fecha DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Método para obtener los ejercicios asignados a una rutina
    public function obtenerEjerciciosPorRutina($rutinaNombre) {
        $sql = "
            SELECT e.nombre AS ejercicio, e.descripcion, e.categoria, re.completado
            FROM rutina_ejercicios re
            JOIN ejercicios e ON re.ejercicio_nombre = e.nombre
            WHERE re.rutina_nombre = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $rutinaNombre);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Método para marcar como completados solo los ejercicios seleccionados
    public function completarEjerciciosSeleccionados($rutinaNombre, $ejerciciosSeleccionados) {
        $sql = "UPDATE rutina_ejercicios SET completado = 1 WHERE rutina_nombre = ? AND ejercicio_nombre = ?";
        $stmt = $this->conn->prepare($sql);
        foreach ($ejerciciosSeleccionados as $ejercicio) {
            $stmt->bind_param("ss", $rutinaNombre, $ejercicio);
            $stmt->execute();
        }
        return true;
    }
}

// Crear instancia de la clase ListaRutinas
$listaRutinas = new ListaRutinas($conn);

// Verificar si el usuario está logeado
if (!isset($_SESSION['matricula'])) {
    die("Usuario no logeado.");
}

// Manejar la acción "Terminar Rutina"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminar_rutina'])) {
    $rutinaNombre = $_POST['rutina'];
    $ejerciciosSeleccionados = $_POST['ejercicios_seleccionados'] ?? []; // Ejercicios seleccionados

    if (!empty($ejerciciosSeleccionados)) {
        $listaRutinas->completarEjerciciosSeleccionados($rutinaNombre, $ejerciciosSeleccionados);
        echo "<script>alert('¡Ejercicios seleccionados completados!');</script>";
    } else {
        echo "<script>alert('No seleccionaste ejercicios para completar.');</script>";
    }

    header("Location: misRutinas.php");
    exit();
}

// Obtener las rutinas del usuario logeado
$matricula = $_SESSION['matricula'];
$rutinas = $listaRutinas->obtenerRutinasPorUsuario($matricula);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Rutinas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
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
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
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
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #5A0CAB;
        }

        /* Botón de toggle */
        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 15px;
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

        /* Contenido principal */
        .content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f4f4f4;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        .content.expanded {
            margin-left: 0;
            width: 100%;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        }

        .ejercicios ul {
            padding-left: 20px;
        }

        .ejercicios li {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }

        .btn-terminar {
            background-color: #28A745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-terminar:hover {
            background-color: #218838;
        }

        /* Estilo responsivo */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.hidden {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Botón de toggle -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <a href="../View/infoalumno.php">Ver mi información</a>
        <a href="verejer.php">Lista de Ejercicios</a>
        <a href="../View/verAnuncios.php">Ver Anuncios</a>
        <a href="../View/verrecomendacion.php">Ver Recomendaciones</a>
        <a href="../View/vermiprogreso.php">Ver mi progreso</a>
        <a href="../View/banco.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <div class="container">
            <h1>Mis Rutinas</h1>
            <?php if (!empty($rutinas)): ?>
                <?php foreach ($rutinas as $rutina): ?>
                    <div class="rutina">
                        <h3><?php echo htmlspecialchars($rutina['rutina']); ?></h3>
                        <p><?php echo htmlspecialchars($rutina['descripcion']); ?></p>
                        <p>Fecha: <?php echo htmlspecialchars($rutina['fecha']); ?></p>
                        <div class="ejercicios">
                            <h4>Ejercicios:</h4>
                            <form method="post">
                                <ul>
                                    <?php
                                    $ejercicios = $listaRutinas->obtenerEjerciciosPorRutina($rutina['rutina']);
                                    if (!empty($ejercicios)):
                                        foreach ($ejercicios as $ejercicio):
                                    ?>
                                            <li>
                                                <input type="checkbox" name="ejercicios_seleccionados[]" value="<?php echo htmlspecialchars($ejercicio['ejercicio']); ?>" <?php echo $ejercicio['completado'] ? 'checked disabled' : ''; ?>>
                                                <?php echo htmlspecialchars($ejercicio['ejercicio']); ?> - <?php echo htmlspecialchars($ejercicio['descripcion']); ?>
                                            </li>
                                    <?php
                                        endforeach;
                                    else:
                                    ?>
                                        <li>No hay ejercicios asignados.</li>
                                    <?php endif; ?>
                                </ul>
                                <input type="hidden" name="rutina" value="<?php echo htmlspecialchars($rutina['rutina']); ?>">
                                <button type="submit" name="terminar_rutina" class="btn-terminar">Completar seleccionados</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tienes rutinas asignadas actualmente.</p>
            <?php endif; ?>
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
