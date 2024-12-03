<!--
(index.html) Login 	  -> Si el usuario existe 	 -> principal.html
(index.html) Login    -> Si el usuario no existe -> (index.html) Login
 registrar.html       -> Nuevo usuario           -> (index.html) Login

El login tendrá la opción para registrar.
Usar "estilos.css" en el ejercicio.

-->

<?php
// Datos de conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "proyecto";
$port = 3306; // Cambiar a 3307 si es necesario

// Establecer conexión
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
} else {
   
}
?>
