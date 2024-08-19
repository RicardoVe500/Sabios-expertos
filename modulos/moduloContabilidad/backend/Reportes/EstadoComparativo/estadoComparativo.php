<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PDF extends FPDF {
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
        $this->SetMargins(20, 20, 20); // Ajustar los márgenes para centrar mejor
    }

    function Header() {
        // Imagen de encabezado
        $this->Image('../../../../../lib/img/images.png', 20, 10, 30);
        
        // Salto de línea para espacio entre la imagen y el título
        $this->Ln(15);
        
        // Configuración de fuente para el título principal
        $this->SetFont('Arial','B',12);
        
        // Movernos a la derecha para centrar el título
        $this->Cell(0, 10, 'SABIOS Y EXPERTOS', 0, 1, 'C');
    
        // Restablecer fuente para sub-títulos
        $this->SetFont('Arial','B',12);
        
        // Sub-título
        $this->Cell(0, 10, utf8_decode('COMPARATIVO ESTADOS FINANCIEROS - ESTADO DE RESULTADOS'), 0, 1, 'C');
    
        // Sub-título: Años comparativos
        $currentYear = date('Y'); // Año actual
        $previousYear = $currentYear - 1; // Año anterior
        $this->Cell(0, 10, "Comparativo $previousYear vs $currentYear", 0, 1, 'C');
    
        // Sub-título: Expresado en Dólares
        $this->Cell(0, 10, utf8_decode('(Expresado en Dólares de los Estados Unidos de América)'), 0, 1, 'C');
        
        // Salto de línea adicional si es necesario
        $this->Ln(15);
    }
    
    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function LoadData($con, $year) {
        $query = "SELECT cc.cuentaId, cc.nombreCuenta, 
                  SUM(CASE WHEN YEAR(p.fechacontable) = $year THEN d.debe ELSE 0 END) as totalDebe, 
                  SUM(CASE WHEN YEAR(p.fechacontable) = $year THEN d.haber ELSE 0 END) as totalHaber
                  FROM catalogocuentas cc
                  LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId
                  LEFT JOIN partidas p ON d.partidaId = p.partidaId
                  WHERE cc.nivelCuenta = 2 
                  GROUP BY cc.cuentaId, cc.nombreCuenta
                  ORDER BY cc.numeroCuenta";
        
        $result = mysqli_query($con, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $saldo = $row['totalDebe'] - $row['totalHaber'];
                $data[] = [
                    'nombreCuenta' => $row['nombreCuenta'],
                    'saldo' => $saldo
                ];
            }
        } else {
            throw new Exception("Error al cargar los datos: " . mysqli_error($con));
        }

        return $data;
    }

    function FancyTable($dataCurrent, $dataPrevious, $previousYear, $currentYear) {
        // Encabezado de la tabla
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(169, 208, 142); // Color verde claro
        $this->Cell(85, 10, "CUENTAS", 1, 0, 'C', true);
        $this->Cell(30, 10, $previousYear, 1, 0, 'C', true);
        $this->Cell(30, 10, $currentYear, 1, 0, 'C', true);
        $this->Cell(30, 10, "Porcentaje", 1, 1, 'C', true);
        
        // Iterar sobre cada cuenta mayor
        foreach ($dataCurrent as $index => $item) {
            $this->SetFont('Arial', '', 11);
            $this->Cell(85, 6, $item['nombreCuenta'], 1, 0);
    
            // Verificar si existe la cuenta en el año anterior
            $saldoPrevious = isset($dataPrevious[$index]) ? $dataPrevious[$index]['saldo'] : 0;
            $formattedSaldoPrevious = $saldoPrevious < 0 ? '(' . number_format(abs($saldoPrevious), 2) . ')' : number_format($saldoPrevious, 2);
            $this->Cell(30, 6, $formattedSaldoPrevious, 1, 0, 'R');
    
            // Formatear el saldo actual
            $formattedSaldoCurrent = $item['saldo'] < 0 ? '(' . number_format(abs($item['saldo']), 2) . ')' : number_format($item['saldo'], 2);
            $this->Cell(30, 6, $formattedSaldoCurrent, 1, 0, 'R');
    
            // Calcular y mostrar el porcentaje de crecimiento
            $difference = $item['saldo'] - $saldoPrevious;
            $percentageChange = $saldoPrevious != 0 ? ($difference / abs($saldoPrevious)) * 100 : 0;
            $formattedPercentageChange = number_format($percentageChange, 2) . '%';
            $this->Cell(30, 6, $formattedPercentageChange, 1, 1, 'R');
        }
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$currentYear = date("Y");
$previousYear = $currentYear - 1;

$dataCurrent = $pdf->LoadData($con, $currentYear);
$dataPrevious = $pdf->LoadData($con, $previousYear);

$pdf->FancyTable($dataCurrent, $dataPrevious, $previousYear, $currentYear);

$pdf->Output();
?>
