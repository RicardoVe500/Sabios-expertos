<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php"); // Asegúrate de ajustar la ruta al archivo FPDF





class PDF extends FPDF
{
    // Encabezado de página
    function Header()
    {
        $fechacontable = $_POST['monthYearPickergeneral'];

        // Crear un objeto DateTime desde el formato mes/año
        $date = DateTime::createFromFormat('m/Y', $fechacontable);
        
        // Formatear la fecha para que aparezca como 'June 2024'
        $fechaFormateada = $date->format('F Y'); // Se elimina la pleca, solo espacio entre mes y año
        
        // Crear un array de traducción de meses de inglés a español
        $meses = [
            'January' => 'ENERO',
            'February' => 'FEBRERO',
            'March' => 'MARZO',
            'April' => 'ABRIL',
            'May' => 'MAYO',
            'June' => 'JUNIO',
            'July' => 'JULIO',
            'August' => 'AGOSTO',
            'September' => 'SEPTIEMBRE',
            'October' => 'OCTUBRE',
            'November' => 'NOVIEMBRE',
            'December' => 'DICIEMBRE'
        ];
        
        // Obtener el nombre del mes en inglés
        $mesIngles = $date->format('F');
        
        // Reemplazar el mes en inglés por el mes en español
        $mesEspanol = $meses[$mesIngles];
        $fechaFormateada = str_replace($mesIngles, $mesEspanol, $fechaFormateada);
        
        // Insertar " de " entre el mes y el año
        $fechaFormateadaheader = str_replace(' ', ' DE ', $fechaFormateada);

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
        $this->Cell(30,10,'BALANCE GENERAL AL '.$fechaFormateadaheader,0,0,'C');
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

    $anioActual = date("Y");
    $fechaCompleta = $anioActual . "-01-01";
    $fechacontable = $_POST['monthYearPickergeneral'];


    list($mes, $anio) = explode('/', $fechacontable);
    $ultimoDia = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
    $fechaFormateada = $anio . '-' . $mes . '-' . $ultimoDia;

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
             d.fechaContable
            FROM catalogocuentas cc 
            LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId 
            WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta]
            AND cc.nivelCuenta = 3
            AND d.fechaContable BETWEEN '$fechaCompleta' AND '$fechaFormateada'
            GROUP BY cc.cuentaId;");

            while ($datasaldos = mysqli_fetch_assoc($selectSaldos)) {
                $saldoSubcuenta = $cuentasMayDato['tipoSaldoId'] == 1 ? ($datasaldos['ttdebe'] - $datasaldos['tthaber']) : ($datasaldos['tthaber'] - $datasaldos['ttdebe']);
                $totalSaldoCuentaNivel2 += $saldoSubcuenta;
                if ($saldoSubcuenta != 0) {
                    $subcuentas[] = [
                        'nombreSubcuenta' => $datasaldos['nombreCuenta'],
                        'saldo' => $saldoSubcuenta
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

        $isFirstPassive = true;
    
    
        foreach ($data as $item) {
         
            $this->SetFont('Arial', 'B', 12);
            $this->Ln(5);
            //Para escribir el total de activo
            if ($isFirstPassive && $item['tipoSaldoId'] == 2) {
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(145, 6, 'Total Activos', 0, 0);
                $this->Cell(0, 6, '$ '.number_format($totalActivos), 0, 1);
                $y = $this->GetY();
                $this->Line(155, $y - 1, 175, $y - 1); 
                $y2 = $y + 2;  // Aumenta el valor de $y en 2 mm
                $this->Line(155, $y2 - 1, 175, $y2 - 1);
                $this->Ln(15);
                $isFirstPassive = false;  // Cambiar la bandera después de mostrar total de activos
            }

            $this->Cell(145, 6, $item['nombreCuenta'], 0, 0);
            $this->Cell(0, 6, number_format($item['totalSaldo']), 0, 1);

          
            
            $this->SetFont('Arial', '', 11);

    
            foreach ($item['subcuentas'] as $sub) {
                $this->SetX(20);
                $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                $this->Cell(30, 6, number_format($sub['saldo'], 2), 0, 1, 'R');

               
            }
           
            $y = $this->GetY();
            // Dibujar la línea desde la posición X de la celda de saldo hasta el final de la página
            $this->Line(155, $y - 1, 125, $y - 1); 
            
        }
        
        // Agregar los totales de Activos y Pasivos
 
      

        $this->SetFont('Arial', 'B', 12);
        $this->Ln(5);
        $this->Cell(145, 6, 'Total Pasivos y patrimonio', 0, 0);
        $this->Cell(0, 6, '$ '.number_format($totalPasivos), 0, 1);
        $y = $this->GetY();
            // Dibujar la línea desde la posición X de la celda de saldo hasta el final de la página
            $this->Line(155, $y - 1, 175, $y - 1); 
            $y2 = $y + 2;  // Aumenta el valor de $y en 2 mm
                $this->Line(155, $y2 - 1, 175, $y2 - 1);
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
