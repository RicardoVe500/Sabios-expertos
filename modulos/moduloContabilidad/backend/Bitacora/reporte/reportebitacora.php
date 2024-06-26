<?php
require_once("../../../../../lib/fpdf/fpdf.php");
include("../../../../../lib/config/conect.php");

$fechaInicio = mysqli_real_escape_string($con, $_POST['fechadesde']);
$fechaFin = mysqli_real_escape_string($con, $_POST['fechahasta']);

$sql = "SELECT fecha, detalle FROM bitacora WHERE fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($con));
}

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);


class PDF extends FPDF {
    function Header() {
        $this->Image('../../../../../lib/img/images.png', 10, 3, 30);
        $this->SetFont('Arial', 'B', 11);
        $this->SetY(10);
        $this->Cell(190, 5, 'SABIOS Y EXPERTOS', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(190, 5, 'Unidad de Contabilidad', 0, 1, 'C');
        $this->Cell(190, 5, 'Reporte de Bitacora', 0, 1, 'C');
        $this->Cell(190, 5, 'Fecha de Impresion: ' . date('d-m-Y H:i:s'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    function PrintJSON($json) {
        $data = json_decode($json, true);
        return $this->FormatArray($data);
    }
    
    function FormatArray($arr, $indent = '') {
        $formattedText = '';
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                if (is_array($value)) {
                    $formattedText .= $indent . ucfirst($key) . ":\n";
                    $formattedText .= $this->FormatArray($value, $indent . '  ');
                } else {
                    $formattedText .= $indent . ucfirst($key) . ': ' . $value . "\n";
                }
            }
        } else {
            $formattedText = $indent . 'Información no disponible' . "\n";
        }
        return $formattedText;
    }
    
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Headers
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Cell(150, 10, 'Detalle', 1);
$pdf->Ln();

// Data loading
foreach ($data as $row) {
    $pdf->Cell(40, 10, $row['fecha'], 1, 0);
    $detailText = $pdf->PrintJSON($row['detalle']); 
    $pdf->MultiCell(150, 10, $detailText, 1);
}

$pdf->Output();
?>
