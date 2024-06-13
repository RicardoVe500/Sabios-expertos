<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php");

$partidaId = $_GET["partidaId"];
$codigoPartida = $_GET["codigoPartida"];

$query = "SELECT 
pd.partidaDetalleId,
p.partidaId,
p.codigoPartida,
tc.nombreComprobante,
cc.numeroCuenta, 
cc.nombreCuenta,
p.debe,
p.haber,
pd.cargo,
pd.abono,
pd.saldo,
pd.numeroComprobante,
pd.fechaComprobante,
pd.concepto
FROM 
partidaDetalle pd
LEFT JOIN 
partidas p ON pd.partidaId = p.partidaId
LEFT JOIN 
catalogocuentas cc ON pd.cuentaId = cc.cuentaId
LEFT JOIN 
tipoComprobante tc ON pd.tipoComprobanteId = tc.tipoComprobanteId
WHERE 
p.partidaId = $partidaId AND p.codigoPartida = '$codigoPartida'
ORDER BY 
CASE WHEN pd.cargo > 0 THEN 0 ELSE 1 END, pd.cargo DESC";

$result = mysqli_query($con, $query);



if (!$result) {
    die("Error en la consulta: " . mysqli_error($con));
}

//se inicializa el PDF
$pdf = new FPDF();
$pdf->AddPage();
//se establece la fuente de la letra y que sea negrita en tamaño 10
$pdf->SetFont('Arial', 'B', 10);

$pdf->Image('../../../../../lib/img/images.png', 10, 3, 30);
//titulo de el reporte
$pdf->SetY(10); 
$pdf->Cell(190, 5, 'SABIOS Y EXPERTOS', 0, 1, 'C');
$pdf->Cell(190, 5, 'Unidad de Contabilidad', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(190, 5, 'Partida de Libro Diarios', 0, 1, 'C');
$pdf->Cell(190, 5, 'Codigo de partida:'.$row['codigoPartida'], 0, 1, 'C');
$pdf->Cell(190, 5, 'Fecha de Impresion: ' . date('d-m-Y H:i:s'), 0, 1, 'C');

$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(2); // 

// Se especifican las columnas que tendra nuestro reporte
$pdf->Cell(60, 8, 'numeroCuenta', 0);
$pdf->Cell(95, 8, 'nombreCuenta', 0);
$pdf->Cell(20, 8, 'Cargo', 0, 0, 'C');
$pdf->Cell(20, 8, 'Abono', 0, 0, 'C');

$pdf->Ln();
//se establecen que las variables de debe y haber solo se tomaran una sola vez
$debe = 0;
$haber = 0;
$debeHaberSet = false;

while ($row = mysqli_fetch_assoc($result)) {
    if (!$debeHaberSet) {
        $debe = $row['debe'];
        $haber = $row['haber'];
        $debeHaberSet = true;  
    }

    // Imprime la cuenta el numero de la cuenta
    $pdf->Cell(60, 6, $row['numeroCuenta'], 0); 
    $pdf->Cell(95, 6, $row['nombreCuenta'], 0);
     

    // Verifica si cargo es igual a 0.00 para imprimir "-"
    if (floatval($row['cargo']) == 0.00) {
        $pdf->Cell(20, 6, '-', 0, 0, 'C');
    } else {
        $pdf->Cell(20, 6, $row['cargo'], 0, 0, 'C');
    }

    // Verifica si abono es igual a 0.00 para imprimir "-"
    if (floatval($row['abono']) == 0.00) {
        $pdf->Cell(20, 6, '-', 0, 0, 'C');
    } else {
        $pdf->Cell(20, 6, $row['abono'], 0, 0, 'C');
    }

    $pdf->Ln(); 
}

$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
// se establece el final para mostrar las sumas de los datos y ver que estan balanceados
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(165); 
$pdf->Cell(20, 6, $debe, 0, 0, 'C');
$pdf->Cell(20, 6, $haber, 0, 0, 'C');

$pdf->Ln(40);  // Salto de línea

$pdf->SetFont('Arial', '', 10);

//footer del reporte para poder mostrar las firmas de las personas que revisan el reporte
// Calcular el ancho para las celdas de firma para que se ajusten correctamente
$anchoFirma = 195 / 3;
$offsetFirma = 5;

// Agregar espacios para firmas en la misma fila con línea en cada uno
$pdf->SetX(10 + $offsetFirma);
$pdf->Cell($anchoFirma - 2 * $offsetFirma, 6, '', 'B', 0, 'C');  
$pdf->SetX(10 + $anchoFirma + $offsetFirma);
$pdf->Cell($anchoFirma - 2 * $offsetFirma, 6, '', 'B', 0, 'C');  
$pdf->SetX(10 + 2 * $anchoFirma + $offsetFirma);
$pdf->Cell($anchoFirma - 2 * $offsetFirma, 6, '', 'B', 0, 'C');  
$pdf->Ln();  // Salto de línea después de las firmas

// Añadir etiquetas bajo cada línea de firma
$pdf->SetX(10);
$pdf->Cell($anchoFirma, 6, 'Firma Contador', 0, 0, 'C'); 
$pdf->Cell($anchoFirma, 6, 'Firma Supervisor', 0, 0, 'C');  
$pdf->Cell($anchoFirma, 6, 'Firma Director', 0, 1, 'C');

$pdf->Output('I', 'ReportePartida.pdf'); // Enviar el PDF al navegador
?>
