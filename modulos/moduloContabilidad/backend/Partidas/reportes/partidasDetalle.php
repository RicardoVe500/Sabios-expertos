<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php");

// Obtención de parámetros GET
$partidaId = $_GET["partidaId"];
$codigoPartida = $_GET["codigoPartida"];

// Consulta a la base de datos
$query = "SELECT pd.partidaDetalleId, p.partidaId, p.codigoPartida, tc.nombreComprobante,
cc.numeroCuenta, cc.nombreCuenta, p.debe, p.haber, pd.cargo, pd.abono, pd.saldo,
pd.numeroComprobante, pd.fechaComprobante, pd.concepto, p.fechacontable
FROM partidaDetalle pd
LEFT JOIN partidas p ON pd.partidaId = p.partidaId
LEFT JOIN catalogocuentas cc ON pd.cuentaId = cc.cuentaId
LEFT JOIN tipoComprobante tc ON pd.tipoComprobanteId = tc.tipoComprobanteId
WHERE p.partidaId = $partidaId AND p.codigoPartida = '$codigoPartida'
ORDER BY CASE WHEN pd.cargo > 0 THEN 0 ELSE 1 END, pd.cargo DESC";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($con));
}

// Obtener la fecha contable una sola vez
$row = mysqli_fetch_assoc($result); // Asumimos que todas las entradas tienen la misma fecha contable
$fechaContable = $row['fechacontable'];
mysqli_data_seek($result, 0); // Reiniciar el puntero de resultados para el bucle while

class PDF extends FPDF {
    protected $codigoPartida;  // Variable para almacenar el código de partida

    function __construct($codigoPartida = '') {
        parent::__construct();
        $this->codigoPartida = $codigoPartida;
    }

    function Header() {
        $this->Image('../../../../../lib/img/images.png', 10, 3, 30);
        $this->SetFont('Arial', 'B', 11);
        $this->SetY(10);
        $this->Cell(190, 5, 'SABIOS Y EXPERTOS', 0, 1, 'C');
        $this->Cell(190, 5, 'Partida de Libro Diarios', 0, 1, 'C');
        $this->Cell(190, 5, utf8_decode('Código de partida: '). $this->codigoPartida, 0, 1, 'C');
        $this->Cell(190, 5, 'Fecha Del Proceso: ' . $GLOBALS['fechaContable'], 0, 1, 'C');
        $this->Cell(190, 5, '(Expresado en Dolares de los Estados Unidos de America)', 0, 1, 'C');

        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function AddTableHeaders() {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 6, 'No. Cuenta', 0, 0, 'C');
        $this->Cell(49, 6, 'Nombre Cuenta', 0, 0, 'C');
        $this->Cell(70, 6, 'Concepto', 0, 0, 'C');
        $this->Cell(20, 6, 'Cargo', 0, 0, 'C');
        $this->Cell(32, 6, 'Abono', 0, 0, 'C');
        $this->Ln();
    }
}

// Inicialización del PDF
$pdf = new PDF($codigoPartida);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AddTableHeaders();
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());



$pdf->SetFont('Arial', '', 10);
$debe = 0;
$haber = 0;
$debeHaberSet = false;

while ($row = mysqli_fetch_assoc($result)) {
    if (!$debeHaberSet) {
        $debe = $row['debe'];
        $haber = $row['haber'];
        $debeHaberSet = true;
    }
    $pdf->Cell(30, 6, $row['numeroCuenta'], 0); 
    $pdf->Cell(65, 6, $row['nombreCuenta'], 0);
    $pdf->Cell(40, 6, $row['concepto'], 0);
    $pdf->Cell(30, 6, floatval($row['cargo']) == 0.00 ? '-' : $row['cargo'], 0, 0, 'C');
    $pdf->Cell(20, 6, floatval($row['abono']) == 0.00 ? '-' : $row['abono'], 0, 0, 'C');
    $pdf->Ln(); 
}

// Footer de totales
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(155); 
$pdf->Cell(10, 6, $debe, 0, 0, 'C');
$pdf->Cell(40, 6, $haber, 0, 0, 'C');
$pdf->Ln(40);

// Sección de firmas
$anchoFirma = 195 / 3;
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($anchoFirma, 6, '', 'B', 0, 'C');
$pdf->Cell($anchoFirma, 6, '', 'B', 0, 'C');
$pdf->Cell($anchoFirma, 6, '', 'B', 1, 'C');
$pdf->Cell($anchoFirma, 6, 'Firma Contador', 0, 0, 'C');
$pdf->Cell($anchoFirma, 6, 'Firma Supervisor', 0, 0, 'C');
$pdf->Cell($anchoFirma, 6, 'Firma Director', 0, 1, 'C');

$pdf->Output('I', 'ReportePartida.pdf'); // Enviar el PDF al navegador
?>
