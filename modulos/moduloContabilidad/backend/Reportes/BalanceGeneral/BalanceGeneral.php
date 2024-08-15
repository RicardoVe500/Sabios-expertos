<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php"); // Asegúrate de ajustar la ruta al archivo FPDF

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PDF extends FPDF
{
    // Redefine el constructor para incluir el establecimiento de márgenes
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
        // Establece los márgenes (izquierdo, superior, derecho)
        $this->SetMargins(25, 20, 25);
    }

    // Encabezado de página
    function Header()
    {
        $fechacontable = $_POST['monthYearPickerbalance'];
    
        // Crear un objeto DateTime desde el formato mes/año
        $date = DateTime::createFromFormat('m/Y', $fechacontable);
    
        // Obtener el último día del mes
        $ultimoDiaMes = $date->format('t'); // 't' da el último día del mes
    
        // Formatear la fecha para que aparezca como 'June 2024'
        $fechaFormateada = $date->format('F Y');
    
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
    
        // Formatear la fecha final como "Último día del mes Mes de Año"
        $fechaFormateadaheader = $ultimoDiaMes . ' DE ' . $fechaFormateada;
    
        // Imagen de encabezado
        $this->Image('../../../../../lib/img/images.png', 20, 10, 30);
        $this->SetFont('Arial', 'B', 12);
        // Movernos a la derecha para centrar el título
        $this->Cell(70);
        // Título
        $this->Cell(30, 10, 'SABIOS Y EXPERTOS', 0, 0, 'C');
        // Salto de línea
        $this->Ln(5);
    
        // Restablecer fuente para sub-títulos
        
        // Movernos a la derecha nuevamente
        $this->Cell(70);
        // Sub-título: Departamento de contabilidad
        $this->Cell(30, 10, 'BALANCE GENERAL AL ' . $fechaFormateadaheader, 0, 0, 'C');
        // Salto de línea
        $this->Ln(5);
    
        // Movernos a la derecha
        $this->Cell(70);
        // Sub-título: Balance de Comprobacion
        $this->Cell(35, 10, '(Expresado en Dolares de los Estados Unidos de America)', 0, 0, 'C');
        // Salto de línea
        $this->Ln(5);
    
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
        $fechacontable = $_POST['monthYearPickerbalance'];
    
    
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
            cc.nivelCuenta,
            d.fechaContable
            FROM catalogocuentas cc 
            LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId 
            WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta]
            AND d.fechaContable BETWEEN '$fechaCompleta' AND '$fechaFormateada'
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
                $this->Cell(135, 6, $item['nombreCuenta'], 0, 0);
                $this->Cell(0, 6, number_format($item['totalSaldo']), 0, 1, 'C');
    
                // Imprimir subcuentas de Activos con niveles de indentación
                $this->SetFont('Arial', '', 11);
                foreach ($item['subcuentas'] as $sub) {
                    $indent = 30; // Espacio base para las subcuentas
                    if ($sub['nivel'] == 3) {
                        
                        $this->SetX($indent);
                        $this->Cell(90, 6, $sub['nombreSubcuenta'], 0, 0);
                        $this->Cell(40, 6, number_format($sub['saldo'], 2), 0, 1, 'R');
                        

                    } elseif ($sub['nivel'] > 3) {

                        $this->SetX($indent + 10); // Doble sangría para niveles mayores a 3
                        $this->Cell(90, 6, $sub['nombreSubcuenta'], 0, 0);
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
        $this->Cell(135, 6, 'Total Activos y Cuentas Deudoras', 0, 0);
        $this->Cell(0, 6, '$ ' . number_format($totalActivos), 0, 1);
        $y1 = $this->GetY();
        $this->Line(160, $y1 - 1, 180, $y1 - 1);
        $y2 = $y1 + 2;
        $this->Line(160, $y2 - 1, 180, $y2 - 1);

        $this->Ln(13);
    
        // Sección de Pasivos
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(155, 0, 'PASIVOS', 0, 0, 'C');
        $this->Ln(5);
    
        foreach ($data as $item) {
            if ($item['tipoSaldoId'] == 2) { // Pasivos
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(130, 6, $item['nombreCuenta'], 0, 0);
                $this->Cell(0, 6, number_format($item['totalSaldo']), 0, 1, 'C');
    
                // Imprimir subcuentas de Pasivos con niveles de indentación
                $this->SetFont('Arial', '', 11);
                foreach ($item['subcuentas'] as $sub) {
                    $indent = 30; // Espacio base para las subcuentas
                    if ($sub['nivel'] == 3) {

                        $this->SetX($indent);
                        $this->Cell(90, 6, $sub['nombreSubcuenta'], 0, 0);
                        $this->Cell(40, 6, number_format($sub['saldo'], 2), 0, 1, 'R');

                    } elseif ($sub['nivel'] > 3) {

                    $this->SetX($indent + 10); // Doble sangría para niveles mayores a 3
                    $this->Cell(90, 6, $sub['nombreSubcuenta'], 0, 0);
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
        $this->Cell(135, 6, 'Total Pasivos, patrimonio y Cuentas Acreedoras', 0, 0);
        $this->Cell(0, 6, '$ ' . number_format($totalPasivos), 0, 1);
        $y3 = $this->GetY();
        $this->Line(160, $y3 - 1, 180, $y3 - 1);
        $y4 = $y3 + 2;
        $this->Line(160, $y4 - 1, 180, $y4 - 1);



        $this->SetY(-125); // Ajustar la posición más arriba para tener espacio para las firmas
    
   
        // Firma izquierda
        $this->SetFont('Arial', '', 10);
    
        $this->SetX(15);
        $this->Line(15, $this->GetY() + 10, 55, $this->GetY() + 10); // Longitud reducida de 60 a 40
        $this->Ln(12); // Salto de línea para poner el texto debajo de la línea

        // Texto de firma izquierda
        $this->SetX(15);
        $this->Cell(40, 5, 'Firma Izquierda', 0, 0, 'C');
        $this->Ln(5); // Salto de línea
        $this->SetX(15);
        $this->Cell(40, 5, 'Nombre Izquierda', 0, 0, 'C');


    
        // Firma centro
        $this->SetY(-125); // Regresa a la posición inicial de las firmas
        $this->SetX(90);

        // Línea para la firma centro, más pequeña y centrada
        $this->Line(85, $this->GetY() + 10, 125, $this->GetY() + 10); // Longitud reducida
        $this->Ln(12); // Salto de línea para poner el texto debajo de la línea

        // Texto de firma centro
        $this->SetX(90);
        $this->Cell(30, 5, 'Firma Centro', 0, 0, 'C');
        $this->Ln(5); // Salto de línea
        $this->SetX(90);
        $this->Cell(30, 5, 'Nombre Centro', 0, 0, 'C'); // Nombre aún no establecido
    
        // Firma derecha
        $this->SetY(-125); // Regresa a la posición inicial de las firmas
        $this->SetX(160);

        // Línea para la firma derecha, más pequeña y centrada
        $this->Line(155, $this->GetY() + 10, 195, $this->GetY() + 10); // Longitud reducida
        $this->Ln(12); // Salto de línea para poner el texto debajo de la línea

        // Texto de firma derecha
        $this->SetX(160);
        $this->Cell(30, 5, 'Firma Derecha', 0, 0, 'C');
        $this->Ln(5); // Salto de línea
        $this->SetX(160);
        $this->Cell(30, 5, 'Nombre Derecha', 0, 0, 'C'); // Nombre aún no establecido
    
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
