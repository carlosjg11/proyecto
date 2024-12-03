<?php
// Incluir la librería TCPDF (asegúrate de tener TCPDF instalada)
require_once('../../tcpdf/tcpdf.php');

// Datos de conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "proyecto";
$port = 3306; // Cambia si es necesario

// Establecer conexión a la base de datos
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

class ReporteEntrenadorRutinas {

    // Método para obtener el reporte de rutinas por entrenador
    public function obtenerReporte($conn) {
        $sql = "SELECT 
                    CONCAT(l.nombres, ' ', l.apellidopaterno, ' ', l.apellidomaterno) AS entrenador_nombre,
                    COUNT(r.nombre) AS rutinas_puestas,
                    SUM(CASE WHEN re.completado = 1 THEN 1 ELSE 0 END) AS rutinas_completadas
                FROM 
                    rutinas r
                LEFT JOIN 
                    rutina_ejercicios re ON r.nombre = re.rutina_nombre
                JOIN 
                    login l ON r.creador = l.matricula
                GROUP BY 
                    r.creador
                ORDER BY 
                    rutinas_puestas DESC"; 

        // Ejecutar la consulta
        $resultado = mysqli_query($conn, $sql);

        return $resultado;
    }

    // Método para generar el PDF
    public function generarPDF($conn) {
        // Obtener los datos
        $resultado = $this->obtenerReporte($conn);
        
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            // Crear una instancia de TCPDF
            $pdf = new TCPDF();
            $pdf->SetFont('helvetica', '', 12);
            $pdf->AddPage();
            
            // Título del documento
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Reporte de Rutinas por Entrenador', 0, 1, 'C');
            
            // Tabla
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Ln(10); // Salto de línea

            // Cabecera de la tabla
            $pdf->Cell(60, 10, 'Nombre del Entrenador', 1, 0, 'C');
            $pdf->Cell(60, 10, 'Ejercicios Puestos', 1, 0, 'C');
            $pdf->Cell(60, 10, 'Ejercicios Completados', 1, 1, 'C');
            
            // Datos de la tabla
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $pdf->Cell(60, 10, $fila['entrenador_nombre'], 1, 0, 'C');
                $pdf->Cell(60, 10, $fila['rutinas_puestas'], 1, 0, 'C');
                $pdf->Cell(60, 10, $fila['rutinas_completadas'], 1, 1, 'C');
            }
            
            // Cerrar el PDF y descargarlo
            $pdf->Output('reporte_rutinas.pdf', 'D'); // 'D' para descargar directamente
        } else {
            echo "No hay datos disponibles.";
        }
    }

    // Método para mostrar el reporte de rutinas por entrenador en la página
    public function mostrarEntrenadorRutinas($conn) {
        $sql = "SELECT 
                    CONCAT(l.nombres, ' ', l.apellidopaterno, ' ', l.apellidomaterno) AS entrenador_nombre,
                    COUNT(r.nombre) AS rutinas_puestas,
                    SUM(CASE WHEN re.completado = 1 THEN 1 ELSE 0 END) AS rutinas_completadas
                FROM 
                    rutinas r
                LEFT JOIN 
                    rutina_ejercicios re ON r.nombre = re.rutina_nombre
                JOIN 
                    login l ON r.creador = l.matricula
                GROUP BY 
                    r.creador
                ORDER BY 
                    rutinas_puestas DESC"; // Ordenar por rutinas puestas en orden descendente

        // Ejecutar la consulta
        $resultado = mysqli_query($conn, $sql);

        // Si hay resultados, mostrar la tabla
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            // Crear la tabla HTML
            echo "<div class='table-container'>";
            echo "<table class='styled-table'>
                    <thead>
                        <tr>
                            <th>Nombre del Entrenador</th>
                            <th>Ejercicios Puestos</th>
                            <th>Ejercicios Completados</th>
                        </tr>
                    </thead>
                    <tbody>";

            // Mostrar los resultados en filas
            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo "<tr>
                        <td>" . htmlspecialchars($fila['entrenador_nombre']) . "</td>
                        <td>" . htmlspecialchars($fila['rutinas_puestas']) . "</td>
                        <td>" . htmlspecialchars($fila['rutinas_completadas'] ?? 0) . "</td>
                    </tr>";
            }

            // Cerrar la tabla
            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<p class='no-data'>No se encontraron rutinas registradas para los entrenadores.</p>";
        }
    }
}

// Crear una instancia de la clase ReporteEntrenadorRutinas
$reporte = new ReporteEntrenadorRutinas();

// Si se hace clic en el botón para generar el PDF
if (isset($_POST['descargar_pdf'])) {
    $reporte->generarPDF($conn);
    exit(); // Detener la ejecución después de generar el PDF
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rutinas por Entrenador</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #4a148c;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-btn {
            display: inline-block;
            margin: 20px 0;
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

        /* Estilos para la tabla */
        .table-container {
            overflow-x: auto;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .styled-table th {
            background-color: #4a148c;
            color: white;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:hover {
            background-color: #e1bee7;
        }

        /* Botón de impresión */
        .print-btn {
            display: block;
            width: fit-content;
            margin: 10px auto 20px auto;
            padding: 10px 20px;
            background-color: #4a148c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .print-btn:hover {
            background-color: #7b1fa2;
        }

        /* Mensaje sin datos */
        .no-data {
            text-align: center;
            color: #999;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="./verReportes.php" class="back-btn">← Atrás</a> <!-- Botón de Atrás -->
    <h1>Reporte de Ejercicios puestos completados</h1>
    
    <!-- Formulario para descargar el PDF -->
    <form method="POST" action="">
        <button type="submit" name="descargar_pdf" class="print-btn">Descargar PDF</button>
    </form>

    <!-- Mostrar el reporte -->
    <?php $reporte->mostrarEntrenadorRutinas($conn); ?>
</div>

</body>
</html>

<?php
// Cerrar la conexión
mysqli_close($conn);
?>
