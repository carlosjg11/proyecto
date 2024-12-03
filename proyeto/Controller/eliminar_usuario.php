<?php
session_start();
include('./conexionn.php');

// Verifica si se recibió la matrícula
if (isset($_GET['matricula'])) {
    $matricula = $_GET['matricula'];
    
    // Consulta para eliminar el usuario
    $query = "DELETE FROM login WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $matricula);
    
    if (mysqli_stmt_execute($stmt)) {
        // Destruye la sesión después de eliminar el usuario
        session_destroy();
        
        // Redirige al usuario al index.php
        echo "<script>alert('Cuenta eliminada exitosamente'); window.location.href = '../View/agelmo.php';</script>";
    } else {
        echo "Error al eliminar el usuario: " . mysqli_error($conn);
    }
} else {
    echo "No se especificó la matrícula del usuario.";
    exit();
}
?>
