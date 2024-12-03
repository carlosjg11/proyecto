<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #7B2CBF;
            color: white;
            overflow-x: hidden;
            padding-top: 20px;
            transition: all 0.3s ease;
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
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
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
            z-index: 1000;
        }

        .toggle-btn:hover {
            background-color: #5A0CAB;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f4f4f4;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        .content.expanded {
            margin-left: 0;
            width: 100%;
        }

        .card {
            width: 700px; /* Ancho más grande */
            margin: 0 auto;
        }

        .card-body {
            padding: 30px; /* Reduce el espacio vertical */
        }

        footer.footer-derechos {
            background-color: #6A0DAD;
            color: white;
            padding: 2px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Botón para mostrar/ocultar el menú -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Barra lateral -->
    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
        
       
        <a href="lista_ejercicios.php">Lista de Ejercicios</a>
        <a href="../Model/rutinas_entrenador.php">Agregar Rutina</a>
        <a href="recomendaciones.php">Recomendaciones</a>
        <a href="anuncios.php">Agregar Avisos</a>
        <a href="agelmo.php">Modificar/Eliminar Usuarios</a>
        <a href="../Controller/respaldo.php">Respaldo de base de datos</a>
        <a href="Reports/verReportes.php">Ver reportes</a>
        <a href="admin.php">Volver</a>
    </div>

    <!-- Contenido principal -->
    <div class="content" id="content">
        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Crear una cuenta</h2>
                            <form action="../Controller/login_registrar.php" method="POST">
                                <!-- Campos del formulario -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="matricula" class="form-label">Matrícula</label>
                                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="&#128273; Matrícula" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido Paterno" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Apellido Materno" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nombres" class="form-label">Nombres</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="pass" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="pass" name="pass" placeholder="&#128274; Contraseña" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rol" class="form-label">Rol</label>
                                        <select class="form-select" id="rol" name="rol" required>
                                            <option value="0" disabled selected>Seleccionar</option>
                                            <option value="alumno">Alumno</option>
                                            <option value="admin">Administrador</option>
                                            <option value="entrenador">Entrenador</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sexo" class="form-label">Sexo</label>
                                        <select class="form-select" id="sexo" name="sexo" required>
                                            <option value="0" disabled selected>Seleccionar</option>
                                            <option value="Hombre">Hombre</option>
                                            <option value="Mujer">Mujer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="carrera" class="form-label">Carrera</label>
                                    <select class="form-select" id="carrera" name="carrera" required>
                                        <option value="0" disabled selected>Seleccionar</option>
                                        <option value="ITI">ITI</option>
                                        <option value="LAE">LAE</option>
                                        <option value="IBT">IBT</option>
                                        <option value="ITA">ITA</option>
                                        <option value="IIN">IIN</option>
                                        <option value="IET">IET</option>
                                        <option value="IFI">IFI</option>
                                    </select>
                                </div>
                                <input type="hidden" name="fecha_hora_registro" value="<?php echo $fecha_hora_registro; ?>">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" name="btnregistrar">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
