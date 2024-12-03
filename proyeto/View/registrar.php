<?php
$fecha_hora_registro = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .footer-derechos {
            background-color: #6A0DAD;
            color: white;
            padding: 2px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .form-control::placeholder {
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Crear una cuenta</h2>
                        <form action="../Controller/login_registrar.php" method="POST">
                            <div class="row g-3">
                                <!-- Campo Matrícula -->
                                <div class="col-md-6">
                                    <label for="matricula" class="form-label">Matrícula</label>
                                    <input type="text" class="form-control" id="matricula" name="matricula" placeholder="&#128273; Matrícula" required>
                                </div>
                                <!-- Apellido Paterno -->
                                <div class="col-md-6">
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido Paterno" required>
                                </div>
                                <!-- Apellido Materno -->
                                <div class="col-md-6">
                                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Apellido Materno" required>
                                </div>
                                <!-- Nombres -->
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label">Nombres</label>
                                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required>
                                </div>
                                <!-- Contraseña -->
                                <div class="col-md-6">
                                    <label for="pass" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="pass" name="pass" placeholder="&#128274; Contraseña" required>
                                </div>
                                <!-- Rol -->
                                <div class="col-md-6">
                                    <label for="rol" class="form-label">Rol</label>
                                    <select class="form-select" id="rol" name="rol" required>
                                        <option value="0" disabled selected>Seleccionar</option>
                                        <option value="alumno">Alumno</option>
                                    </select>
                                </div>
                                <!-- Sexo -->
                                <div class="col-md-6">
                                    <label for="sexo" class="form-label">Sexo</label>
                                    <select class="form-select" id="sexo" name="sexo" required>
                                        <option value="0" disabled selected>Seleccionar</option>
                                        <option value="Hombre">Hombre</option>
                                        <option value="Mujer">Mujer</option>
                                    </select>
                                </div>
                                <!-- Fecha de Nacimiento -->
                                <div class="col-md-6">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <!-- Carrera -->
                                <div class="col-md-6">
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
                            </div>
                            <!-- Fecha y hora de registro oculta -->
                            <input type="hidden" name="fecha_hora_registro" value="<?php echo $fecha_hora_registro; ?>">
                            <!-- Botón de registro -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary" name="btnregistrar">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    
</html>
