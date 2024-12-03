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

    // Método para obtener las rutinas del usuario logueado
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

    // Método para marcar solo los ejercicios seleccionados como bloqueados
    public function terminarRutina($rutinaNombre) {
        $sql = "UPDATE rutina_ejercicios SET completado = 1 WHERE rutina_nombre = ? AND completado = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $rutinaNombre);
        return $stmt->execute();
    }
}

// Crear instancia de la clase ListaRutinas
$listaRutinas = new ListaRutinas($conn);

// Verificar si el usuario está logeado
if (!isset($_SESSION['matricula'])) {
    die("Usuario no logeado.");
}

// Obtener las rutinas del usuario logeado
$matricula = $_SESSION['matricula'];
$rutinas = $listaRutinas->obtenerRutinasPorUsuario($matricula);

// Manejo del botón "Terminar Rutina"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminar_rutina'])) {
    $rutinaNombre = $_POST['rutina'];
    if ($listaRutinas->terminarRutina($rutinaNombre)) {
        echo "<script>alert('¡Rutina terminada exitosamente! Los ejercicios seleccionados han sido bloqueados.');</script>";
    } else {
        echo "<script>alert('Error al terminar la rutina.');</script>";
    }
    header("Location: ../Model/misRutinas.php");
    exit();
}
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
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
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
            margin: 0 0 10px;
            font-weight: 600;
        }
        .meta-info {
            color: #6A0DAD;
            font-size: 14px;
        }
        .ejercicios ul {
            padding-left: 20px;
        }
        .ejercicios li {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }
        .ejercicios li.completed {
            text-decoration: line-through;
            color: gray;
        }
        .ejercicios input[type="checkbox"] {
            margin-right: 10px;
        }
        .rutina-completada {
            color: green;
            font-weight: bold;
        }
        .btn-terminar {
            background-color: #28A745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-terminar:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mis Rutinas</h1>
        <?php if (empty($rutinas)): ?>
            <p>No tienes rutinas asignadas.</p>
        <?php else: ?>
            <?php foreach ($rutinas as $rutina): ?>
                <div class="rutina">
                    <h3><?php echo htmlspecialchars($rutina['rutina']); ?></h3>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($rutina['descripcion']); ?></p>
                    <p class="meta-info"><strong>Fecha:</strong> <?php echo htmlspecialchars($rutina['fecha']); ?></p>
                    <div class="ejercicios">
                        <h4>Ejercicios Asignados:</h4>
                        <ul>
                            <?php 
                            $ejercicios = $listaRutinas->obtenerEjerciciosPorRutina($rutina['rutina']);
                            if (empty($ejercicios)): ?>
                                <li>No hay ejercicios asignados.</li>
                            <?php else: ?>
                                <?php foreach ($ejercicios as $ejercicio): ?>
                                    <li class="<?php echo $ejercicio['completado'] ? 'completed' : ''; ?>">
                                        <input type="checkbox" 
                                               <?php echo $ejercicio['completado'] ? 'checked disabled' : ''; ?>
                                               onchange="actualizarEstado('<?php echo htmlspecialchars($rutina['rutina']); ?>', '<?php echo htmlspecialchars($ejercicio['ejercicio']); ?>', this)">
                                        <strong><?php echo htmlspecialchars($ejercicio['ejercicio']); ?></strong>
                                        (<?php echo htmlspecialchars($ejercicio['categoria']); ?>)
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="rutina" value="<?php echo htmlspecialchars($rutina['rutina']); ?>">
                        <button type="submit" name="terminar_rutina" class="btn-terminar">Terminar Rutina</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
