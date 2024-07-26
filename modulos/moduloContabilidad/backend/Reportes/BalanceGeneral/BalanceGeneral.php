<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php"); // Asegúrate de ajustar la ruta al archivo FPDF


class PDF extends FPDF
{
    // Encabezado de página
    function Header()
    {
       

        // Imagen de encabezado
        $this->Image('../../../../../lib/img/images.png', 10, 7, 30);
        $this->SetFont('Arial','B',12);
        // Movernos a la derecha para centrar el título
        $this->Cell(80);
        // Título
        $this->Cell(30,10,'SABIOS Y EXPERTOS',0,0,'C');
        // Salto de línea
        $this->Ln(5);

        // Restablecer fuente para sub-títulos
        
        // Movernos a la derecha nuevamente
        $this->Cell(80);
        // Sub-título: Departamento de contabilidad
        $this->Cell(30,10,'BALANCE COMPROBACION AL ',0,0,'C');
        // Salto de línea
        $this->Ln(5);

        // Movernos a la derecha
        $this->Cell(80);
        // Sub-título: Balance de Comprobacion
       $this->Cell(35,10,'(Expresado en Dolares de los Estados Unidos de America)',0,0,'C');
        // Salto de línea
        $this->Ln(5);

        // Movernos a la derecha
        //$this->Cell(80);
        // Fecha de impresión
        //$date = date('d-m-Y H:i:s');
        //$this->Cell(30,10,'Fecha de impresion: ' . $date,0,0,'C');
        // Salto de línea para comenzar con el contenido del reporte
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

    function LoadData($con) {

   
        $data = [];
        $totalActivos = 0;
        $totalPasivos = 0;
        

    
        $selecCtsMayores = mysqli_query($con, 
        "SELECT cc.cuentaId, cc.numeroCuenta, cc.nombreCuenta, 
        cc.cuentaDependiente, cc.nivelCuenta, 
        cc.tipoSaldoId, ts.nombreTipo 
        FROM catalogocuentas cc 
        LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId 
        WHERE cc.nivelCuenta = 2;");


        while ($cuentasMayDato = mysqli_fetch_assoc($selecCtsMayores)) {
            $subcuentas = [];
            $totalSaldoCuentaNivel2 = 0;
            $selectSaldos = mysqli_query($con, 
            "SELECT cc.cuentaId, cc.nombreCuenta, 
            SUM(d.debe) AS ttdebe, 
            SUM(d.haber) AS tthaber,
            cc.nivelCuenta,
            d.fechaContable
            FROM catalogocuentas cc 
            LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId 
            WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta]
            GROUP BY cc.cuentaId
            ORDER BY cc.numeroCuenta;");

            while ($datasaldos = mysqli_fetch_assoc($selectSaldos)) {
                $saldoSubcuenta = $cuentasMayDato['tipoSaldoId'] == 1 ? ($datasaldos['ttdebe'] - $datasaldos['tthaber']) : ($datasaldos['tthaber'] - $datasaldos['ttdebe']);
                
                if($datasaldos['nivelCuenta']== 3){
                    $totalSaldoCuentaNivel2 += $saldoSubcuenta;
                }
                
               
                if ($saldoSubcuenta != 0) {
                    $subcuentas[] = [
                        'nombreSubcuenta' => $datasaldos['nombreCuenta'],
                        'saldo' => $saldoSubcuenta,
                        'nivel' => $datasaldos['nivelCuenta']

                    ];
                }
            }
    
            if (!empty($subcuentas)) {
                $data[] = [
                    'nombreCuenta' => $cuentasMayDato['nombreCuenta'],
                    'totalSaldo' => $totalSaldoCuentaNivel2,
                    'tipoSaldoId' => $cuentasMayDato['tipoSaldoId'],
                    'subcuentas' => $subcuentas
                ];

                if ($cuentasMayDato['tipoSaldoId'] == 1) {
                    $totalActivos += $totalSaldoCuentaNivel2;
                } else if ($cuentasMayDato['tipoSaldoId'] == 2) {
                    $totalPasivos += $totalSaldoCuentaNivel2;
                }
                
            }
        }
        return ['data' => $data, 'totalActivos' => $totalActivos, 'totalPasivos' => $totalPasivos];
    }
   
    
    function FancyTable($result) {
        $data = $result['data'];
        $totalActivos = $result['totalActivos'];
        $totalPasivos = $result['totalPasivos'];
    
        // Sección de Activos
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(155, 0, 'ACTIVOS', 0, 0, 'C');
        $this->Ln(5);
    
        foreach ($data as $item) {
            if ($item['tipoSaldoId'] == 1) { // Activos
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(140, 6, $item['nombreCuenta'], 0, 0);
                $this->Cell(0, 6, number_format($item['totalSaldo']), 0, 1, 'C');
    
                // Imprimir subcuentas de Activos con niveles de indentación
                $this->SetFont('Arial', '', 11);
                foreach ($item['subcuentas'] as $sub) {
                    $indent = 20; // Espacio base para las subcuentas
                    if ($sub['nivel'] == 3) {
                        
                        $this->SetX($indent);
                        $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                        $this->Cell(40, 6, number_format($sub['saldo'], 2), 0, 1, 'R');
                        

                    } elseif ($sub['nivel'] > 3) {

                        $this->SetX($indent + 10); // Doble sangría para niveles mayores a 3
                        $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                        $this->Cell(5, 6, number_format($sub['saldo'], 2), 0, 1, 'R');
                        

                    }
                    
                }
                $y1 = $this->GetY();
                $y = $y1 + 2;
                $this->Line(115, $y - 1, 135, $y - 1);

                $this->Ln(15);
            }
        }
    
        // Impresión del total de activos
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(155, 6, 'Total Activos y Cuentas Deudoras', 0, 0);
        $this->Cell(0, 6, '$ ' . number_format($totalActivos), 0, 1);
        $y1 = $this->GetY();
        $this->Line(165, $y1 - 1, 185, $y1 - 1);
        $y2 = $y1 + 2;
        $this->Line(165, $y2 - 1, 185, $y2 - 1);

        $this->Ln(13);
    
        // Sección de Pasivos
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(155, 0, 'PASIVOS', 0, 0, 'C');
        $this->Ln(5);
    
        foreach ($data as $item) {
            if ($item['tipoSaldoId'] == 2) { // Pasivos
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(140, 6, $item['nombreCuenta'], 0, 0);
                $this->Cell(0, 6, number_format($item['totalSaldo']), 0, 1, 'C');
    
                // Imprimir subcuentas de Pasivos con niveles de indentación
                $this->SetFont('Arial', '', 11);
                foreach ($item['subcuentas'] as $sub) {
                    $indent = 20; // Espacio base para las subcuentas
                    if ($sub['nivel'] == 3) {

                        $this->SetX($indent);
                        $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                        $this->Cell(40, 6, number_format($sub['saldo'], 2), 0, 1, 'R');

                    } elseif ($sub['nivel'] > 3) {

                    $this->SetX($indent + 10); // Doble sangría para niveles mayores a 3
                    $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                    $this->Cell(5, 6, number_format($sub['saldo'], 2), 0, 1, 'R');

                    }
                    
                    
                }

                $y1 = $this->GetY();
                $y = $y1 + 2;
                $this->Line(115, $y - 1, 135, $y - 1);

                $this->Ln(10);
            }
        }
    
        // Impresión del total de pasivos
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(155, 6, 'Total Pasivos, patrimonio y Cuentas Acreedoras', 0, 0);
        $this->Cell(0, 6, '$ ' . number_format($totalPasivos), 0, 1);
        $y3 = $this->GetY();
        $this->Line(165, $y3 - 1, 185, $y3 - 1);
        $y4 = $y3 + 2;
        $this->Line(165, $y4 - 1, 185, $y4 - 1);
    }
       
    
}
// Crea una instancia del PDF
$pdf = new PDF();

$pdf->AliasNbPages();
$pdf->AddPage();

// Conexión a la base de datos


// Carga los datos
$data = $pdf->LoadData($con);

// Imprime la tabla con los datos
$pdf->FancyTable($data);

// Cierra la conexión a la base de datos


// Salida del PDF
$pdf->Output();

?>
