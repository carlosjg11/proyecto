<?php
session_start();
include '../Controller/conexionn.php'; // Conexión a la base de datos

// Agregar rutina
if (isset($_POST['agregar_rutina'])) {
    $nombre = trim($_POST['nombre_rutina']);
    $descripcion = trim($_POST['descripcion_rutina']);
    $matricula = trim($_POST['matricula']);
    $creador = $_SESSION['matricula']; // Matricula del creador desde la sesión

    if (!empty($nombre) && !empty($descripcion) && !empty($matricula) && !empty($creador)) {
        $stmt = $conn->prepare("INSERT INTO rutinas (nombre, descripcion, matricula, creador, fecha) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $nombre, $descripcion, $matricula, $creador);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Rutina agregada exitosamente.";
        } else {
            $_SESSION['error'] = "Error al agregar la rutina: " . $stmt->error;
        }
    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    }
    header("Location: rutinas_entrenador2.php");
    exit();
}

// Eliminar rutina
if (isset($_GET['eliminar_rutina'])) {
    $nombre = trim($_GET['eliminar_rutina']);
    $stmt = $conn->prepare("DELETE FROM rutinas WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Rutina eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la rutina: " . $stmt->error;
    }
    header("Location: rutinas_entrenador2.php");
    exit();
}

if (isset($_POST['guardar_cambios'])) {
    $nombreOriginal = trim($_POST['nombre_original']);
    $nombreNuevo = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $matricula = trim($_POST['matricula']);
    $ejercicio = isset($_POST['ejercicio_nombre']) ? trim($_POST['ejercicio_nombre']) : null;

    $stmt = $conn->prepare("UPDATE rutinas SET nombre = ?, descripcion = ?, matricula = ? WHERE nombre = ?");
    $stmt->bind_param("ssss", $nombreNuevo, $descripcion, $matricula, $nombreOriginal);

    if ($stmt->execute()) {
        // Si se seleccionó un ejercicio, asignarlo a la rutina
        if (!empty($ejercicio)) {
            $stmtEjercicio = $conn->prepare("INSERT INTO rutina_ejercicios (rutina_nombre, ejercicio_nombre) VALUES (?, ?)");
            $stmtEjercicio->bind_param("ss", $nombreNuevo, $ejercicio);
            $stmtEjercicio->execute();
        }
        $_SESSION['mensaje'] = "Rutina actualizada exitosamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar la rutina: " . $stmt->error;
    }
    header("Location: rutinas_entrenador2.php");
    exit();
}


// Asignar ejercicio a rutina (máximo 10 ejercicios)
if (isset($_POST['agregar_ejercicio'])) {
    $rutina = trim($_POST['rutina_nombre']);
    $ejercicio = trim($_POST['ejercicio_nombre']);

    if (!empty($rutina) && !empty($ejercicio)) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM rutina_ejercicios WHERE rutina_nombre = ?");
        $stmt->bind_param("s", $rutina);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['total'];

        if ($count >= 10) {
            $_SESSION['error'] = "No puedes asignar más de 10 ejercicios a esta rutina.";
        } else {
            $stmt = $conn->prepare("INSERT INTO rutina_ejercicios (rutina_nombre, ejercicio_nombre) VALUES (?, ?)");
            $stmt->bind_param("ss", $rutina, $ejercicio);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Ejercicio asignado a la rutina correctamente.";
            } else {
                $_SESSION['error'] = "Error al asignar ejercicio: " . $stmt->error;
            }
        }
    } else {
        $_SESSION['error'] = "Debe seleccionar una rutina y un ejercicio.";
    }
    header("Location: rutinas_entrenador2.php");
    exit();
}

// Eliminar ejercicio de rutina
if (isset($_GET['eliminar_ejercicio'])) {
    $rutina = trim($_GET['rutina']);
    $ejercicio = trim($_GET['ejercicio']);
    $stmt = $conn->prepare("DELETE FROM rutina_ejercicios WHERE rutina_nombre = ? AND ejercicio_nombre = ?");
    $stmt->bind_param("ss", $rutina, $ejercicio);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Ejercicio eliminado correctamente de la rutina.";
    } else {
        $_SESSION['error'] = "Error al eliminar el ejercicio: " . $stmt->error;
    }
    header("Location: rutinas_entrenador2.php");
    exit();
}
// Agregar ejercicio
if (isset($_POST['agregar_ejercicio_nuevo'])) {
    $nombre = trim($_POST['nombre_ejercicio']);
    $descripcion = trim($_POST['descripcion_ejercicio']);
    $categoria = trim($_POST['categoria_ejercicio']);

    if (!empty($nombre) && !empty($descripcion) && !empty($categoria)) {
        $stmt = $conn->prepare("INSERT INTO ejercicios (nombre, descripcion, categoria) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $descripcion, $categoria);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Ejercicio agregado exitosamente.";
        } else {
            $_SESSION['error'] = "Error al agregar el ejercicio: " . $stmt->error;
        }
    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios para agregar un ejercicio.";
    }
    header("Location: rutinas_entrenador2.php");
    exit();
}

// Obtener rutinas con información del creador
$query = "
    SELECT r.nombre, r.descripcion, r.fecha, r.creador, l.matricula,
           CONCAT(l.nombres, ' ', l.apellidopaterno, ' ', l.apellidomaterno) AS alumno,
           CONCAT(c.nombres, ' ', c.apellidopaterno, ' ', c.apellidomaterno) AS creador_nombre
    FROM rutinas r
    JOIN login l ON r.matricula = l.matricula
    JOIN login c ON r.creador = c.matricula
";
$result = $conn->query($query);
if (!$result) {
    die("Error al obtener las rutinas: " . $conn->error);
}
$rutinas = $result->fetch_all(MYSQLI_ASSOC);

// Obtener alumnos
$alumnosResult = $conn->query("
    SELECT matricula, CONCAT(nombres, ' ', apellidopaterno, ' ', apellidomaterno) AS nombre_completo
    FROM login
    WHERE rol = 'alumno'
");
if (!$alumnosResult) {
    die("Error al obtener los alumnos: " . $conn->error);
}
$alumnos = $alumnosResult->fetch_all(MYSQLI_ASSOC);

// Obtener ejercicios
$ejerciciosResult = $conn->query("SELECT nombre, descripcion, categoria FROM ejercicios");
if (!$ejerciciosResult) {
    die("Error al obtener los ejercicios: " . $conn->error);
}
$ejercicios = $ejerciciosResult->fetch_all(MYSQLI_ASSOC);

// Obtener ejercicios asignados a rutinas
$ejerciciosAsignados = $conn->query("
    SELECT re.rutina_nombre, e.nombre AS ejercicio, e.categoria
    FROM rutina_ejercicios re
    JOIN ejercicios e ON re.ejercicio_nombre = e.nombre
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Rutinas</title>
    <!-- Botón para mostrar/ocultar la barra lateral -->
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Barra lateral -->
<div class="sidebar" id="sidebar">
        <a href="lista_ejercicios2.php">Lista de Ejercicios</a>
        <a href="recomendacion2.php">Agregar Recomendación</a>
        <a href="anuncios2.php">Agregar Anuncios</a>
        <a href="../View/entrenador.php">Volver </a>

</div>

<!-- Estilos CSS para la barra lateral -->
<style>
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
</style>

<!-- Script JavaScript para ocultar/mostrar la barra lateral -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
    }
</script>

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
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
        }
        .btn-agregar {
            background-color: #6A0DAD;
        }
        .btn-guardar {
            background-color: #28A745;
        }
        .btn-eliminar {
            background-color: #DC3545;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .rutina {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div style="background-color: green; color: white; padding: 10px;"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="background-color: red; color: white; padding: 10px;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <h3>Gestión de Rutinas</h3>

    <!-- Formulario para agregar rutina -->
    <form action="rutinas_entrenador2.php" method="POST">
        <input type="text" name="nombre_rutina" class="form-control" placeholder="Nombre de la rutina" required>
        <textarea name="descripcion_rutina" class="form-control" placeholder="Descripción" required></textarea>
        <select name="matricula" class="form-control" required>
            <option value="" disabled selected>Seleccionar alumno</option>
            <?php foreach ($alumnos as $alumno): ?>
                <option value="<?php echo $alumno['matricula']; ?>"><?php echo $alumno['nombre_completo']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="agregar_rutina" class="btn btn-agregar">Agregar Rutina</button>
    </form>

   <!-- Mostrar rutinas -->
<?php foreach ($rutinas as $rutina): ?>
    <div class="rutina">
        <h4><?php echo htmlspecialchars($rutina['nombre']); ?></h4>
        <p><strong>Alumno:</strong> <?php echo htmlspecialchars($rutina['alumno']); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($rutina['descripcion']); ?></p>
            <p><strong>Creador:</strong> <?php echo htmlspecialchars($rutina['creador_nombre']); ?></p>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($rutina['fecha']); ?></p>
        <ul>
            <?php foreach ($ejerciciosAsignados as $ej): ?>
                <?php if ($ej['rutina_nombre'] === $rutina['nombre']): ?>
                    <li>
                        <?php echo htmlspecialchars($ej['ejercicio']); ?> 
                        (<?php echo htmlspecialchars($ej['categoria']); ?>)
                        <a href="?eliminar_ejercicio&rutina=<?php echo urlencode($rutina['nombre']); ?>&ejercicio=<?php echo urlencode($ej['ejercicio']); ?>">Eliminar</a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <!-- Formulario para editar la rutina -->
        <form action="rutinas_entrenador2.php" method="POST">
            <input type="hidden" name="nombre_original" value="<?php echo htmlspecialchars($rutina['nombre']); ?>">
            
            <!-- Editar nombre -->
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($rutina['nombre']); ?>" class="form-control" required>

            <!-- Editar descripción -->
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" required><?php echo htmlspecialchars($rutina['descripcion']); ?></textarea>

            <!-- Seleccionar alumno -->
            <label for="matricula">Alumno:</label>
            <select id="matricula" name="matricula" class="form-control" required>
                <?php foreach ($alumnos as $alumno): ?>
                    <option value="<?php echo htmlspecialchars($alumno['matricula']); ?>" <?php echo $alumno['matricula'] == $rutina['matricula'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($alumno['nombre_completo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Seleccionar ejercicio -->
            <label for="ejercicio_nombre">Agregar Ejercicio:</label>
            <select id="ejercicio_nombre" name="ejercicio_nombre" class="form-control">
                <option value="" disabled selected>Seleccionar ejercicio</option>
                <?php foreach ($ejercicios as $ejercicio): ?>
                    <option value="<?php echo htmlspecialchars($ejercicio['nombre']); ?>">
                        <?php echo htmlspecialchars($ejercicio['nombre']); ?> - <?php echo htmlspecialchars($ejercicio['categoria']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Botones de acción -->
            <button type="submit" name="guardar_cambios" class="btn btn-guardar">Guardar</button>
        </form>

        <!-- Botón para eliminar rutina -->
        <a href="?eliminar_rutina=<?php echo urlencode($rutina['nombre']); ?>" class="btn btn-eliminar">Eliminar Rutina</a>
    </div>
<?php endforeach; ?>

</div>
</body>
</html>