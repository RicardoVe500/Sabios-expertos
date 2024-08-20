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
        $this->Cell(0, 10, 'COMPARATIVO ESTADOS FINANCIEROS - ESTADO DE RESULTADOS', 0, 1, 'C');
    
        // Sub-título: Años comparativos
        $currentYear = date('Y'); // Año actual
        $previousYear = $currentYear - 1; // Año anterior
        $this->Cell(0, 10, "Comparativo $previousYear vs $currentYear", 0, 1, 'C');
    
        // Sub-título: Expresado en Dólares
        $this->Cell(0, 10, '(Expresado en Dólares de los Estados Unidos de America)', 0, 1, 'C');
        
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
        $selecCtsMayores = mysqli_query($con, "SELECT cc.cuentaId, cc.numeroCuenta, cc.nombreCuenta, 
        cc.cuentaDependiente, cc.nivelCuenta, cc.tipoSaldoId, ts.nombreTipo 
        FROM catalogocuentas cc 
        LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId 
        WHERE cc.nivelCuenta = 2;");
    
        $data = [];
    
        while ($cuentasMayDato = mysqli_fetch_assoc($selecCtsMayores)) {
            $subcuentas = [];
            $totalSaldoCuentaNivel2 = 0;
    
            $selectSaldos = mysqli_query($con, 
            "SELECT cc.cuentaId, cc.nombreCuenta,
                    SUM(d.debe) AS ttdebe,
                    SUM(d.haber) AS tthaber,
                    cc.nivelCuenta, cc.tipoSaldoId, cc.numeroCuenta
                FROM catalogocuentas cc 
                LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId
                WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta] AND
                      YEAR(d.fechaContable) = $year
                GROUP BY cc.cuentaId
                ORDER BY cc.numeroCuenta;");
    
            while ($datasaldos = mysqli_fetch_assoc($selectSaldos)) {
                $saldoSubcuenta = $cuentasMayDato['tipoSaldoId'] == 1 ? ($datasaldos['ttdebe'] - $datasaldos['tthaber']) : ($datasaldos['tthaber'] - $datasaldos['ttdebe']);
    
                if ($saldoSubcuenta != 0) {
                    $subcuentas[] = [
                        'nombreSubcuenta' => $datasaldos['nombreCuenta'],
                        'nivelCuenta' => $datasaldos['nivelCuenta'],
                        'saldo' => $saldoSubcuenta
                    ];
                    $totalSaldoCuentaNivel2 += $saldoSubcuenta;
                }
            }
    
            if (!empty($subcuentas)) {
                $data[] = [
                    'nombreCuenta' => $cuentasMayDato['nombreCuenta'],
                    'numeroCuenta' => $cuentasMayDato['numeroCuenta'],
                    'totalSaldo' => $totalSaldoCuentaNivel2,
                    'tipoSaldoId' => $cuentasMayDato['tipoSaldoId'],
                    'subcuentas' => $subcuentas
                ];
            }
        }
        return $data; 
    }

    function FancyTable($dataCurrent, $dataPrevious) {
        // Encabezado de la tabla
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(169, 208, 142); // Color verde claro
        $this->Cell(85, 10, "CUENTAS", 1, 0, 'C', true);
        $this->Cell(30, 10, "2023", 1, 0, 'C', true);
        $this->Cell(30, 10, "2024", 1, 0, 'C', true);
        $this->Cell(30, 10, "Porcentaje", 1, 1, 'C', true);
        
        // Iterar sobre cada cuenta mayor
        foreach ($dataCurrent as $index => $item) {
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(85, 6, $item['nombreCuenta'], 1, 0);
    
            // Verificar si existe la cuenta en el año anterior
            $saldoPrevious = isset($dataPrevious[$index]) ? $dataPrevious[$index]['totalSaldo'] : 0;
            $formattedSaldoPrevious = $saldoPrevious < 0 ? '(' . number_format(abs($saldoPrevious), 2) . ')' : number_format($saldoPrevious, 2);
            $this->Cell(30, 6, $formattedSaldoPrevious, 1, 0, 'R');
    
            // Formatear el saldo actual
            $formattedSaldoCurrent = $item['totalSaldo'] < 0 ? '(' . number_format(abs($item['totalSaldo']), 2) . ')' : number_format($item['totalSaldo'], 2);
            $this->Cell(30, 6, $formattedSaldoCurrent, 1, 0, 'R');
    
            // Calcular y mostrar el porcentaje de crecimiento
            $difference = $item['totalSaldo'] - $saldoPrevious;
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

$pdf->FancyTable($dataCurrent, $dataPrevious);

$pdf->Output();
?>
