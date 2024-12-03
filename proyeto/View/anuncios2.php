<?php
session_start();
include('../Controller/conexionn.php'); // Conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['nombre_usuario'])) {
    $_SESSION['error'] = "Debes iniciar sesión para acceder a esta página.";
    header("Location: index.php");
    exit();
}

// Publicar un aviso
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publicar_aviso'])) {
    $autor = $_SESSION['nombre_usuario']; // Autor es el nombre completo del usuario logueado
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];

    $sql = "INSERT INTO avisos (titulo, contenido, autor, fecha) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $titulo, $contenido, $autor);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Aviso publicado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al publicar el aviso: " . $stmt->error;
    }
    $stmt->close();

    header("Location: anuncios2.php");
    exit();
}

// Editar un aviso
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_cambios'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];

    $sql = "UPDATE avisos SET titulo = ?, contenido = ?, fecha = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $titulo, $contenido, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Aviso actualizado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar el aviso: " . $stmt->error;
    }
    $stmt->close();

    header("Location: anuncios2.php");
    exit();
}

// Eliminar un aviso
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM avisos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Aviso eliminado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el aviso: " . $stmt->error;
    }
    $stmt->close();

    header("Location: anuncios2.php");
    exit();
}

// Recuperar todos los avisos
$sql = "SELECT * FROM avisos ORDER BY fecha DESC";
$resultado = mysqli_query($conn, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avisos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Barra lateral -->
<div class="sidebar" id="sidebar">
    <h2>Menú</h2>
    <a href="../Model/rutinas_entrenador2.php">Agregar Rutinas</a>
        <a href="lista_ejercicios2.php">Lista de Ejercicios</a>
        <a href="recomendacion2.php">Agregar Recomendación</a>
        <a href="entrenador.php">Volver </a>

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
            margin-top: 20px;
            font-weight: 600;
        }
        .form-container, .avisos-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            color: #6A0DAD;
            text-align: center;
            font-weight: 600;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .btn-publicar {
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn-publicar:hover {
            background-color: #5A0CAB;
        }
        .btn-guardar, .btn-eliminar {
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            border: none;
        }
        .btn-guardar {
            background-color: #28A745;
            color: white;
            margin-right: 5px;
        }
        .btn-guardar:hover {
            background-color: #218838;
        }
        .btn-eliminar {
            background-color: #EA4335;
            color: white;
        }
        .btn-eliminar:hover {
            background-color: #C72D23;
        }
        .aviso {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .aviso h3 {
            color: #6A0DAD;
            margin: 0 0 10px;
            font-weight: 600;
        }
        .meta-info {
            color: #6A0DAD;
            font-size: 14px;
        }
        .btn-container {
            display: flex;
            gap: 5px; /* Espaciado mínimo entre botones */
        }
    </style>
</head>
<body>
    <h1>Avisos</h1>

    <!-- Formulario para agregar un nuevo aviso -->
    <div class="form-container">
        <h2>Publicar un nuevo aviso</h2>
        <form method="POST" action="">
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="contenido" placeholder="Contenido" required></textarea>
            <button type="submit" name="publicar_aviso" class="btn-publicar">Publicar Aviso</button>
        </form>
    </div>

    <!-- Lista de avisos -->
    <div class="avisos-container">
        <?php while ($aviso = mysqli_fetch_assoc($resultado)): ?>
            <div class="aviso">
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $aviso['id']; ?>">
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($aviso['titulo']); ?>" required>
                    <textarea name="contenido" required><?php echo htmlspecialchars($aviso['contenido']); ?></textarea>
                    <p class="meta-info">Publicado por: <strong><?php echo $aviso['autor']; ?></strong> el <strong><?php echo $aviso['fecha']; ?></strong></p>
                    <div class="btn-container">
                        <button type="submit" name="guardar_cambios" class="btn-guardar">Guardar</button>
                        <a href="?eliminar=<?php echo $aviso['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este aviso?')">Eliminar</a>
                    </div>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
