<?php
session_start();
session_unset(); // Limpia las variables de sesión
session_destroy(); // Destruye la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrando sesión</title>
    <script>
        // Mostrar una alerta y redirigir al index
        alert("Has cerrado sesión exitosamente.");
        window.location.href = "../index.php"; // Cambia "index.php" por el archivo de inicio de sesión si es diferente
    </script>
</head>
<body>
</body>
</html>
