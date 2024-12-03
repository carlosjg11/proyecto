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

require_once('../../tcpdf/tcpdf.php'); // Asegúrate de que TCPDF esté correctamente configurado y accesible

class ReporteAnunciosMasPublicados {

    // Mostrar los autores que han publicado más anuncios
    public function mostrarAutoresMasAnuncios($conn) {
        $sql = "SELECT 
                    a.autor,
                    COUNT(a.id) AS cantidad_anuncios
                FROM 
                    avisos a
                GROUP BY 
                    a.autor
                ORDER BY 
                    cantidad_anuncios DESC";

        $resultado = mysqli_query($conn, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Autor</th>
                            <th>Cantidad de Anuncios Publicados</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo "<tr>
                        <td>" . htmlspecialchars($fila['autor']) . "</td>
                        <td>" . htmlspecialchars($fila['cantidad_anuncios']) . "</td>
                    </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='no-result'>No se encontraron resultados.</p>";
        }
    }

    // Generar el reporte en PDF
    public function generarReportePDF($conn) {
        $sql = "SELECT 
                    a.autor,
                    COUNT(a.id) AS cantidad_anuncios
                FROM 
                    avisos a
                GROUP BY 
                    a.autor
                ORDER BY 
                    cantidad_anuncios DESC";

        $resultado = mysqli_query($conn, $sql);

        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle('Reporte de Anuncios Más Publicados');
        $pdf->SetHeaderData('', 0, 'Reporte de Anuncios Más Publicados', '');
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 10]);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        $html = '<h1 style="text-align:center;">Reporte de Anuncios Más Publicados</h1>';
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">
                        <thead>
                            <tr>
                                <th>Autor</th>
                                <th>Cantidad de Anuncios Publicados</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($fila['autor']) . '</td>
                            <td>' . htmlspecialchars($fila['cantidad_anuncios']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No se encontraron resultados.</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('reporte_anuncios.pdf', 'D');
    }
}

// Crear una instancia de la clase
$reporte = new ReporteAnunciosMasPublicados();

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
    <title>Reporte de Anuncios Más Publicados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #4a148c;
        }

        .container {
            width: 80%;
            margin: 20px auto;
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
            display: flex;
            justify-content: center;
            margin-top: 20px;
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

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #4a148c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #7b1fa2;
        }
    </style>
</head>
<body>
<a href="./verReportes.php" class="back-btn">← Atrás</a>

<div class="container">
    <h1>Reporte de Anuncios Más Publicados</h1>

    <?php $reporte->mostrarAutoresMasAnuncios($conn); ?>

    <div class="download-btn">
        <form method="post">
            <button type="submit" name="descargar_pdf">Descargar PDF</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
// Cerrar la conexión
mysqli_close($conn);
?>
