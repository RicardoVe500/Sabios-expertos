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
        $this->Cell(190, 10, 'SABIOS Y EXPERTOS', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(190, 10, 'Reporte de Bitacora del Sistema', 0, 1, 'C');
        $this->Cell(190, 10, 'Reporte de Bitacora desde: ' . $_POST['fechadesde'] . ' hasta: ' . $_POST['fechahasta'], 0, 1, 'C');
        $date = date('d-m-Y H:i:s');
        $this->Ln(5);
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
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $formattedText .= $indent . ucfirst($key) . ":\n";
                $formattedText .= $this->FormatArray($value, $indent . '  ');
            } else {
                $formattedText .= $indent . ucfirst($key) . ': ' . $value . "\n";
            }
        }
        return $formattedText;
    }
    
    function ImprovedTable($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(220, 220, 220);
        $this->SetTextColor(0);
        $this->SetDrawColor(50, 50, 100);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        foreach($header as $col) {
            $this->Cell($col[1], 7, $col[0], 1, 0, 'C', true);
        }
        $this->Ln();

        // Data
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(0);
        $this->SetFont('');
        $fill = false;

        foreach($data as $row) {
            $this->Cell(40, 10, $row['fecha'], 'LR', 0, 'C', $fill);
            $detailText = $this->PrintJSON($row['detalle']); 
            $this->MultiCell(150, 10, $detailText, 'LR', 'L', $fill);
            $fill = !$fill;
            $this->Ln();
        }
        $this->Cell(190, 0, '', 'T');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Prepare headers and data
$header = [['Fecha', 40], ['Detalle', 150]];
$pdf->ImprovedTable($header, $data);

$pdf->Output();
?>
