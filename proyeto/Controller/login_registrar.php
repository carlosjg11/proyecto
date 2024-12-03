<?php
include("./conexionn.php");  

// Captura los datos del formulario
$matricula = $_POST["matricula"];
$pass = $_POST["pass"];
$rol = $_POST["rol"];
$carrera = $_POST["carrera"];
$sexo = $_POST["sexo"];
$fechanacimiento = $_POST["fecha_nacimiento"];
$apellidopaterno = $_POST["apellido_paterno"];
$apellidomaterno = $_POST["apellido_materno"];
$nombres = $_POST["nombres"];
$fechaHoraRegistro = date('Y-m-d H:i:s'); // Fecha y hora actuales

// Registrar
if (isset($_POST["btnregistrar"])) {
    
    // Usar mysqli_real_escape_string para evitar inyección SQL
    $matricula = mysqli_real_escape_string($conn, $matricula);
    $pass = mysqli_real_escape_string($conn, $pass);
    $rol = mysqli_real_escape_string($conn, $rol);
    $sexo = mysqli_real_escape_string($conn, $sexo);
    $fechanacimiento = mysqli_real_escape_string($conn, $fechanacimiento);
    $apellidopaterno = mysqli_real_escape_string($conn, $apellidopaterno);
    $apellidomaterno = mysqli_real_escape_string($conn, $apellidomaterno);
    $nombres = mysqli_real_escape_string($conn, $nombres);
    $carrera = mysqli_real_escape_string($conn, $carrera);
    $fechaHoraRegistro = mysqli_real_escape_string($conn, $fechaHoraRegistro);
    
    // Consulta SQL para insertar los datos en la tabla `login`
    $sqlgrabar = "INSERT INTO login (matricula, pass, rol, sexo, fechanacimiento, apellidopaterno, apellidomaterno, nombres, carrera, fechaHoraRegistro) 
                  VALUES ('$matricula', '$pass', '$rol', '$sexo', '$fechanacimiento', '$apellidopaterno', '$apellidomaterno', '$nombres', '$carrera', '$fechaHoraRegistro')";

    // Verificar si la consulta fue exitosa
    if (mysqli_query($conn, $sqlgrabar)) {
        echo "<script> alert('Usuario registrado con éxito: $matricula'); window.location='../index.php' </script>";
    } else {
        echo "Error: " . $sqlgrabar . "<br>" . mysqli_error($conn);  
    }
}

mysqli_close($conn);
?>
