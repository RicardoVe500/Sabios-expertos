<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php");
require_once("../../../../../lib/fpdf/mc_table.php"); 

class PDF extends PDF_MC_Table {
    // Cabecera de página
    function Header() {
        // Imagen de encabezado
        $this->Image('../../../../../lib/img/images.png', 10, 7, 30);
        $this->SetFont('Arial','B',12);
        // Movernos a la derecha para centrar el título
        $this->Cell(80);
        // Título
        $this->Cell(30,10,'SABIOS Y EXPERTOS',0,0,'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(30,10,'Departamento de contabilidad',0,0,'C');
        $this->Ln(5);
        $this->Cell(80);
        $this->Cell(30,10,'Partidas',0,0,'C');
        $this->Ln(5);
        $this->Cell(80);
        $date = date('d-m-Y H:i:s');
        $this->Cell(30,10,'Fecha de impresion: ' . $date,0,0,'C');
        $this->Ln(15);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Creación del documento PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$query = "SELECT 
  p.codigoPartida,
  cc.nombreCuenta,
  cc.numeroCuenta,
  pd.partidaDetalleId,
  pd.cargo,
  pd.abono,
  pd.fechaComprobante,
  pd.concepto AS detalleConcepto,
  e.estado AS estadoPartida
FROM partidas p
JOIN partidaDetalle pd ON p.partidaId = pd.partidaId
LEFT JOIN estado e ON p.estadoId = e.estadoId
LEFT JOIN catalogocuentas cc ON pd.cuentaId = cc.cuentaId
ORDER BY p.codigoPartida, pd.partidaDetalleId";

$result = mysqli_query($con, $query);

$currentCodigoPartida = '';
while ($row = $result->fetch_assoc()) {
    if ($currentCodigoPartida != $row['codigoPartida']) {
        if ($currentCodigoPartida != '') $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Código de Partida: " . $row['codigoPartida'] . " | Estado: " . $row['estadoPartida'], 0, 1);
        $currentCodigoPartida = $row['codigoPartida'];
        $pdf->SetFont('Arial', '', 10);
        // Configurar las columnas para la tabla
        $pdf->SetWidths(array(25, 40, 25, 50, 25, 25));
        $pdf->Row(array('Numero Cuenta', 'Nombre Cuenta', 'Fecha Comprobante', 'Concepto', 'Cargo', 'Abono'));
    }
    // Imprimir detalles de cada movimiento
    $pdf->Row(array(
        $row['numeroCuenta'],
        $row['nombreCuenta'],
        $row['fechaComprobante'],
        $row['detalleConcepto'],
        number_format($row['cargo'], 2),
        number_format($row['abono'], 2)
    ));
}

$pdf->Output();
?>
