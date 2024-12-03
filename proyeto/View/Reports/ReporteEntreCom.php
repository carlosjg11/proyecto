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

class ReporteEntrenadorConMasRutinas {

    // Método para mostrar el reporte en pantalla
    public function mostrarEntrenadorConMasRutinas($conn) {
        $sql = "SELECT 
                    l.nombres AS nombre_entrenador, 
                    COUNT(r.nombre) AS total_rutinas
                FROM 
                    rutinas r
                JOIN 
                    login l ON r.creador = l.matricula
                GROUP BY 
                    l.nombres
                ORDER BY 
                    total_rutinas DESC";

        $resultado = mysqli_query($conn, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Nombre del Entrenador</th>
                            <th>Total de Rutinas</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo "<tr>
                        <td>" . htmlspecialchars($fila['nombre_entrenador']) . "</td>
                        <td>" . htmlspecialchars($fila['total_rutinas']) . "</td>
                    </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='no-result'>No se encontraron registros de rutinas creadas.</p>";
        }
    }

    // Método para generar el reporte en PDF
    public function generarReportePDF($conn) {
        require_once('../../tcpdf/tcpdf.php');

        $sql = "SELECT 
                    l.nombres AS nombre_entrenador, 
                    COUNT(r.nombre) AS total_rutinas
                FROM 
                    rutinas r
                JOIN 
                    login l ON r.creador = l.matricula
                GROUP BY 
                    l.nombres
                ORDER BY 
                    total_rutinas DESC";

        $resultado = mysqli_query($conn, $sql);

        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Rutinas');
        $pdf->SetTitle('Reporte de Entrenador con Más Rutinas');
        $pdf->SetHeaderData('', 0, 'Reporte de Entrenador con Más Rutinas', '');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->AddPage();

        $html = '<h1>Reporte de Entrenador con Más Rutinas</h1>';
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $html .= '<table border="1" cellpadding="5">
                        <thead>
                            <tr>
                                <th>Nombre del Entrenador</th>
                                <th>Total de Rutinas</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($fila['nombre_entrenador']) . '</td>
                            <td>' . htmlspecialchars($fila['total_rutinas']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No se encontraron registros de rutinas creadas.</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('reporte_entrenador.pdf', 'D');
    }
}

$reporte = new ReporteEntrenadorConMasRutinas();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descargar_pdf'])) {
    $reporte->generarReportePDF($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Entrenador con Más Rutinas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .back-btn {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: #4a148c;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #7b1fa2;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .container {
            width: 50%;
            margin: 10px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4a148c;
            color: #ffffff;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #e1bee7;
        }

        .no-result {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-top: 20px;
        }

        .download-btn {
            display: block;
            text-align: center;
            margin: 20px 0;
        }

        .download-btn button {
            background-color: #4a148c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .download-btn button:hover {
            background-color: #7b1fa2;
        }
    </style>
</head>
<body>

<a href="./verReportes.php" class="back-btn">← Atrás</a> <!-- Botón de Atrás -->

<div class="container">
    <h1>Reporte de Entrenador con Más Rutinas</h1>

    <?php $reporte->mostrarEntrenadorConMasRutinas($conn); ?>

    <div class="download-btn">
        <form method="post">
            <button type="submit" name="descargar_pdf">Descargar PDF</button>
        </form>
    </div>
</div>

</body>
</html>
