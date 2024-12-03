<?php
ob_start(); // Activa el almacenamiento en búfer de salida

require_once __DIR__ . '/tcpdf/tcpdf.php';
include('conexionn.php');

// Crear una instancia de TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Listado de Usuarios');
$pdf->SetHeaderData('', 0, 'Listado de Usuarios', '');

// Configuración de fuente
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// Contenido del PDF (HTML)
$html = '<h1>Listado de Usuarios</h1>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Nombres</th>
                    <th>Matrícula</th>
                    <th>Contraseña</th>
                    <th>Rol</th>
                    <th>Sexo</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Carrera</th>
                    <th>Fecha y Hora de Registro</th>
                </tr>
            </thead>
            <tbody>';

// Consulta a la base de datos
$sql = "SELECT * FROM login";
$resultado = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($resultado)) {
    $html .= '<tr>
                <td>' . htmlspecialchars($row['apellidopaterno']) . '</td>
                <td>' . htmlspecialchars($row['apellidomaterno']) . '</td>
                <td>' . htmlspecialchars($row['nombres']) . '</td>
                <td>' . htmlspecialchars($row['matricula']) . '</td>
                <td>' . htmlspecialchars($row['pass']) . '</td>
                <td>' . htmlspecialchars($row['rol']) . '</td>
                <td>' . htmlspecialchars($row['sexo']) . '</td>
                <td>' . htmlspecialchars($row['fechanacimiento']) . '</td>
                <td>' . htmlspecialchars($row['carrera']) . '</td>
                <td>' . htmlspecialchars($row['fechaHoraRegistro']) . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// Generar el contenido del PDF
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean(); // Limpia el búfer de salida
$pdf->Output('listado_usuarios.pdf', 'D');
?>
