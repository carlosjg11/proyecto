<?php
session_start();
include('./conexionn.php');

// Verifica si se recibió la matrícula
if (isset($_GET['matricula'])) {
    $matricula = $_GET['matricula'];
    
    // Consulta para obtener los datos del usuario
    $query = "SELECT * FROM login WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $matricula);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);
    } else {
        echo "No se encontró la información del usuario.";
        exit();
    }
} else {
    echo "No se especificó la matrícula del usuario.";
    exit();
}

// Actualizar los datos del usuario cuando se envíe el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matriculaNueva = $_POST['matricula'];
    $pass = $_POST['pass'];
    $rol = $_POST['rol'];
    $sexo = $_POST['sexo'];
    $fechanacimiento = $_POST['fechanacimiento'];
    $apellidopaterno = $_POST['apellidopaterno'];
    $apellidomaterno = $_POST['apellidomaterno'];
    $nombres = $_POST['nombres'];
    $carrera = $_POST['carrera'];
    $fechaRegistro = date('Y-m-d'); // Guardar solo la fecha actual
    
    $updateQuery = "UPDATE login 
                    SET matricula = ?, pass = ?, rol = ?, sexo = ?, fechanacimiento = ?, apellidopaterno = ?, apellidomaterno = ?, nombres = ?, carrera = ?, fechaHoraRegistro = ? 
                    WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssssssss", $matriculaNueva, $pass, $rol, $sexo, $fechanacimiento, $apellidopaterno, $apellidomaterno, $nombres, $carrera, $fechaRegistro, $matricula);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Usuario actualizado exitosamente'); window.location.href = '../View/agelmo.php';</script>";
    } else {
        echo "Error al actualizar el usuario: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../View/Css/editar_style.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Editar Información del Usuario</h1>
        <form method="POST">
    <label for="matricula">Matrícula:</label>
    <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($usuario['matricula'] ?? ''); ?>" required>

    <label for="pass">Contraseña:</label>
    <input type="text" id="pass" name="pass" value="<?php echo htmlspecialchars($usuario['pass'] ?? ''); ?>" required>

    <label for="rol">Rol:</label>
    <select id="rol" name="rol">
        <option value="admin" <?php if (($usuario['rol'] ?? '') == 'admin') echo 'selected'; ?>>Admin</option>
        <option value="alumno" <?php if (($usuario['rol'] ?? '') == 'alumno') echo 'selected'; ?>>Alumno</option>
        <option value="entrenador" <?php if (($usuario['rol'] ?? '') == 'entrenador') echo 'selected'; ?>>Entrenador</option>
    </select>

    <label for="sexo">Sexo:</label>
    <select id="sexo" name="sexo">
        <option value="Hombre" <?php if (($usuario['sexo'] ?? '') == 'Hombre') echo 'selected'; ?>>Hombre</option>
        <option value="Mujer" <?php if (($usuario['sexo'] ?? '') == 'Mujer') echo 'selected'; ?>>Mujer</option>
    </select>

    <label for="fechanacimiento">Fecha de Nacimiento:</label>
    <input type="date" id="fechanacimiento" name="fechanacimiento" value="<?php echo htmlspecialchars($usuario['fechanacimiento'] ?? ''); ?>" required>

    <label for="apellidopaterno">Apellido Paterno:</label>
    <input type="text" id="apellidopaterno" name="apellidopaterno" value="<?php echo htmlspecialchars($usuario['apellidopaterno'] ?? ''); ?>" required>

    <label for="apellidomaterno">Apellido Materno:</label>
    <input type="text" id="apellidomaterno" name="apellidomaterno" value="<?php echo htmlspecialchars($usuario['apellidomaterno'] ?? ''); ?>" required>

    <label for="nombres">Nombres:</label>
    <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($usuario['nombres'] ?? ''); ?>" required>

    <div class="mb-3">
        <label for="carrera" class="form-label">Carrera</label>
        <select class="form-select" id="carrera" name="carrera" required>
            <option value="0" disabled selected>Seleccionar</option>
            <option value="ITI" <?php if (($usuario['carrera'] ?? '') == 'ITI') echo 'selected'; ?>>ITI</option>
            <option value="LAE" <?php if (($usuario['carrera'] ?? '') == 'LAE') echo 'selected'; ?>>LAE</option>
            <option value="IBT" <?php if (($usuario['carrera'] ?? '') == 'IBT') echo 'selected'; ?>>IBT</option>
            <option value="ITA" <?php if (($usuario['carrera'] ?? '') == 'ITA') echo 'selected'; ?>>ITA</option>
            <option value="IIN" <?php if (($usuario['carrera'] ?? '') == 'IIN') echo 'selected'; ?>>IIN</option>
            <option value="IET" <?php if (($usuario['carrera'] ?? '') == 'IET') echo 'selected'; ?>>IET</option>
            <option value="IFI" <?php if (($usuario['carrera'] ?? '') == 'IFI') echo 'selected'; ?>>IFI</option>
        </select>
    </div>

    <div class="button-container">
        <button type="submit">Guardar Cambios</button>
    </div>
</form>

                
            </div>
        </form>
    </div>
</body>
</html>
