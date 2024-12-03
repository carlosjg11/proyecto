<?php
session_start();
include('../../Controller/conexionn.php');
require_once('../../tcpdf/tcpdf.php');

class ReporteUsuarios {
    public function obtenerUsuariosFiltrados($conn, $rol = null, $sexo = null, $carrera = null) {
        $filtros = [];
        if (!empty($rol)) {
            $filtros[] = "rol = '" . mysqli_real_escape_string($conn, $rol) . "'";
        }
        if (!empty($sexo)) {
            $filtros[] = "sexo = '" . mysqli_real_escape_string($conn, $sexo) . "'";
        }
        if (!empty($carrera)) {
            $filtros[] = "carrera = '" . mysqli_real_escape_string($conn, $carrera) . "'";
        }

        $sql = "SELECT * FROM login";
        if (!empty($filtros)) {
            $sql .= " WHERE " . implode(" AND ", $filtros);
        }

        $resultado = mysqli_query($conn, $sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerCantidadUsuarios($conn, $rol = null, $sexo = null, $carrera = null) {
        $filtros = [];
        if (!empty($rol)) {
            $filtros[] = "rol = '" . mysqli_real_escape_string($conn, $rol) . "'";
        }
        if (!empty($sexo)) {
            $filtros[] = "sexo = '" . mysqli_real_escape_string($conn, $sexo) . "'";
        }
        if (!empty($carrera)) {
            $filtros[] = "carrera = '" . mysqli_real_escape_string($conn, $carrera) . "'";
        }

        $sql = "SELECT COUNT(*) AS cantidad_usuarios FROM login";
        if (!empty($filtros)) {
            $sql .= " WHERE " . implode(" AND ", $filtros);
        }

        $resultado = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($resultado);
        return $row['cantidad_usuarios'];
    }

    public function generarPDF($usuarios, $rol, $sexo, $carrera) {
        ob_clean(); // Limpiar cualquier salida previa
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle('Listado de Usuarios');
        $pdf->SetHeaderData('', 0, 'Reporte de Usuarios', '');
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 10]);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        $html = '<h1 style="text-align:center;">Reporte de Usuarios</h1>';
        
        // Mostrar filtros aplicados
        $html .= '<p><strong>Filtros aplicados:</strong></p>';
        $html .= '<ul>';
        if (!empty($rol)) {
            $html .= '<li><strong>Rol:</strong> ' . htmlspecialchars($rol) . '</li>';
        } else {
            $html .= '<li><strong>Rol:</strong> Todos</li>';
        }
        if (!empty($sexo)) {
            $html .= '<li><strong>Sexo:</strong> ' . htmlspecialchars($sexo) . '</li>';
        } else {
            $html .= '<li><strong>Sexo:</strong> Todos</li>';
        }
        if (!empty($carrera)) {
            $html .= '<li><strong>Carrera:</strong> ' . htmlspecialchars($carrera) . '</li>';
        } else {
            $html .= '<li><strong>Carrera:</strong> Todas</li>';
        }
        $html .= '</ul>';

        // Mostrar cantidad de usuarios encontrados
        $html .= '<p><strong>Cantidad de usuarios encontrados: </strong>' . count($usuarios) . '</p>';

        if (!empty($usuarios)) {
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">
                        <thead>
                            <tr>
                                <th>Apellido Paterno</th>
                                <th>Apellido Materno</th>
                                <th>Nombres</th>
                                <th>Matrícula</th>
                                <th>Rol</th>
                                <th>Sexo</th>
                                <th>Fecha Nacimiento</th>
                                <th>Carrera</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($usuarios as $usuario) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($usuario['apellidopaterno']) . '</td>
                            <td>' . htmlspecialchars($usuario['apellidomaterno']) . '</td>
                            <td>' . htmlspecialchars($usuario['nombres']) . '</td>
                            <td>' . htmlspecialchars($usuario['matricula']) . '</td>
                            <td>' . htmlspecialchars($usuario['rol']) . '</td>
                            <td>' . htmlspecialchars($usuario['sexo']) . '</td>
                            <td>' . htmlspecialchars($usuario['fechanacimiento']) . '</td>
                            <td>' . htmlspecialchars($usuario['carrera']) . '</td>
                            <td>' . htmlspecialchars($usuario['fechaHoraRegistro']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No se encontraron usuarios con los filtros seleccionados.</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('listado_usuarios.pdf', 'D');
        exit(); // Finaliza la ejecución después de generar el PDF
    }
}

// Obtener datos
$reporte = new ReporteUsuarios();
$rol = isset($_GET['rol']) ? $_GET['rol'] : null;
$sexo = isset($_GET['sexo']) ? $_GET['sexo'] : null;
$carrera = isset($_GET['carrera']) ? $_GET['carrera'] : null;
$usuarios = $reporte->obtenerUsuariosFiltrados($conn, $rol, $sexo, $carrera);

// Contar usuarios con todos los filtros aplicados
$cantidad_usuarios = $reporte->obtenerCantidadUsuarios($conn, $rol, $sexo, $carrera);

if (isset($_POST['descargar_pdf'])) {
    $reporte->generarPDF($usuarios, $rol, $sexo, $carrera);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
        }
        h1 {
            color: #6A0DAD;
            text-align: center;
        }
        .filter-form {
            margin: 20px auto;
            text-align: center;
        }
        table {
            margin: 20px auto;
            width: 90%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #6A0DAD;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #6A0DAD;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #5A0CAB;
            text-decoration: none;
        }
        .download-btn {
            margin-top: 30px;
            text-align: center;
        }
        .filter-btn {
            background-color: #6A0DAD;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-btn:hover {
            background-color: #5A0CAB;
        }
    </style>
</head>
<body>

<a href="./verReportes.php" class="back-btn">← Atrás</a>

<div class="container">
    <h1>Listado de Usuarios</h1>
    <form method="GET" class="filter-form">
        <select name="rol">
            <option value="">Roles: Todos</option>
            <option value="alumno" <?php echo ($rol == 'alumno') ? 'selected' : ''; ?>>Alumno</option>
            <option value="entrenador" <?php echo ($rol == 'entrenador') ? 'selected' : ''; ?>>Entrenador</option>
            <option value="admin" <?php echo ($rol == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
        <select name="sexo">
            <option value="">Sexo: Todos</option>
            <option value="Hombre" <?php echo ($sexo == 'Hombre') ? 'selected' : ''; ?>>Hombre</option>
            <option value="Mujer" <?php echo ($sexo == 'Mujer') ? 'selected' : ''; ?>>Mujer</option>
        </select>

        <select name="carrera">
            <option value="">Carrera: Todas</option>
            <option value="ITI" <?php echo ($carrera == 'ITI') ? 'selected' : ''; ?>>ITI</option>
            <option value="LAE" <?php echo ($carrera == 'LAE') ? 'selected' : ''; ?>>LAE</option>
            <option value="IBT" <?php echo ($carrera == 'IBT') ? 'selected' : ''; ?>>IBT</option>
            <option value="IIN" <?php echo ($carrera == 'IIN') ? 'selected' : ''; ?>>IIN</option>
            <option value="IFI" <?php echo ($carrera == 'IFI') ? 'selected' : ''; ?>>IFI</option>
            <option value="IET" <?php echo ($carrera == 'IET') ? 'selected' : ''; ?>>IET</option>
            <option value="ITA" <?php echo ($carrera == 'ITA') ? 'selected' : ''; ?>>ITA</option>




        </select>
        <button type="submit" class="filter-btn">Filtrar</button>
    </form>

    <div class="download-btn">
        <form method="POST">
            <button type="submit" name="descargar_pdf" class="filter-btn">Descargar PDF</button>
        </form>
    </div>

    <p><strong>Cantidad de usuarios encontrados:</strong> <?php echo $cantidad_usuarios; ?></p>

    <?php if (!empty($usuarios)): ?>
        <table>
            <thead>
                <tr>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Nombres</th>
                    <th>Matrícula</th>
                    <th>Rol</th>
                    <th>Sexo</th>
                    <th>Fecha Nacimiento</th>
                    <th>Carrera</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['apellidopaterno']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['apellidomaterno']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['sexo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['fechanacimiento']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['carrera']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['fechaHoraRegistro']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron usuarios con los filtros seleccionados.</p>
    <?php endif; ?>
</div>

</body>
</html>
