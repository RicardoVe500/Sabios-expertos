<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php"); // Asegúrate de ajustar la ruta al archivo FPDF



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class PDF extends FPDF
{
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
        // Establece los márgenes (izquierdo, superior, derecho)
        $this->SetMargins(25, 20, 25);
    }

    // Encabezado de página
    function Header()
    { 

        $fechaInicio = $_POST['fechadesdemayor'];
        $fechaFin = $_POST['fechahastamayor'];

        
        // Imagen de encabezado
        $this->Image('../../../../../lib/img/images.png', 25, 15, 30);
        $this->SetFont('Arial','B',12);
        // Movernos a la derecha para centrar el título
        $this->Cell(70);
        // Título
        $this->Cell(30,10,'SABIOS Y EXPERTOS',0,0,'C');
        // Salto de línea
        $this->Ln(5);
        // Movernos a la derecha nuevamente
        // Movernos a la derecha
        $this->Cell(70);
        // Sub-título: Catálogo de Cuentas
        $this->Cell(30,10,'LIBRO MAYOR',0,0,'C');
        // Salto de línea
        $this->Ln(5);
        $this->Cell(70);
        // Sub-título: Catálogo de Cuentas
        $this->Cell(30, 10, 'Desde: '.$fechaInicio.' Hasta: '. $fechaFin , 0, 0, 'C');

        $this->Ln(5);
    
        $this->Cell(70);
        // Sub-título: Catálogo de Cuentas
        $this->Cell(35, 10, '(Expresado en Dolares de los Estados Unidos de America)', 0, 0, 'C');

        $this->Ln(15);




       
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Cargar datos
    function LoadData($con) {

        $fechaInicio = mysqli_real_escape_string($con, $_POST['fechadesdemayor']);
        $fechaFin = mysqli_real_escape_string($con, $_POST['fechahastamayor']);


        $query = "SELECT p.codigoPartida, p.fechacontable, pd.cargo, pd.abono, 
            cc.nombreCuenta, cc.numeroCuenta, s.saldo, p.fechacontable
        FROM partidas p 
        JOIN partidaDetalle pd ON p.partidaId = pd.partidaId 
        JOIN catalogocuentas cc ON pd.cuentaId = cc.cuentaId
        JOIN saldo s ON cc.cuentaId = s.cuentaId
        WHERE p.fechacontable BETWEEN '$fechaInicio' AND '$fechaFin'
        ORDER BY cc.numeroCuenta;";
    
        $result = mysqli_query($con, $query);
    
        if (!$result) {
            die("Error en la consulta: " . mysqli_error($con));
        }
    
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$row['numeroCuenta'].'  '.$row['nombreCuenta']][] = $row;
        }
        return $data;
    }
    
    // Tabla coloreada
   // Tabla coloreada con saldo
function FancyTable($header, $data, $saldo)
{
    // Altura de la fila
    $rowHeight = 6;
    // Calcula el alto total necesario para la tabla (encabezado + datos)
    $totalHeight = 7 + (count($data) * $rowHeight);

    // Verifica si cabe en la página
    if ($this->GetY() + $totalHeight > $this->PageBreakTrigger) {
        $this->AddPage($this->CurOrientation);
    }

    // Colores, ancho de línea y fuente en negrita para el encabezado
    $this->SetFillColor(224, 224, 224);
    $this->SetTextColor(0);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('', 'B');

    // Anchuras de las columnas
    $w = array(30, 25, 30, 40, 40);
    for ($i = 0; $i < count($header); $i++)
        $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    $this->Ln();

    // Restauración de colores y fuentes para los datos
    $this->SetFillColor(224, 224, 224);
    $this->SetTextColor(0);
    $this->SetFont('');

    // Datos
    
    foreach ($data as $row) {
        $this->Cell($w[0], $rowHeight, $row['codigoPartida'], 'LR', 0, 'L', 0);
        $this->Cell($w[1], $rowHeight, $row['fechacontable'], 'LR', 0, 'L', 0);
        $this->Cell($w[2], $rowHeight, $row['numeroCuenta'], 'LR', 0, 'R', 0);
        // Para el campo 'cargo'
        $cargoFormatted = $row['cargo'] == 0 ? '-' : '$' . number_format($row['cargo']);
        $this->Cell($w[3], $rowHeight, $cargoFormatted, 'LR', 0, 'R', 0);

        // Para el campo 'abono'
        $abonoFormatted = $row['abono'] == 0 ? '-' : '$' . number_format($row['abono']);
        $this->Cell($w[4], $rowHeight, $abonoFormatted, 'LR', 0, 'R', 0);
        $this->Ln();
        
    }

    // Agregar el saldo al final
    // Comprobar si el saldo es negativo y formatearlo
    $formattedSaldo = $saldo < 0 ? '($' . number_format(-$saldo) . ')' : '$' . number_format($saldo);

    // Usar el saldo formateado en la celda
    $this->Cell(array_sum($w), $rowHeight, 'Saldo: ' . $formattedSaldo, 1, 0, 'R', true);
    $this->Ln();

    $this->Cell(array_sum($w), 0, '', 'T');
}


}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Conectar a la base de datos


// Cargar datos
$data = $pdf->LoadData($con);

// Encabezados de las columnas
$header = array('Partida', 'Fecha', 'N Cuenta', 'Cargo', 'Abono');

// Generar una tabla por cada cuenta
foreach ($data as $nombreCuenta => $rows) {
    $pdf->SetX(25); // Restablece la posición x del cursor a 10, que es el margen izquierdo
    $pdf->Cell(0, 10, 'Cuenta: ' . $nombreCuenta, 0, 1);
    $saldo = end($rows)['saldo']; // Asegúrate de que el saldo esté presente en los datos de las filas
    $pdf->FancyTable($header, $rows, $saldo);
}


$pdf->Output();


?>
