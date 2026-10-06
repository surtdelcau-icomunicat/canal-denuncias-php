<?php

require __DIR__ . '/fpdf/fpdf.php';
require __DIR__ . '/config.php'; // Conexión a la base de datos

function pdf_text(string $value): string { return (string) iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $value); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recogemos todos los datos del formulario
    $lugar_incidente = $_POST['lugar_incidente'];
    $relacion = $_POST['relacion'];
    $tipo_denuncia = $_POST['tipo_denuncia'];
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $puesto_trabajo = $_POST['puesto_trabajo'] ?? '';
    $razon_anonimato = $_POST['razon_anonimato'] ?? '';
    $tipo_delito = $_POST['tipo_delito'];
    $otros_delito = $_POST['otros_delito'] ?? '';
    $descripcion = $_POST['descripcion'];
    $fecha_hechos = $_POST['fecha_hechos'];
    $afectados = $_POST['afectados'];
    $identidad_conocida = $_POST['identidad_conocida'] ?? '';
    $nombre_cargo = $_POST['nombre_cargo'] ?? '';
    $documento_acreditativo = $_POST['documento_acreditativo'] ?? '';
    $descripcion_documento = $_POST['descripcion_documento'] ?? '';

    // Insertar los datos en la base de datos y obtener el ID de la denuncia
// Generar el timestamp como identificador
$denuncia_id = time(); // Formato: AAAAMMDDHHMMSS, o usa `time()` para un timestamp numérico

// Modificar la consulta para insertar el timestamp como ID
$stmt = $conn->prepare("INSERT INTO tickets (id, lugar_incidente, relacion, tipo_denuncia, nombre, apellidos, telefono, email, puesto_trabajo, razon_anonimato, tipo_delito, otros_delito, descripcion, fecha_hechos, afectados, identidad_conocida, nombre_cargo, documento_acreditativo, descripcion_documento, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


if ($stmt) {
    $fecha_creacion = date('Y-m-d H:i:s');
    $stmt->bind_param("ssssssssssssssssssss", $denuncia_id, $lugar_incidente, $relacion, $tipo_denuncia, $nombre, $apellidos, $telefono, $email, $puesto_trabajo, $razon_anonimato, $tipo_delito, $otros_delito, $descripcion, $fecha_hechos, $afectados, $identidad_conocida, $nombre_cargo, $documento_acreditativo, $descripcion_documento, $fecha_creacion);
    $stmt->execute();
    $stmt->close();
} else {
    die("Error al preparar la consulta: " . $conn->error);
}

    // Generar PDF con FPDF
    if (!empty($settings['mail_enabled'])) {
    try {
    $pdf = new FPDF();
    $pdf->AddPage();

    // Configurar márgenes
    $pdf->SetMargins(15, 20, 15); // Márgenes: Izquierda, Arriba, Derecha
    $pdf->SetAutoPageBreak(true, 20); // Espacio inferior automático
    

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Detalles de la denuncia', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);
    
    $pdf->Cell(0, 10, "ID de la denuncia: $denuncia_id", 0, 1);
    $pdf->Cell(0, 10, pdf_text("Fecha de Creación: $fecha_creacion"), 0, 1);
    $pdf->Cell(0, 10, pdf_text("Empresa a la que se dirige: $lugar_incidente"), 0, 1);
    $pdf->Cell(0, 10, pdf_text("Relacion con la empresa: $relacion"), 0, 1);
    $pdf->Cell(0, 10, pdf_text("Tipo de denuncia: $tipo_denuncia"), 0, 1);
    if ($tipo_denuncia === 'confidencial') {
        $pdf->Cell(0, 10, pdf_text("Nombre: $nombre"), 0, 1);
        $pdf->Cell(0, 10, pdf_text("Apellidos: $apellidos"), 0, 1);
        $pdf->Cell(0, 10, pdf_text("Telefono: $telefono"), 0, 1);
        $pdf->Cell(0, 10, pdf_text("Email: $email"), 0, 1);
        $pdf->Cell(0, 10, pdf_text("Puesto de trabajo: $puesto_trabajo"), 0, 1);
    } else {
        $pdf->Cell(0, 10, pdf_text("Razon para mantener el anonimato: $razon_anonimato"), 0, 1);
    }
    $pdf->Cell(0, 10, pdf_text("Tipo de delito: $tipo_delito"), 0, 1);
    if ($tipo_delito === 'otros') {
        $pdf->Cell(0, 10, pdf_text("Otros (Especificar): $otros_delito"), 0, 1);
    }
    $pdf->MultiCell(0, 10, pdf_text("Descripción: $descripcion"), 0, 'L');

    $pdf->MultiCell(180, 12, pdf_text("Fecha de los hechos: $fecha_hechos"), 0, "J");
    $pdf->Ln(5); // Salto de línea

    $pdf->MultiCell(0, 10, pdf_text("Afectados: $afectados"), 0, 'L');

    if ($identidad_conocida === 'si') {
        $pdf->MultiCell(180, 12, pdf_text("Nombre y cargo de las personas comprometidas: $nombre_cargo"), 0, 'J');
        $pdf->Ln(5); // Salto de línea
        
    }
    if ($documento_acreditativo === 'si') {
        $pdf->Cell(0, 10, pdf_text("Descripcion del documento acreditativo: $descripcion_documento"), 0, 1);
    }

    $pdfBytes = $pdf->Output('S');
    $pdfName = 'denuncia_' . $denuncia_id . '.pdf';

    // Enviar email a los administradores sin usar PHPMailer
    $to = [];
    $sql = "SELECT email FROM admins";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $to[] = $row['email'];
        }
    }

    $subject = 'Nuevo Ticket Registrado';
    $message = 'Se ha registrado una nueva denuncia. Se adjunta un PDF con los detalles.';
    $from = $settings['mail_from'] ?? '';
    if (!filter_var($from, FILTER_VALIDATE_EMAIL) || strpbrk($from, "\r\n") !== false) {
        throw new RuntimeException('Invalid sender');
    }
    $headers = "From: Sistema de Denuncias <" . $from . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

    $attachment = chunk_split(base64_encode($pdfBytes));

    $body = "--boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= "$message\r\n";
    $body .= "--boundary\r\n";
    $body .= "Content-Type: application/pdf; name=\"" . $pdfName . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . $pdfName . "\"\r\n\r\n";
    $body .= "$attachment\r\n";
    $body .= "--boundary--";

    foreach ($to as $email_address) {
        if (!mail($email_address, $subject, $body, $headers)) {
            throw new RuntimeException('Mail transport failed');
        }
    }

    // Redirigir a la página de agradecimiento con el ID del ticket
    } catch (Throwable $error) {
        error_log('Canal: notification failed; review mail transport and PDF configuration.');
    }
    }
    header('Location: thanks.php?id=' . urlencode($denuncia_id));
    exit();


}
?>