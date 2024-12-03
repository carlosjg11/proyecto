<?php
session_start();
include 'conexionn.php'; // Conexión a la base de datos

// Agregar rutina
if (isset($_POST['agregar_rutina'])) {
    $nombre = trim($_POST['nombre_rutina']);
    $descripcion = trim($_POST['descripcion_rutina']);
    $matricula = trim($_POST['matricula']);

    if (!empty($nombre) && !empty($descripcion) && !empty($matricula)) {
        $stmt = $conn->prepare("INSERT INTO rutinas (nombre, descripcion, matricula, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $nombre, $descripcion, $matricula);
        $stmt->execute();
        $_SESSION['mensaje'] = "Rutina agregada exitosamente.";
    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    }
    header("Location: rutinas_entrenador.php");
    exit();
}

// Eliminar rutina
if (isset($_GET['eliminar'])) {
    $nombre = trim($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM rutinas WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $_SESSION['mensaje'] = "Rutina eliminada correctamente.";
    header("Location: rutinas_entrenador.php");
    exit();
}

// Actualizar rutina
if (isset($_POST['guardar_cambios'])) {
    $nombreOriginal = trim($_POST['nombre_original']);
    $nombreNuevo = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $matricula = trim($_POST['matricula']);

    $stmt = $conn->prepare("UPDATE rutinas SET nombre = ?, descripcion = ?, matricula = ? WHERE nombre = ?");
    $stmt->bind_param("ssss", $nombreNuevo, $descripcion, $matricula, $nombreOriginal);
    $stmt->execute();
    $_SESSION['mensaje'] = "Rutina actualizada exitosamente.";
    header("Location: rutinas_entrenador.php");
    exit();
}

// Asignar ejercicio a rutina
if (isset($_POST['agregar_ejercicio'])) {
    $rutina = trim($_POST['rutina_nombre']);
    $ejercicio = trim($_POST['ejercicio_nombre']);

    if (!empty($rutina) && !empty($ejercicio)) {
        $stmt = $conn->prepare("INSERT INTO rutina_ejercicios (rutina_nombre, ejercicio_nombre) VALUES (?, ?)");
        $stmt->bind_param("ss", $rutina, $ejercicio);
        $stmt->execute();
        $_SESSION['mensaje'] = "Ejercicio asignado a la rutina correctamente.";
    } else {
        $_SESSION['error'] = "Debe seleccionar una rutina y un ejercicio.";
    }
    header("Location: rutinas_entrenador.php");
    exit();
}

// Obtener rutinas
$query = "
    SELECT r.nombre, r.descripcion, r.fecha, l.matricula, CONCAT(l.nombres, ' ', l.apellidopaterno, ' ', l.apellidomaterno) AS alumno
    FROM rutinas r
    JOIN login l ON r.matricula = l.matricula
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutinas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h3 {
            text-align: center;
            color: #6A0DAD;
            font-weight: 600;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            display: inline-block;
        }
        .btn-agregar {
            background-color: #6A0DAD;
            width: 100%;
        }
        .btn-agregar:hover {
            background-color: #5A0CAB;
        }
        .btn-guardar {
            background-color: #28A745;
        }
        .btn-guardar:hover {
            background-color: #218838;
        }
        .btn-eliminar {
            background-color: #DC3545;
        }
        .btn-eliminar:hover {
            background-color: #C82333;
        }
        .form-control, textarea {
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <h3>Rutinas</h3>

    <!-- Formulario para agregar rutina -->
    <form action="rutinas_entrenador.php" method="POST">
        <input type="text" name="nombre_rutina" class="form-control" placeholder="Nombre de la rutina" required>
        <textarea name="descripcion_rutina" class="form-control" placeholder="Descripción de la rutina" required></textarea>
        <select name="matricula" class="form-control" required>
            <option value="" disabled selected>Seleccionar alumno</option>
            <?php foreach ($alumnos as $alumno): ?>
                <option value="<?php echo $alumno['matricula']; ?>"><?php echo $alumno['nombre_completo']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="agregar_rutina" class="btn btn-agregar">Agregar Rutina</button>
    </form>

    <!-- Formulario para asignar ejercicio -->
    <form action="rutinas_entrenador.php" method="POST" class="mt-4">
        <h3>Asignar Ejercicio a Rutina</h3>
        <select name="rutina_nombre" class="form-control" required>
            <option value="" disabled selected>Seleccionar rutina</option>
            <?php foreach ($rutinas as $rutina): ?>
                <option value="<?php echo $rutina['nombre']; ?>"><?php echo $rutina['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="ejercicio_nombre" class="form-control" required>
            <option value="" disabled selected>Seleccionar ejercicio</option>
            <?php foreach ($ejercicios as $ejercicio): ?>
                <option value="<?php echo $ejercicio['nombre']; ?>"><?php echo $ejercicio['nombre']; ?> - <?php echo $ejercicio['categoria']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="agregar_ejercicio" class="btn btn-agregar">Agregar Ejercicio</button>
    </form>
</div>
</body>
</html>
