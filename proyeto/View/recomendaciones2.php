<?php
// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../Controller/conexionn.php'); // Archivo para la conexión a la base de datos

class Recomendaciones {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método para agregar una recomendación
    public function agregarRecomendacion($autor, $alumno, $comentario) {
        $stmt = $this->conn->prepare("INSERT INTO recomendaciones (autor, alumno, comentario, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $autor, $alumno, $comentario);
        return $stmt->execute();
    }

    // Método para obtener todas las recomendaciones
    public function obtenerRecomendaciones() {
        $sql = "SELECT * FROM recomendaciones ORDER BY fecha DESC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Método para editar una recomendación
    public function editarRecomendacion($id, $comentario) {
        $stmt = $this->conn->prepare("UPDATE recomendaciones SET comentario = ?, fecha = NOW() WHERE id = ?");
        $stmt->bind_param("si", $comentario, $id);
        return $stmt->execute();
    }

    // Método para eliminar una recomendación
    public function eliminarRecomendacion($id) {
        $stmt = $this->conn->prepare("DELETE FROM recomendaciones WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Método para obtener todos los alumnos
    public function obtenerAlumnos() {
        $sql = "SELECT matricula, CONCAT(nombres, ' ', apellidopaterno, ' ', apellidomaterno) AS nombre_completo FROM login WHERE rol = 'Alumno'";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

// Crear instancia de la clase Recomendaciones
$recomendaciones = new Recomendaciones($conn);

// Manejar formulario de agregar recomendación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $autor = $_SESSION['nombre_usuario'] ?? 'Entrenador Anónimo'; // Autor de la recomendación
    $alumno = $_POST['alumno'];
    $comentario = $_POST['comentario'];

    if ($recomendaciones->agregarRecomendacion($autor, $alumno, $comentario)) {
        $_SESSION['mensaje'] = "Recomendación agregada correctamente.";
    } else {
        $_SESSION['error'] = "Error al agregar la recomendación.";
    }
    header("Location: recomendaciones2.php");
    exit();
}

// Manejar formulario de editar recomendación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $comentario = $_POST['comentario'];

    if ($recomendaciones->editarRecomendacion($id, $comentario)) {
        $_SESSION['mensaje'] = "Recomendación actualizada correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar la recomendación.";
    }
    header("Location: recomendaciones2.php");
    exit();
}

// Manejar solicitud de eliminar recomendación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    if ($recomendaciones->eliminarRecomendacion($id)) {
        $_SESSION['mensaje'] = "Recomendación eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la recomendación.";
    }
    header("Location: recomendaciones2.php");
    exit();
}

// Obtener todas las recomendaciones
$listaRecomendaciones = $recomendaciones->obtenerRecomendaciones();

// Obtener lista de alumnos
$listaAlumnos = $recomendaciones->obtenerAlumnos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recomendaciones</title>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Barra lateral -->
<div class="sidebar" id="sidebar">
    <h2>Menú</h2>
    
    <a href="../Model/rutinas_entrenador2.php">Agregar Rutinas</a>
        <a href="lista_ejercicios2.php">Lista de Ejercicios</a>
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
        select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .btn-agregar, .btn-editar, .btn-eliminar {
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-block;
        }
        .btn-editar {
            background-color: #28A745;
        }
        .btn-eliminar {
            background-color: #EA4335;
        }
        .btn-agregar:hover {
            background-color: #5A0CAB;
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
    </style>
</head>
<body>
    <h1>Recomendaciones</h1>

    <!-- Formulario para agregar recomendación -->
    <div class="container">
        <h2>Agregar Recomendación</h2>
        <form method="POST" action="">
            <label for="alumno">Alumno:</label>
            <select id="alumno" name="alumno" required>
                <option value="" disabled selected>Selecciona un alumno</option>
                <?php foreach ($listaAlumnos as $alumno): ?>
                    <option value="<?php echo $alumno['nombre_completo']; ?>">
                        <?php echo $alumno['nombre_completo']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="comentario">Comentario:</label>
            <textarea id="comentario" name="comentario" placeholder="Escribe tu recomendación aquí..." required></textarea>
            <button type="submit" name="agregar" class="btn-agregar">Agregar Recomendación</button>
        </form>
    </div>

    <!-- Lista de recomendaciones -->
    <div class="container">
        <h2>Lista de Recomendaciones</h2>
        <?php if (empty($listaRecomendaciones)): ?>
            <p>No hay recomendaciones disponibles.</p>
        <?php else: ?>
            <?php foreach ($listaRecomendaciones as $recomendacion): ?>
                <div class="recomendacion">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $recomendacion['id']; ?>">
                        <h3>Para: <?php echo htmlspecialchars($recomendacion['alumno']); ?></h3>
                        <textarea name="comentario" required><?php echo htmlspecialchars($recomendacion['comentario']); ?></textarea>
                        <p class="meta-info">Por: <?php echo htmlspecialchars($recomendacion['autor']); ?> | Fecha: <?php echo htmlspecialchars($recomendacion['fecha']); ?></p>
                        <button type="submit" name="editar" class="btn-editar">Guardar Cambios</button>
                        <a href="?eliminar=<?php echo $recomendacion['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta recomendación?')">Eliminar</a>
                        <a href="observaciones_recomendaciones2.php?id=<?php echo $recomendacion['id']; ?>">Abrir recomendaciones</a>

                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
