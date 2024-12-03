<?php
require_once('../Model/usuario.php'); // Incluye el archivo con las funciones

// Obtener los usuarios de la base de datos
$usuarios = getUsuarios($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Barra lateral -->
<div class="sidebar" id="sidebar">
    <h2>Menú</h2>
    <a href="lista_ejercicios.php">Lista de Ejercicios</a>
        <a href="../Model/rutinas_entrenador.php">Agregar Rutina</a>
        <a href="recomendaciones.php">Recomendaciones</a>
        <a href="anuncios.php">Agregar Avisos</a>
        <a href="../Controller/respaldo.php">Respaldo de base de datos</a>
        <a href="Reports/verReportes.php">Ver reportes</a>
        <a href="admin.php">Volver</a>

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
    <link rel="stylesheet" href="Css/morado_style.css"> <!-- Enlace al CSS -->
</head>
<body>
    <div class="container">
        <h2 class="title">Lista de Usuarios</h2>
        <?php if ($usuarios === null || empty($usuarios)): ?>
            <p>No se encontraron usuarios.</p>
        <?php else: ?>
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
                        <th>Fecha y Hora de Registro</th> <!-- Cambié el título para incluir la hora -->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($row['pass']); ?></td>
                            <td><?php echo htmlspecialchars($row['rol']); ?></td>
                            <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($row['fechanacimiento']); ?></td>
                            <td><?php echo htmlspecialchars($row['apellidopaterno']); ?></td>
                            <td><?php echo htmlspecialchars($row['apellidomaterno']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($row['carrera']); ?></td>
                            <!-- Aquí se incluye tanto la fecha como la hora -->
                            <td><?php echo htmlspecialchars($row['fechaHoraRegistro']); ?></td>
                            <td class="actions">
                                <a href="../Controller/editar_usuario.php?matricula=<?php echo urlencode($row['matricula']); ?>" class="edit-btn">Editar</a>
                                <a href="../Controller/eliminar_usuario.php?matricula=<?php echo urlencode($row['matricula']); ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
