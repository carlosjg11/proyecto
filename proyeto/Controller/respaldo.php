<?php
// Configuración de conexión
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'proyecto';

// Ruta completa a mysqldump y mysql (ajusta esta ruta según tu instalación de XAMPP)
$mysqldumpPath = "C:/xampp/mysql/bin/mysqldump.exe";
$mysqlPath = "C:/xampp/mysql/bin/mysql.exe";

// Función para hacer el respaldo de la base de datos y descargar el archivo
function respaldarBaseDeDatos($host, $user, $password, $database, $mysqldumpPath) {
    $fecha = date('Y-m-d_H-i-s');
    $archivo_respaldo = "{$database}_respaldo_{$fecha}.sql";
    
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename={$archivo_respaldo}");
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $comando = "{$mysqldumpPath} -h {$host} -u {$user} --password={$password} {$database}";
    $output = null;
    $return_var = null;
    exec($comando, $output, $return_var);
    
    if ($return_var === 0) {
        foreach ($output as $linea) {
            echo $linea . "\n";
        }
    } else {
        echo "Error al realizar el respaldo. Código de error: " . $return_var;
        echo "<br>Salida del comando: " . implode("\n", $output);
    }
    exit();
}

// Función para restaurar la base de datos desde un archivo SQL
function restaurarBaseDeDatos($host, $user, $password, $database, $mysqlPath, $archivo_sql) {
    if ($_FILES['archivo_sql']['error'] !== UPLOAD_ERR_OK) {
        echo "Error al cargar el archivo. Código de error: " . $_FILES['archivo_sql']['error'];
        return;
    }

    $archivo_temporal = $_FILES['archivo_sql']['tmp_name'];
    $comando = "{$mysqlPath} -h {$host} -u {$user} --password={$password} {$database} < {$archivo_temporal}";
    exec($comando, $output, $return_var);
    
    if ($return_var === 0) {
        // Redirigir a la misma página con un mensaje de éxito
        header("Location: " . $_SERVER['PHP_SELF'] . "?mensaje=restauracion_exitosa");
        exit();
    } else {
        echo "Error al restaurar la base de datos. Código de error: " . $return_var;
        echo "<br>Salida del comando: " . implode("\n", $output);
    }
    exit();
}

// Verificar si se está haciendo un respaldo o restauración
if (isset($_POST['accion'])) {
    if ($_POST['accion'] == 'respaldar') {
        respaldarBaseDeDatos($host, $user, $password, $database, $mysqldumpPath);
        echo "<script>alert('El respaldo de la base de datos se descargó correctamente.');</script>";
    }
    if ($_POST['accion'] == 'restaurar') {
        restaurarBaseDeDatos($host, $user, $password, $database, $mysqlPath, $_FILES['archivo_sql']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respaldo y Restauración de Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
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
        }
        .sidebar h2 {
            text-align: center;
            color: white;
            margin-bottom: 20px;
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
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        button {
            background-color: #4a148c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #7b1fa2;
        }
        input[type="file"] {
            display: block;
            margin: 10px auto;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <div class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <a href="infoalumno.php">Ver mi información</a>
        <a href="../Model/verejer.php">Lista de Ejercicios</a>
        <a href="verAnuncios.php">Ver Anuncios</a>
        <a href="verrecomendacion.php">Ver Recomendaciones</a>
        <a href="vermiprogreso.php">Ver mi progreso</a>
        <a href="listarRutinas.php">Rutinas</a>
        <a href="../View/admin.php">Volver</a>
    </div>

    <div class="content">
        <h1>Respaldo y Restauración de la Base de Datos</h1>
        <div class="container">
            <h2>Respaldo de la Base de Datos</h2>
            <form method="post">
                <button type="submit" name="accion" value="respaldar">Descargar Respaldo</button>
            </form>
            <h2>Restaurar Base de Datos</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="archivo_sql" accept=".sql" required>
                <button type="submit" name="accion" value="restaurar">Restaurar Base de Datos</button>
            </form>
        </div>
        
        <?php
        // Verificar si hay un mensaje de éxito por la restauración
        if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'restauracion_exitosa') {
            echo "<script>alert('La base de datos se restauró correctamente.');</script>";
        }
        ?>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.content');
            if (sidebar.style.display === 'none') {
                sidebar.style.display = 'block';
                content.style.marginLeft = '250px';
            } else {
                sidebar.style.display = 'none';
                content.style.marginLeft = '0';
            }
        }
    </script>
</body>
</html>
