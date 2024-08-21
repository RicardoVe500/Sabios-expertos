
<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php"); // Asegúrate de ajustar la ruta al archivo FPDF

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PDF extends FPDF {
 
    // Redefine el constructor para incluir el establecimiento de márgenes
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
        // Establece los márgenes (izquierdo, superior, derecho)
        $this->SetMargins(25, 20, 25);
    }


    function Header()
    {
        $fechacontable = $_POST['resultadobalance'];
    
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
        
        $this->Image('../../../../../lib/img/images.png', 25, 10, 30);
        $this->SetFont('Arial','B',12);
        // Movernos a la derecha para centrar el título
        $this->Cell(70);
        // Título
        $this->Cell(30,10,'SABIOS Y EXPERTOS',0,0,'C');
        // Salto de línea
        $this->Ln(5);

        // Restablecer fuente para sub-títulos
        
        // Movernos a la derecha nuevamente
        $this->Cell(70);
        // Sub-título: Departamento de contabilidad

        $this->Cell(30, 10, 'ESTADO DE COMPROBACION AL ' . $fechaFormateadaheader, 0, 0, 'C');

        // Salto de línea
        $this->Ln(5);

        // Movernos a la derecha
        $this->Cell(70);
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
        $this->SetY(-30); // Posiciona a 30 mm del final de la página
        $this->SetFont('Arial', 'I', 10);

        // Firma a la izquierda
        $this->Cell(60, 10, 'Firma Contador', 0, 0, 'C');

        // Firma al centro
        $this->SetX($this->w / 2 - 30); // Ajusta la posición x al centro
        $this->Cell(60, 10, 'Firma Supervisor', 0, 0, 'C');

        // Firma a la derecha
        $this->SetX($this->w - 90); // Ajusta la posición x a la derecha
        $this->Cell(60, 10, 'Firma Director', 0, 0, 'C');
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
        $fechacontable = $_POST['resultadobalance'];
    
    
        list($mes, $anio) = explode('/', $fechacontable);
        $ultimoDia = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $fechaFormateada = $anio . '-' . $mes . '-' . $ultimoDia;

        
        $selecCtsMayores = mysqli_query($con, "SELECT cc.cuentaId, cc.numeroCuenta, cc.nombreCuenta, 
        cc.cuentaDependiente, cc.nivelCuenta, 
        cc.tipoSaldoId, ts.nombreTipo 
        FROM catalogocuentas cc 
        LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId 
        WHERE cc.nivelCuenta = 2;");
    
        $data = [];
        $totalBruto = 0;  // Inicializar el total bruto
    
        while ($cuentasMayDato = mysqli_fetch_assoc($selecCtsMayores)) {
            $subcuentas = [];
            $totalSaldoCuentaNivel2 = 0;
    
            // Consulta para obtener los saldos de las subcuentas asociadas de todos los niveles superiores
            $selectSaldos = mysqli_query($con, 
            "SELECT cc.cuentaId, cc.nombreCuenta,
                    SUM(d.debe) AS ttdebe,
                    SUM(d.haber) AS tthaber,
                    d.fechaContable,
                    cc.nivelCuenta,
                    cc.tipoSaldoId,
                    cc.numeroCuenta
                FROM catalogocuentas cc 
                LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId
                WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta] AND
                    cc.numeroCuenta NOT LIKE '1%' AND
                    cc.numeroCuenta NOT LIKE '2%' AND
                    cc.numeroCuenta NOT LIKE '3%' AND
                    cc.numerocuenta NOT LIKE '5102%' AND
                    cc.numeroCuenta NOT LIKE '4102%' AND
                    d.fechaContable BETWEEN '$fechaCompleta' AND '$fechaFormateada'
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
                        'nivelCuenta' => $datasaldos['nivelCuenta'],
                        'saldo' => $saldoSubcuenta
                    ];
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
    
    
    function calculateTotalBruto($data) {
        $totalIngresos = 0;
        $totalGastos = 0;
    
        // Recorre los datos para sumar ingresos y gastos por separado basándose en el tipo de saldo
        foreach ($data as $item) {
            if ($item['tipoSaldoId'] == 1) { // Deudora podría implicar gastos o ingresos dependiendo de la naturaleza de la cuenta
                // Suponemos que las cuentas de ingresos deudoras son ingresos
                $totalIngresos += $item['totalSaldo'];
            } elseif ($item['tipoSaldoId'] == 2) { // Acreedora podría implicar ingresos o gastos
                // Suponemos que las cuentas de gastos acreedoras son gastos
                $totalGastos += $item['totalSaldo'];
            }
        }
    
        // Calcula el total bruto como la diferencia de ingresos y gastos
        $totalBruto = $totalGastos - $totalIngresos;
        
        return $totalBruto;
    }


    function LoadData2($con) {

        $anioActual = date("Y");
        $fechaCompleta = $anioActual . "-01-01";
        $fechacontable = $_POST['resultadobalance'];
    
    
        list($mes, $anio) = explode('/', $fechacontable);
        $ultimoDia = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $fechaFormateada = $anio . '-' . $mes . '-' . $ultimoDia;

        
        $selecCtsMayores = mysqli_query($con, "SELECT cc.cuentaId, cc.numeroCuenta, cc.nombreCuenta, 
        cc.cuentaDependiente, cc.nivelCuenta, 
        cc.tipoSaldoId, ts.nombreTipo 
        FROM catalogocuentas cc 
        LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId 
        WHERE cc.nivelCuenta = 2;");
    
        $data2 = [];
        //$totalBruto = 0;  // Inicializar el total bruto
    
        while ($cuentasMayDato = mysqli_fetch_assoc($selecCtsMayores)) {
            $subcuentas = [];
            $totalSaldoCuentaNivel2 = 0;
    
            // Consulta para obtener los saldos de las subcuentas asociadas de todos los niveles superiores
            $selectSaldos = mysqli_query($con, 
            "SELECT cc.cuentaId, cc.nombreCuenta,
                    SUM(d.debe) AS ttdebe,
                    SUM(d.haber) AS tthaber,
                    d.fechaContable,
                    cc.nivelCuenta,
                    cc.tipoSaldoId,
                    cc.numeroCuenta
                FROM catalogocuentas cc 
                LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId
                WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta] AND
                    cc.numeroCuenta NOT LIKE '1%' AND
                    cc.numeroCuenta NOT LIKE '2%' AND
                    cc.numeroCuenta NOT LIKE '3%' AND
                     (cc.numeroCuenta LIKE '4102%' OR
                     cc.numeroCuenta LIKE '5102%' ) AND
                     d.fechaContable BETWEEN '$fechaCompleta' AND '$fechaFormateada'
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
                        'nivelCuenta' => $datasaldos['nivelCuenta'],
                        'saldo' => $saldoSubcuenta
                    ];
                }
            }
    
            if (!empty($subcuentas)) {
                $data2[] = [
                    'nombreCuenta' => $cuentasMayDato['nombreCuenta'],
                    'numeroCuenta' => $cuentasMayDato['numeroCuenta'],
                    'totalSaldo' => $totalSaldoCuentaNivel2,
                    'tipoSaldoId' => $cuentasMayDato['tipoSaldoId'],
                    'subcuentas' => $subcuentas
                ];
            }
        }
        return $data2; 
    }

    function calculateTotalOperativo($data2) {
        // Ordenamos el arreglo $data2 basado en numeroCuenta de forma descendente
        usort($data2, function($a, $b) {
            return strcmp($b['numeroCuenta'], $a['numeroCuenta']);
        });
    
        $totalOperativo = 0;
        $primerValor = true;
    
        foreach ($data2 as $item) {
            if ($primerValor) {
                $totalOperativo = $item['totalSaldo'];
                $primerValor = false;
            } else {
                $totalOperativo -= $item['totalSaldo'];
            }
        }
        return $totalOperativo;
    }
    
    
    function FancyTable($result, $operationalResult = null) {

        $data = $result['data'];
        $totalBruto = $result['totalBruto'];
        
        // Ordenar los datos por tipo de saldo
        usort($data, function($a, $b) {
            return $b['tipoSaldoId'] - $a['tipoSaldoId'];
        });
    
        // Iterar sobre cada cuenta mayor
        foreach ($data as $item) {
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(145, 6, $item['nombreCuenta'], 0, 0);
            
            // Formatear y mostrar el saldo de la cuenta mayor
            $formattedTotalSaldo = $item['totalSaldo'] < 0 ? '(' . number_format(abs($item['totalSaldo']), 2) . ')' : number_format($item['totalSaldo'], 2);
            $this->Cell(0, 6, $formattedTotalSaldo, 0, 1);
    
            // Iterar sobre las subcuentas
            foreach ($item['subcuentas'] as $sub) {
                $nivel = $sub['nivelCuenta'];
                $indent = 30;  // Espacio base para las subcuentas
                
                if ($nivel == 3) {
                    $this->SetFont('Arial', '', 11);
                    $this->SetX($indent);
                    $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                    
                    // Formatear y mostrar el saldo de la subcuenta
                    $formattedSaldo = $sub['saldo'] < 0 ? '(' . number_format(abs($sub['saldo']), 2) . ')' : number_format($sub['saldo'], 2);
                    $this->Cell(35, 6, $formattedSaldo, 0, 1, 'R');
                } elseif ($nivel > 3) {
                    $this->SetFont('Arial', '', 11);
                    $this->SetX($indent + 5 * ($nivel - 3));
                    $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                    $formattedSaldo = $sub['saldo'] < 0 ? '(' . number_format(abs($sub['saldo']), 2) . ')' : number_format($sub['saldo'], 2);
                    $this->Cell(5, 6, $formattedSaldo, 0, 1, 'R');
                }
            }
            
        }
        $y1 = $this->GetY();
        $this->Line(120, $y1 - 1, 140, $y1 - 1);

        $y2 = $this->GetY();
        $this->Line(147, $y2 - 6.5, 167, $y1 - 6.5);
        
        // Mostrar el total bruto al final del reporte
        $this->SetFont('Arial', 'B', 11);
        $this->Ln(3);
        
        $this->Cell(145, 6, "Total Bruto:  ", 0, 0);
     
        $y3 = $this->GetY();
        $this->Line(170, $y3 - 1, 195, $y3 - 1);
       
        $formattedTotalBruto = $totalBruto < 0 ? '(' . number_format(abs($totalBruto), 2) . ')' : number_format($totalBruto, 2);
        $this->Cell(0, 6, "$" . $formattedTotalBruto, 0, 0);
       

        

        if ($operationalResult) {
            $this->Ln(5);
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(0, 10, "Resultados Operativos", 0, 1, 'C');
            
            // Procesar y mostrar los datos operativos
            foreach ($operationalResult['data'] as $item) {
                $this->SetFont('Arial', 'B', 11);
    
                // Extraer el primer dígito de numeroCatalogo
                $primerDigito = substr($item['numeroCuenta'], 0, 1);
            
                // Modificar el título de nombreCuenta basado en el primer dígito de numeroCatalogo
                switch ($primerDigito) {
                    case '4':
                        $nombreCuentaModificado = "MENOS:" ;
                        break;
                    case '5':
                        $nombreCuentaModificado = "MAS: ";
                        break;
                    default:
                        $nombreCuentaModificado = $item['nombreCuenta']; // Sin modificación
                }
            
                // Imprimir el nombre de la cuenta modificado
                $this->Cell(150, 6, $nombreCuentaModificado, 0, 0);
                
                // Formatear y imprimir el total de saldo
                $formattedTotalSaldo = $item['totalSaldo'] < 0 ? '(' . number_format($item['totalSaldo'], 2) . ')' : number_format($item['totalSaldo'], 2);
                $this->Cell(0, 6, $formattedTotalSaldo, 0, 1);
            
                // Procesar y mostrar las subcuentas con configuraciones basadas en el nivel
                foreach ($item['subcuentas'] as $sub) {
                    $nivel = $sub['nivelCuenta'];
                    if ($nivel == 3) {
                        $indent = 25;  // Espacio base para las subcuentas nivel 3
                        $this->SetFont('Arial', '', 11);
                        $this->SetX($indent);
                        $this->Cell(90, 6, $sub['nombreSubcuenta'], 0, 0);
                        $formattedSaldo = $sub['saldo'] < 0 ? '(' . number_format($sub['saldo'], 2) . ')' : number_format($sub['saldo'], 2);
                        $this->Cell(50, 6, $formattedSaldo, 0, 1, 'R');
                        
                    } elseif ($nivel > 3) {
                        $indent = 25 + 5 * ($nivel - 3);  // Incrementar indentación para niveles superiores
                        $this->SetFont('Arial', '', 11);
                        $this->SetX($indent);
                        $this->Cell(90, 6, $sub['nombreSubcuenta'], 0, 0);
                        $formattedSaldo = $sub['saldo'] < 0 ? '(' . number_format($sub['saldo'], 2) . ')' : number_format($sub['saldo'], 2);
                        
                        $this->Cell(20, 6, $formattedSaldo, 0, 1, 'R');
                    }
                   
                }
            } 
            $yopera = $this->GetY();
            $this->Line(147, $yopera - 6.5, 167, $yopera - 6.5);

            $sub = $this->GetY();
            $this->Line(120, $sub - 1, 140,  $sub - 1);
            
            
            $this->Ln(5);
            $this->SetFont('Arial', 'B', 11);
            $formattedTotalOperativo = $operationalResult['totalOperativo'] < 0 ? '(' . number_format(abs($operationalResult['totalOperativo']), 2) . ')' : number_format($operationalResult['totalOperativo'], 2);
        
            $this->Cell(155, 6, "Exedente Operativo:  ", 0, 0);
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            
            $this->Cell(0, 6, "$" . $formattedTotalOperativo, 0, 0, 'C');


        // Calcular y mostrar la diferencia entre el total bruto y el total operativ
    
         // Calcular la diferencia entre el total bruto y el total operativo
         $diferencia =   $totalBruto - $operationalResult['totalOperativo'] ;
         $this->Ln(10);
         $this->SetFont('Arial', 'B', 12);
         
         // Comprobar si la diferencia es negativa o positiva y mostrar el mensaje apropiado
         if ($diferencia < 0) {
            $this->Ln(3);
            $this->SetFont('Arial', 'B', 11);
            $formattedDiferencia = $diferencia < 0 ? '(' . number_format(abs($diferencia), 2) . ')' : number_format($diferencia, 2);
            $this->Cell(155, 6, "Exedente del Ejercicio:  ", 0, 0);
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            $this->Cell(0, 6, "$" . $formattedDiferencia, 0, 0, 'C');


            $this->Ln(10);


            $this->Cell(155, 6, "Impuesto Sobre la Renta (22.9%):  ", 0, 0);


            // Calcular el impuesto
            $impuesto = $diferencia * 0.229;
            $formattedImpuesto = $impuesto < 0 ? '(' . number_format(abs($impuesto), 2) . ')' : number_format($impuesto, 2);

            // Imprimir el impuesto
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            $this->Cell(0, 6, "$" . $formattedImpuesto, 0, 1, 'C');

            // Ajustar el total con el impuesto y formato
            $totalFinal = $diferencia - $impuesto;
            $formattedTotalFinal = $totalFinal < 0 ? '(' . number_format(abs($totalFinal), 2) . ')' : number_format($totalFinal, 2);
            $this->Ln(5);
            // Imprimir el total ajustado
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            $this->Line(170, $yex - 2, 195, $yex - 2);
            $this->Cell(155, 6, "Resultado Neto:  ", 0, 0);
            $this->Cell(0, 6, "$". $formattedTotalFinal, 0, 1, 'C');
            $this->Ln(2);
            $final = $this->GetY();
            $this->Line(170, $final - 1, 195, $final - 1);

    
         } else {
            $this->Ln(3);
            $this->SetFont('Arial', 'B', 11);
            $formattedDiferencia = $diferencia < 0 ? '(' . number_format(abs($diferencia), 2) . ')' : number_format($diferencia, 2);
            $this->Cell(155, 6, "Exedente del Ejercicio:  ", 0, 0);
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            $this->Cell(0, 6, "$" . $formattedDiferencia, 0, 0, 'C');


            $this->Ln(10);


            $this->Cell(155, 6, "Impuesto Sobre la Renta (22.9%):  ", 0, 0);


            // Calcular el impuesto
            $impuesto = $diferencia * 0.229;
            $formattedImpuesto = $impuesto < 0 ? '(' . number_format(abs($impuesto), 2) . ')' : number_format($impuesto, 2);

            // Imprimir el impuesto
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            $this->Cell(0, 6, "$" . $formattedImpuesto, 0, 1, 'C');

            // Ajustar el total con el impuesto y formato
            $totalFinal = $diferencia - $impuesto;
            $formattedTotalFinal = $totalFinal < 0 ? '(' . number_format(abs($totalFinal), 2) . ')' : number_format($totalFinal, 2);
            $this->Ln(5);
            // Imprimir el total ajustado
            $yex = $this->GetY();
            $this->Line(170, $yex - 1, 195, $yex - 1);
            $this->Line(170, $yex - 2, 195, $yex - 2);
            $this->Cell(155, 6, "Resultado Neto:  ", 0, 0);
            $this->Cell(0, 6, "$". $formattedTotalFinal, 0, 1, 'C');
            $this->Ln(2);
            $final = $this->GetY();
            $this->Line(170, $final - 1, 195, $final - 1);

           

         }
        
        }
            


    }

    function savePDF() {
        // Obtener mes y año desde la fecha contable
        $fechacontable = $_POST['resultadobalance'];
        list($mes, $anio) = explode('/', $fechacontable);
    
        if ($mes == '12') {
            $nombreCarpeta = "/$anio"; // Nombre de la carpeta basado en el año actual
            $rutaCarpeta = $_SERVER['DOCUMENT_ROOT'] . $nombreCarpeta; // Ruta completa en la raíz del disco
    
            // Verificar si la carpeta ya existe, si no, crearla
            if (!file_exists($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0777, true); // Crear la carpeta con permisos de escritura
            }
    
            // Especificar la ruta del archivo PDF dentro de la nueva carpeta
            $rutaArchivo = $rutaCarpeta . '/archivo.pdf';
    
            // Verificar si el archivo PDF ya existe y eliminarlo si es así
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo); // Eliminar el archivo existente
            }
    
            // Guardar el nuevo PDF en la carpeta especificada
            $this->Output('F', $rutaArchivo); // 'F' significa que se guarda el archivo en el servidor
            echo "PDF guardado en: $rutaArchivo";
        } else {
            // Si no es diciembre, guardar en una ruta predeterminada
            $this->Output();
        }
    }
    

    
    
    
}


$pdf = new PDF();

$pdf->AliasNbPages();

$pdf->AddPage();

// Conexión a la base de datos
$data = $pdf->LoadData($con);
$totalBruto = $pdf->calculateTotalBruto($data);
$result = ['data' => $data, 'totalBruto' => $totalBruto];

$dataOperativa = $pdf->LoadData2($con);
$totalOperativo = $pdf->calculateTotalOperativo($dataOperativa);
$operationalResult = ['data' => $dataOperativa, 'totalOperativo' => $totalOperativo];

$pdf->FancyTable($result, $operationalResult);

// Cierra la conexión a la base de datos

// Salida del PDF adaptada para manejar la lógica de archivo basada en el mes
$pdf->savePDF();
?>
