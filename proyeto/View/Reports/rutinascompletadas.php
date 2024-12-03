<?php
session_start();
include('../../Controller/conexionn.php');
require_once('../../tcpdf/tcpdf.php');

class RutinasCompletadas {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerEstadoRutinas($filtro = '') {
        $where = '';
        if ($filtro === 'completadas') {
            $where = "HAVING ejercicios_completados = total_ejercicios";
        } elseif ($filtro === 'no_completadas') {
            $where = "HAVING ejercicios_completados < total_ejercicios";
        }

        $sql = "
            SELECT 
                l.matricula, 
                CONCAT(l.nombres, ' ', l.apellidopaterno, ' ', l.apellidomaterno) AS nombre_completo, 
                r.nombre AS rutina, 
                COUNT(re.ejercicio_nombre) AS total_ejercicios,
                SUM(re.completado) AS ejercicios_completados,
                CONCAT(c.nombres, ' ', c.apellidopaterno, ' ', c.apellidomaterno) AS creador_nombre
            FROM rutinas r
            INNER JOIN login l ON r.matricula = l.matricula
            LEFT JOIN rutina_ejercicios re ON r.nombre = re.rutina_nombre
            INNER JOIN login c ON r.creador = c.matricula
            GROUP BY r.nombre, l.matricula, l.nombres, l.apellidopaterno, l.apellidomaterno, c.nombres, c.apellidopaterno, c.apellidomaterno
            $where
            ORDER BY l.matricula, r.nombre;
        ";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function generarReportePDF($estadoRutinas) {
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Reporte de Rutinas Completadas');
        $pdf->SetHeaderData('', 0, 'Reporte de Rutinas Completadas', '', [0, 64, 255], [0, 64, 128]);
        $pdf->setHeaderFont(['helvetica', '', 10]);
        $pdf->setFooterFont(['helvetica', '', 8]);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        $html = '<h1>Reporte de Rutinas Completadas</h1>';
        $html .= '<table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre del Alumno</th>
                    <th>Rutina</th>
                    <th>Ejercicios Completados</th>
                    <th>Total de Ejercicios</th>
                    <th>Creador</th>
                    <th>Estado</th>
                    <th>Porcentaje Completado</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($estadoRutinas as $estado) {
            $estadoTexto = ($estado['total_ejercicios'] > 0 && $estado['ejercicios_completados'] == $estado['total_ejercicios']) 
                ? '<span style="color:green;font-weight:bold;">Completado</span>' 
                : '<span style="color:red;font-weight:bold;">Pendiente</span>';

            // Cálculo del porcentaje de ejercicios completados
            $porcentaje = $estado['total_ejercicios'] > 0 
                ? round(($estado['ejercicios_completados'] / $estado['total_ejercicios']) * 100, 2) 
                : 0;

            $html .= '<tr>
                <td>' . htmlspecialchars($estado['matricula']) . '</td>
                <td>' . htmlspecialchars($estado['nombre_completo']) . '</td>
                <td>' . htmlspecialchars($estado['rutina']) . '</td>
                <td>' . htmlspecialchars($estado['ejercicios_completados']) . '</td>
                <td>' . htmlspecialchars($estado['total_ejercicios']) . '</td>
                <td>' . htmlspecialchars($estado['creador_nombre']) . '</td>
                <td>' . $estadoTexto . '</td>
                <td>' . $porcentaje . '%</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        ob_end_clean();
        $pdf->Output('reporte_rutinas_completadas.pdf', 'D');
    }
}

$rutinasCompletadas = new RutinasCompletadas($conn);

$filtro = isset($_POST['filtro']) ? $_POST['filtro'] : '';
$estadoRutinas = $rutinasCompletadas->obtenerEstadoRutinas($filtro);

if (isset($_POST['descargar_pdf'])) {
    $rutinasCompletadas->generarReportePDF($estadoRutinas);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Rutinas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #6A0DAD;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #6A0DAD;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .completado {
            color: green;
            font-weight: bold;
        }

        .pendiente {
            color: red;
            font-weight: bold;
        }

        .download-btn {
            background-color: #6A0DAD;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            display: inline-block;
            text-align: center;
        }

        .download-btn:hover {
            background-color: #5A0CAB;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #6A0DAD;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #5A0CAB;
        }
    </style>
</head>
<body>
    <a href="./verReportes.php" class="back-btn">← Atrás</a>
    <h1>Reporte de Rutinas Completadas</h1>
    <div class="container">
        <form method="post">
            <label for="filtro">Filtrar por:</label>
            <select name="filtro" id="filtro">
                <option value="">Todas</option>
                <option value="completadas" <?= $filtro === 'completadas' ? 'selected' : '' ?>>Completadas</option>
                <option value="no_completadas" <?= $filtro === 'no_completadas' ? 'selected' : '' ?>>No Completadas</option>
            </select>
            <button type="submit" name="filtrar" class="download-btn">Filtrar</button>
            <button type="submit" name="descargar_pdf" class="download-btn">Descargar PDF</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre del Alumno</th>
                    <th>Rutina</th>
                    <th>Ejercicios Completados</th>
                    <th>Total de Ejercicios</th>
                    <th>Creador</th>
                    <th>Estado</th>
                    <th>Porcentaje Completado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($estadoRutinas)): ?>
                    <?php foreach ($estadoRutinas as $estado): ?>
                        <tr>
                            <td><?= htmlspecialchars($estado['matricula']) ?></td>
                            <td><?= htmlspecialchars($estado['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($estado['rutina']) ?></td>
                            <td><?= htmlspecialchars($estado['ejercicios_completados']) ?></td>
                            <td><?= htmlspecialchars($estado['total_ejercicios']) ?></td>
                            <td><?= htmlspecialchars($estado['creador_nombre']) ?></td>
                            <td>
                                <?= $estado['total_ejercicios'] > 0 && $estado['ejercicios_completados'] == $estado['total_ejercicios'] ? '<span class="completado">Completado</span>' : '<span class="pendiente">Pendiente</span>' ?>
                            </td>
                            <td>
                                <?= $estado['total_ejercicios'] > 0 
                                    ? round(($estado['ejercicios_completados'] / $estado['total_ejercicios']) * 100, 2) 
                                    : 0 
                                ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No hay datos disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
