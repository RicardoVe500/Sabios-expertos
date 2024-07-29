<?php
include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php"); // Asegúrate de ajustar la ruta al archivo FPDF

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
        $this->Cell(30,10,'ESTADO DE RESULTADO AL ',0,0,'C');
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
        // Consulta para obtener las cuentas de nivel 2
        $selecCtsMayores = mysqli_query($con, "SELECT cc.cuentaId, cc.numeroCuenta, cc.nombreCuenta, 
        cc.cuentaDependiente, cc.nivelCuenta, 
        cc.tipoSaldoId, ts.nombreTipo 
        FROM catalogocuentas cc 
        LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId 
        WHERE cc.nivelCuenta = 2;");
    
        $data = [];
        $totalBruto = 0;  // Inicializar el total bruto
        // Control para el primer valor
    
        while ($cuentasMayDato = mysqli_fetch_assoc($selecCtsMayores)) {
            $subcuentas = [];
            $totalSaldoCuentaNivel2 = 0;
    
            // Consulta para obtener los saldos de las subcuentas asociadas
            $selectSaldos = mysqli_query($con, 
            "SELECT cc.cuentaId, cc.nombreCuenta,
                    SUM(d.debe) AS ttdebe,
                    SUM(d.haber) AS tthaber,
                    d.fechaContable,
                    cc.nivelCuenta,
                    cc.tipoSaldoId
                FROM catalogocuentas cc 
                LEFT JOIN detalle d ON cc.cuentaId = d.cuentaId
                WHERE SUBSTRING(cc.numeroCuenta, 1, 2) = $cuentasMayDato[numeroCuenta] AND
                    cc.numeroCuenta NOT LIKE '1%' AND
                    cc.numeroCuenta NOT LIKE '2%' AND
                    cc.numeroCuenta NOT LIKE '3%' 
                GROUP BY cc.cuentaId
                ORDER BY cc.numeroCuenta;");
    
            while ($datasaldos = mysqli_fetch_assoc($selectSaldos)) {
                $saldoSubcuenta = $cuentasMayDato['tipoSaldoId'] == 1 ? ($datasaldos['ttdebe'] - $datasaldos['tthaber']) : ($datasaldos['tthaber'] - $datasaldos['ttdebe']);
                
                if ($datasaldos['nivelCuenta'] == 3) {
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
                    'totalSaldo' => $totalSaldoCuentaNivel2,
                    'tipoSaldoId' => $cuentasMayDato['tipoSaldoId'],
                    'subcuentas' => $subcuentas
                ];
            }
        }
        return $data; // Devolver data y total bruto
    }
    
    function calculateTotalBruto($data) {
        $totalBruto = 0;
        $primerValor = true;
    
        foreach ($data as $item) {
            if ($primerValor) {
                // Tomar el primer valor como base
                $totalBruto = $item['totalSaldo'];
                $primerValor = false;
            } else {
                // Restar los valores siguientes
                $totalBruto -= $item['totalSaldo'];
            }
        }
        return $totalBruto;
    }
    

    function FancyTable($result) {
        $data = $result['data'];
        $totalBruto = $result['totalBruto'];
        
        usort($data, function($a, $b) {
            return $b['tipoSaldoId'] - $a['tipoSaldoId'];
        });

        foreach ($data as $item) {
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(145, 6, $item['nombreCuenta'], 0, 0);
            $this->Cell(0, 6, number_format($item['totalSaldo']), 0, 1);

            $this->SetFont('Arial', '', 11);
            foreach ($item['subcuentas'] as $sub) {
                if ($sub['nivelCuenta'] == 3) {
                    $this->SetX(20);
                    $this->Cell(100, 6, $sub['nombreSubcuenta'], 0, 0);
                    $this->Cell(30, 6, number_format($sub['saldo'], 2), 0, 1, 'R');
                }
            }
        }

        $this->SetFont('Arial', 'B', 12);
        $this->Ln(5);
        $this->Cell(0, 10, "Total Bruto: $" . number_format($totalBruto, 2), 0, 1);
    }

    
    
}


$pdf = new PDF();

$pdf->AliasNbPages();
$pdf->AddPage();

// Conexión a la base de datos
$data = $pdf->LoadData($con);

// Calcula el total bruto
$totalBruto = $pdf->calculateTotalBruto($data);

// Preparar el resultado para FancyTable
$result = ['data' => $data, 'totalBruto' => $totalBruto];

// Imprime la tabla con los datos
$pdf->FancyTable($result);

// Cierra la conexión a la base de datos



// Salida del PDF
$pdf->Output();


/*

////////////////////////////////////////////////////////////////////////////////
                  
    EJEPMLO DE COMO HACER EL REPORTE Y COMO ESTRUCTURARLO


        Paso 1: Recopilar Información de las Partidas Contables

    El primer paso es recoger todos los datos relevantes de las partidas contables que hemos registrado durante el período. En nuestro caso, esto incluirá:

        Ventas
        Costo de Bienes Vendidos (COGS)
        Gastos (Comisiones y Mano de Obra)


    Paso 2: Clasificar las Transacciones

    Clasifica las transacciones en categorías que coincidan con las secciones del estado de resultados:

        Ingresos: Todas las entradas a las cuentas de ingresos.
        Costo de Ventas: Todas las entradas a las cuentas que reflejan el costo directo de los bienes o servicios vendidos.
        Gastos Operativos: Todas las entradas a cuentas de gastos como salarios, comisiones, etc.

    Paso 3: Sumar los Totales por Categoría

    Suma todos los importes de cada categoría:

        Total de ingresos
        Total de costo de ventas
        Total de gastos operativos

    Paso 4: Calcular el Resultado Bruto


    El resultado bruto se calcula restando el total del costo de ventas del total de ingresos:
    Resultado 
    

    Resultado Bruto = Ingresos Totales − Costo de Ventas Totales
    
    
    Paso 5: Calcular el Resultado Operativo

    El resultado operativo se obtiene restando los gastos operativos del resultado bruto:

    Resultado Operativo = Resultado Bruto − Gastos Operativos Totales 
    
    
    Paso 6: Ajustes y Otros Resultados

    Si hay otros ingresos, gastos no operativos o impuestos, estos también deben ser incluidos aquí para calcular el resultado neto:
    
    Resultado Neto = Resultado Operativo − (Otros Gastos + Otros Ingresos) − Impuestos 
   

    Paso 7: Preparar el Reporte

    Organiza todos los cálculos en un formato de estado de resultados:

        Ingresos
            Ventas
        Costo de Ventas
            Costo de Bienes Vendidos
        Resultado Bruto
        Gastos Operativos
            Comisiones
            Mano de Obra
        Resultado Operativo
        Otros Ingresos/Gastos
        Resultado Neto

*/



?>
