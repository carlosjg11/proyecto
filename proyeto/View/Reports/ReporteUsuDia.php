<?php
// Datos de conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "proyecto";
$port = 3306;

// Establecer conexión
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

require_once('../../tcpdf/tcpdf.php'); // Ruta a TCPDF

class ReporteUsuariosPorDia {

    public function obtenerUsuariosPorDia($conn, $mes = null, $anio = null) {
        // Construcción dinámica de la consulta
        $condiciones = [];
        if (!empty($mes)) {
            $condiciones[] = "MONTH(fechaHoraRegistro) = " . intval($mes);
        }
        if (!empty($anio)) {
            $condiciones[] = "YEAR(fechaHoraRegistro) = " . intval($anio);
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
        $sql = "SELECT 
                    DATE(fechaHoraRegistro) AS fecha_registro,
                    COUNT(matricula) AS total_usuarios
                FROM 
                    login
                $where
                GROUP BY 
                    DATE(fechaHoraRegistro)
                ORDER BY 
                    fecha_registro DESC";

        $resultado = mysqli_query($conn, $sql);

        return $resultado && mysqli_num_rows($resultado) > 0 ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function generarPDF($datos, $mes, $anio) {
        // Crear instancia de TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle('Reporte de Usuarios por Día');
        $pdf->SetHeaderData('', 0, 'Reporte de Usuarios Registrados por Día', "Mes: $mes, Año: $anio");
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 10]);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        // Contenido del PDF
        $html = '<h1 style="text-align:center;">Reporte de Usuarios Registrados por Día</h1>';
        if (!empty($datos)) {
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total de Usuarios Registrados</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($datos as $fila) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($fila['fecha_registro']) . '</td>
                            <td>' . htmlspecialchars($fila['total_usuarios']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No se encontraron registros para el filtro seleccionado.</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('reporte_usuarios_dia.pdf', 'D');
    }
}

// Crear una instancia de la clase
$reporte = new ReporteUsuariosPorDia();

// Capturar valores del filtro
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;
$anio = isset($_POST['anio']) ? $_POST['anio'] : null;

// Obtener los datos
$datos = $reporte->obtenerUsuariosPorDia($conn, $mes, $anio);

// Descargar PDF si se solicita
if (isset($_POST['descargar_pdf'])) {
    $reporte->generarPDF($datos, $mes, $anio);
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Usuarios por Día</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #6A0DAD;
            margin-top: 20px;
        }

        .container {
            width: 50%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-form {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .filter-form select, .filter-form button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        .filter-form button {
            background-color: #6A0DAD;
            color: white;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #5A0CAB;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #6A0DAD;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #5A0CAB;
        }

        .table-container {
            overflow-x: auto;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }

        .styled-table th, .styled-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .styled-table th {
            background-color: #6A0DAD;
            color: white;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #F3E5F5;
        }

        .styled-table tbody tr:hover {
            background-color: #D1C4E9;
        }

        .no-data {
            text-align: center;
            font-size: 18px;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="./verReportes.php" class="back-btn">← Atrás</a>
    <h1>Reporte de Usuarios Registrados por Día</h1>

    <!-- Formulario de filtro -->
    <form method="post" class="filter-form">
        <select name="mes">
            <option value="">Seleccionar Mes</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo ($mes == $i) ? 'selected' : ''; ?>>
                    <?php echo date("F", mktime(0, 0, 0, $i, 10)); ?>
                </option>
            <?php endfor; ?>
        </select>
        <select name="anio">
            <option value="">Seleccionar Año</option>
            <?php for ($i = date("Y"); $i >= 2000; $i--): ?>
                <option value="<?php echo $i; ?>" <?php echo ($anio == $i) ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>
        <button type="submit">Filtrar</button>
        <button type="submit" name="descargar_pdf">Descargar PDF</button>
    </form>

    <!-- Mostrar la tabla -->
    <?php if (!empty($datos)): ?>
        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total de Usuarios Registrados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['fecha_registro']); ?></td>
                            <td><?php echo htmlspecialchars($fila['total_usuarios']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-data">No se encontraron registros.</p>
    <?php endif; ?>
</div>

</body>
</html>
