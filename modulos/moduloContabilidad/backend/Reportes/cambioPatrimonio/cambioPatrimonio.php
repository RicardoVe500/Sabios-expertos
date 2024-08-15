<?php
ob_start(); // Inicia el buffer de salida

include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php");

// Verifica que la fecha hasta no esté vacía
if (empty($_POST['fechahasta'])) {
    echo json_encode(['status' => 'warning', 'message' => 'La fecha hasta no puede estar vacía']);
    exit();
}

// La fecha de inicio siempre es el 1 de enero del año de la fecha final
$anio = DateTime::createFromFormat('m/Y', $_POST['fechahasta'])->format('Y');
$fechaInicio = "$anio-01-01";

// Obtener la fecha hasta del formulario y convertir a formato Y-m-d
$fechaFin = DateTime::createFromFormat('m/Y', $_POST['fechahasta'])->format('Y-m-t');

$meses = [
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
];

// Convertir fechas a nombres de meses y años
$fechaInicioFormatted = strftime('%B %Y', strtotime($fechaInicio));
$fechaFinFormatted = strftime('%B %Y', strtotime($fechaFin));

// Reemplazar los nombres de los meses en inglés por los de español
$fechaInicioFormatted = strtr($fechaInicioFormatted, $meses);
$fechaFinFormatted = strtr($fechaFinFormatted, $meses);

class PDF extends FPDF
{
    private $fechaInicio;
    private $fechaFin;
    private $anio;

    function __construct($fechaInicioFormatted, $fechaFinFormatted, $anio) {
        parent::__construct();
        $this->fechaInicio = $fechaInicioFormatted;
        $this->fechaFin = $fechaFinFormatted;
        $this->anio = $anio;
    }

    function Header()
    {
        $this->Image('../../../../../lib/img/images.png', 10, 7, 30);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'SABIOS Y EXPERTOS', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, 'Departamento de contabilidad', 0, 1, 'C');
        $this->Cell(0, 10, 'Estado de Cambios en el Patrimonio', 0, 1, 'C');
        $this->Cell(0, 10, "Periodo: $this->fechaInicio - $this->fechaFin", 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Función para formatear los números, mostrando los negativos entre paréntesis
    function formatNumber($number) {
        // Si el número es negativo, lo formatea con paréntesis
        if ($number < 0) {
            return '($' . number_format(abs($number), 2) . ')';
        } else {
            return '$' . number_format($number, 2);
        }
    }

    function FancyTable($totals)
    {
        $this->SetFont('Arial', 'B', 8.5); // Tamaño de la fuente reducido para ajustar el texto
        $this->SetFillColor(200, 200, 200); // Color de relleno para encabezados
    
        // Asignar un ancho específico para cada columna
        $w = [39, 30, 32, 40, 50]; // Ejemplo de anchos en milímetros
    
        // Encabezado de columnas, todo en una sola línea
        $header = [
            "Concepto", 
            "Capital Social", 
            "Utilidades Retenidas", 
            "Revalorización de Activos", 
            "Total Patrimonio Neto"
        ];
    
        // Mostrar los encabezados
        foreach ($header as $i => $col) {
            $this->Cell($w[$i], 20, utf8_decode($col), 1, 0, 'C', true); 
        }
        $this->Ln();
    
        // Filas dinámicas con datos reales del query
        $this->SetFont('Arial', '', 9); // Ajustar el tamaño de fuente de las celdas de datos
    
        foreach ($totals as $concept => $values) {
            $this->Cell($w[0], 8, utf8_decode($concept), 1);
            $this->Cell($w[1], 8, $this->formatNumber($values['capital_social']), 1, 0, 'R');
            $this->Cell($w[2], 8, $this->formatNumber($values['utilidades_retenidas']), 1, 0, 'R');
            $this->Cell($w[3], 8, $this->formatNumber($values['revalorizacion_activos']), 1, 0, 'R');
            $this->Cell($w[4], 8, $this->formatNumber($values['total']), 1, 0, 'R');
            $this->Ln();
        }
    }
    
    function LoadData($fechaInicio, $fechaFin)
    {
        $mysqli = new mysqli('localhost', 'root', '', 'tesis');
        if ($mysqli->connect_error) {
            die("Conexión fallida: " . $mysqli->connect_error);
        }

        // Obtener datos para cada sección
        $query = "SELECT pd.cargo, pd.abono, cc.nombreCuenta, p.fechacontable
                  FROM partidaDetalle pd
                  JOIN partidas p ON p.partidaId = pd.partidaId
                  JOIN catalogocuentas cc ON cc.cuentaId = pd.cuentaId
                  WHERE p.fechacontable BETWEEN '$fechaInicio' AND '$fechaFin'
                  ORDER BY p.fechacontable ASC, cc.nombreCuenta ASC";
        $result = $mysqli->query($query);
        if (!$result) {
            die("Error en la consulta de datos: " . $mysqli->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $mysqli->close();
        return $data;
    }

    function calculateTotals($data)
    {
        // Inicializar los totales
        $totals = [
            'Saldo Inicial' => ['capital_social' => 0, 'utilidades_retenidas' => 0, 'revalorizacion_activos' => 0, 'total' => 0],
            'Aportes de los Socios' => ['capital_social' => 0, 'utilidades_retenidas' => 0, 'revalorizacion_activos' => 0, 'total' => 0],
            'Utilidad del Ejercicio' => ['capital_social' => 0, 'utilidades_retenidas' => 0, 'revalorizacion_activos' => 0, 'total' => 0],
            'Dividendos Declarados' => ['capital_social' => 0, 'utilidades_retenidas' => 0, 'revalorizacion_activos' => 0, 'total' => 0],
            'Revalorización de Activos' => ['capital_social' => 0, 'utilidades_retenidas' => 0, 'revalorizacion_activos' => 0, 'total' => 0],
            'Saldo Final' => ['capital_social' => 0, 'utilidades_retenidas' => 0, 'revalorizacion_activos' => 0, 'total' => 0],
        ];

        // Calcular los totales en base a los datos de la consulta
        foreach ($data as $row) {
            if ($row['nombreCuenta'] === 'Capital en acciones') {
                $totals['Saldo Inicial']['capital_social'] += $row['cargo'] - $row['abono'];
            } elseif ($row['nombreCuenta'] === 'Utilidades Retenidas') {
                $totals['Utilidad del Ejercicio']['utilidades_retenidas'] += $row['cargo'] - $row['abono'];
            } elseif ($row['nombreCuenta'] === 'Revalorización de Activos') {
                $totals['Revalorización de Activos']['revalorizacion_activos'] += $row['cargo'] - $row['abono'];
            }
            // Calcular el total de patrimonio neto para cada concepto
            $totals['Saldo Final']['total'] = $totals['Saldo Inicial']['capital_social'] + $totals['Utilidad del Ejercicio']['utilidades_retenidas'] + $totals['Revalorización de Activos']['revalorizacion_activos'];
        }

        return $totals;
    }
}

// Generar PDF
$pdf = new PDF($fechaInicioFormatted, $fechaFinFormatted, $anio);
$pdf->AliasNbPages();
$pdf->AddPage();
$data = $pdf->LoadData($fechaInicio, $fechaFin);
$totals = $pdf->calculateTotals($data);
$pdf->FancyTable($totals);

ob_end_clean(); // Limpia el buffer de salida
$pdf->Output();
?>
