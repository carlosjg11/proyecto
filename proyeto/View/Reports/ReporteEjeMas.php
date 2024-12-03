<?php
session_start();
require_once('../../tcpdf/tcpdf.php');

// Datos de conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "proyecto";
$port = 3306;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

class ReporteEjerciciosUtilizados {
    public function obtenerEjercicios($conn, $tipo = null) {
        $order = "";
        if ($tipo === 'mas_utilizados') {
            $order = "ORDER BY cantidad_usos DESC";
        } elseif ($tipo === 'menos_utilizados') {
            $order = "ORDER BY cantidad_usos ASC";
        }

        $sql = "SELECT 
                    e.nombre AS ejercicio_nombre, 
                    COUNT(re.ejercicio_nombre) AS cantidad_usos
                FROM 
                    rutina_ejercicios re
                JOIN 
                    ejercicios e ON re.ejercicio_nombre = e.nombre
                GROUP BY 
                    e.nombre
                $order";

        $resultado = mysqli_query($conn, $sql);
        return $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
    }

    public function generarPDF($ejercicios, $titulo) {
        ob_clean();
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle($titulo);
        $pdf->SetHeaderData('', 0, $titulo, '');
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 10]);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();

        $html = '<h1 style="text-align:center;">' . $titulo . '</h1>';
        if (!empty($ejercicios)) {
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">
                        <thead>
                            <tr>
                                <th>Ejercicio</th>
                                <th>Cantidad de Usos</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($ejercicios as $ejercicio) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($ejercicio['ejercicio_nombre']) . '</td>
                            <td>' . htmlspecialchars($ejercicio['cantidad_usos']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No se encontraron ejercicios para este reporte.</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($titulo . '.pdf', 'D');
        exit();
    }
}

$reporte = new ReporteEjerciciosUtilizados();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_pdf'])) {
    $tipo = $_POST['tipo_reporte'];
    $titulo = $tipo === 'mas_utilizados' ? 'Ejercicios Más Utilizados' : 'Ejercicios Menos Utilizados';
    $ejercicios = $reporte->obtenerEjercicios($conn, $tipo);
    $reporte->generarPDF($ejercicios, $titulo);
}

$tipo_reporte = isset($_POST['tipo_reporte']) ? $_POST['tipo_reporte'] : null;
$ejercicios = $reporte->obtenerEjercicios($conn, $tipo_reporte);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte: Ejercicios Utilizados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4a148c;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-form select {
            padding: 10px;
            font-size: 16px;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #4a148c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #7b1fa2;
        }

        .table-container {
            display: flex;
            justify-content: center;
        }

        table {
            width: 100%;
            max-width: 600px;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table thead tr {
            background-color: #4a148c;
            color: #ffffff;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #e1bee7;
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
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #7b1fa2;
        }

        p {
            text-align: center;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <a href="./verReportes.php" class="back-btn">← Atrás</a>
    <h1>Reporte de Ejercicios Utilizados</h1>
    <div class="container">
        <form class="filter-form" method="POST">
            <select name="tipo_reporte">
                <option value="">Todos</option>
                <option value="mas_utilizados" <?= isset($tipo_reporte) && $tipo_reporte === 'mas_utilizados' ? 'selected' : '' ?>>Más Utilizados</option>
                <option value="menos_utilizados" <?= isset($tipo_reporte) && $tipo_reporte === 'menos_utilizados' ? 'selected' : '' ?>>Menos Utilizados</option>
            </select>
            <button type="submit" name="filtrar">Filtrar</button>
            <button type="submit" name="generar_pdf">Descargar PDF</button>
        </form>

        <div class="table-container">
            <?php if (!empty($ejercicios)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ejercicio</th>
                            <th>Cantidad de Usos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ejercicios as $ejercicio): ?>
                            <tr>
                                <td><?= htmlspecialchars($ejercicio['ejercicio_nombre']) ?></td>
                                <td><?= htmlspecialchars($ejercicio['cantidad_usos']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron ejercicios para este filtro.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
