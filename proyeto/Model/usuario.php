<?php
require_once('../Controller/conexionn.php'); // Cambia la ruta si es necesario

// Función para obtener todos los usuarios
function getUsuarios($conn) {
    $sql = "SELECT * FROM login";
    $result = mysqli_query($conn, $sql);
    $usuarios = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $usuarios[] = $row;
    }

    return $usuarios;
}

// Función para obtener un usuario específico
function getUsuario($conn, $matricula) {
    $sql = "SELECT * FROM login WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $matricula);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Función para actualizar un usuario
function updateUsuario($conn, $data) {
    $sql = "UPDATE login SET nombres = ?, apellidopaterno = ?, apellidomaterno = ?, rol = ?, sexo = ?, fechanacimiento = ?, carrera = ? WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $data['nombres'], $data['apellidopaterno'], $data['apellidomaterno'], $data['rol'], $data['sexo'], $data['fechanacimiento'], $data['carrera'], $data['matricula']);
    return mysqli_stmt_execute($stmt);
}

// Función para eliminar un usuario
function deleteUsuario($conn, $matricula) {
    $sql = "DELETE FROM login WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $matricula);
    return mysqli_stmt_execute($stmt);
}

// Lógica de enrutamiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    updateUsuario($conn, $_POST);
    header("Location: View/agelmo.php");
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    deleteUsuario($conn, $_GET['matricula']);
    header("Location: View/agelmo.php");
    exit;
}
?>
