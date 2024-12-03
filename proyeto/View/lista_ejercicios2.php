<?php
session_start();

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyecto";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejo de agregar ejercicio
if (isset($_POST['agregar'])) {
    $nombreEjercicio = trim($_POST['ejercicio']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);

    if (!empty($nombreEjercicio)) {
        $stmt = $conn->prepare("INSERT INTO ejercicios (nombre, descripcion, categoria) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombreEjercicio, $descripcion, $categoria);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Ejercicio agregado correctamente.";
        } else {
            $_SESSION['error'] = "Error al agregar ejercicio: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Manejo de eliminar ejercicio
if (isset($_POST['eliminar'])) {
    $nombreEjercicio = trim($_POST['nombre_original']);

    $stmt = $conn->prepare("DELETE FROM ejercicios WHERE nombre = ?");
    $stmt->bind_param("s", $nombreEjercicio);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Ejercicio eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar ejercicio: " . $stmt->error;
    }
    $stmt->close();
}

// Manejo de modificar ejercicio
if (isset($_POST['editar'])) {
    $nombreOriginal = trim($_POST['nombre_original']);
    $nombreNuevo = trim($_POST['nombre_nuevo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);

    $stmt = $conn->prepare("UPDATE ejercicios SET nombre = ?, descripcion = ?, categoria = ? WHERE nombre = ?");
    $stmt->bind_param("ssss", $nombreNuevo, $descripcion, $categoria, $nombreOriginal);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Ejercicio actualizado correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar ejercicio: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener la lista de ejercicios desde la base de datos
$result = $conn->query("SELECT nombre, descripcion, categoria FROM ejercicios");
$ejercicios = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ejercicios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ejercicios</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Botón para mostrar/ocultar la barra lateral -->
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Barra lateral -->
<div class="sidebar" id="sidebar">
    <h2>Menú</h2>
    
        <a href="recomendaciones2.php">Agregar Recomendación</a>
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
        h1 {
            text-align: center;
            color: #6A0DAD;
            font-weight: 600;
            margin-top: 20px;
        }
        .form-container, .list-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .btn-primary {
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .btn-primary:hover {
            background-color: #5A0CAB;
        }
        .btn-container {
            display: flex;
            justify-content: start;
            gap: 10px;
            margin-top: 10px;
        }
        .btn-guardar {
            background-color: #28A745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-guardar:hover {
            background-color: #218838;
        }
        .btn-eliminar {
            background-color: #EA4335;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-eliminar:hover {
            background-color: #C72D23;
        }
        .list-item {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <h1>Gestión de Ejercicios</h1>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div style="text-align: center; color: green;">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="text-align: center; color: red;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar un ejercicio -->
    <div class="form-container">
        <h2>Registrar un nuevo ejercicio</h2>
        <form action="" method="POST">
            <input type="text" name="ejercicio" placeholder="Nombre del ejercicio" required>
            <textarea name="descripcion" placeholder="Descripción del ejercicio" required></textarea>
            <select name="categoria" required>
                <option value="" disabled selected>Categoría</option>
                <option value="Tren superior">Tren superior</option>
                <option value="Cardio">Cardio</option>
                <option value="Pierna">Pierna</option>
            </select>
            <button type="submit" name="agregar" class="btn-primary">Agregar Ejercicio</button>
        </form>
    </div>

    <!-- Lista de ejercicios -->
    <div class="list-container">
        <?php if (!empty($ejercicios)): ?>
            <?php foreach ($ejercicios as $ejercicio): ?>
                <div class="list-item">
                    <form action="" method="POST">
                        <input type="hidden" name="nombre_original" value="<?php echo htmlspecialchars($ejercicio['nombre']); ?>">
                        <input type="text" name="nombre_nuevo" value="<?php echo htmlspecialchars($ejercicio['nombre']); ?>" required>
                        <textarea name="descripcion" required><?php echo htmlspecialchars($ejercicio['descripcion']); ?></textarea>
                        <select name="categoria" required>
                            <option value="Tren superior" <?php echo $ejercicio['categoria'] == 'Tren superior' ? 'selected' : ''; ?>>Tren superior</option>
                            <option value="Cardio" <?php echo $ejercicio['categoria'] == 'Cardio' ? 'selected' : ''; ?>>Cardio</option>
                            <option value="Pierna" <?php echo $ejercicio['categoria'] == 'Pierna' ? 'selected' : ''; ?>>Pierna</option>
                        </select>
                        <div class="btn-container">
                            <button type="submit" name="editar" class="btn-guardar">Guardar</button>
                            <button type="submit" name="eliminar" class="btn-eliminar">Eliminar</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No hay ejercicios registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>
