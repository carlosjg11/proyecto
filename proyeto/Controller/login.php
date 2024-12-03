<?php
session_start();
include('conexionn.php'); // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricula = trim($_POST["txtmatricula"]);
    $pass = trim($_POST["txtpassword"]);
    $rol = trim($_POST["rol"]);

    // Validar campos vacíos
    if (empty($matricula) || empty($pass) || empty($rol)) {
        die("Todos los campos son obligatorios.");
    }

    // Consulta preparada para validar el usuario
    $queryusuario = mysqli_prepare($conn, "SELECT * FROM login WHERE matricula = ? AND pass = ? AND rol = ?");
    mysqli_stmt_bind_param($queryusuario, "sss", $matricula, $pass, $rol);
    mysqli_stmt_execute($queryusuario);
    $result = mysqli_stmt_get_result($queryusuario);

    if ($result && $usuario_data = mysqli_fetch_assoc($result)) {
        // Almacenar matrícula y nombre completo en la sesión
        $_SESSION['matricula'] = $usuario_data['matricula']; // Matrícula del usuario
        $_SESSION['nombre_usuario'] = $usuario_data['nombres'] . ' ' . $usuario_data['apellidopaterno'] . ' ' . $usuario_data['apellidomaterno'];

        // Redirigir según el rol
        if ($rol == "Admin") {
            header("Location: ../View/admin.php");
        } elseif ($rol == "Alumno") {
            header("Location: ../View/banco.php");
        } elseif ($rol == "Entrenador") {
            header("Location: ../View/entrenador.php");
        } else {
            echo "Rol no válido.";
        }
        exit();
    } else {
        echo "<script>alert('Matrícula, contraseña o rol incorrecto.'); window.location = '../index.php';</script>";
    }
}
?>
