<?php

include("../../../../../lib/config/conect.php");
require_once("../../../../../lib/fpdf/fpdf.php");
require_once("../../../../../lib/fpdf/mc_table.php");

// Verifica que las fechas no estén vacías
if (!isset($_POST['fechadesde']) || !isset($_POST['fechahasta'])) {
    echo json_encode(['status' => 'warning', 'message' => 'Las fechas no pueden estar vacías']);
    exit();
}

// Obtener fechas del formulario
$fechaInicio = mysqli_real_escape_string($con, $_POST['fechadesde']);
$fechaFin = mysqli_real_escape_string($con, $_POST['fechahasta']);

if (empty($fechaInicio) || empty($fechaFin)) {
    echo json_encode(['status' => 'warning', 'message' => 'Las fechas no pueden estar vacías']);
    exit();
}

// Convertir fechas a formato Y-m-d
$fechaInicio = DateTime::createFromFormat('m/Y', $fechaInicio)->format('Y-m-01');
$fechaFin = DateTime::createFromFormat('m/Y', $fechaFin)->format('Y-m-t');

// Convertir fechas a nombres de meses y años
$fechaInicioFormatted = date('F Y', strtotime($fechaInicio));
$fechaFinFormatted = date('F Y', strtotime($fechaFin));
$anio = date('Y', strtotime($fechaFin));

class PDF extends PDF_MC_Table
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
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'SABIOS Y EXPERTOS', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Departamento de contabilidad', 0, 1, 'C');
        $this->Cell(0, 10, 'Estado de Cambios en el Patrimonio', 0, 1, 'C');
        $this->Cell(0, 10, "Periodo: $this->fechaInicio - $this->fechaFin, $this->anio", 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function LoadData($fechaInicio, $fechaFin)
    {
        $data = [
            'Capital Social' => [],
            'Reserva Legal' => [],
            'Resultados Acumulados' => []
        ];
        $mysqli = new mysqli('localhost', 'root', '', 'tesis');
        if ($mysqli->connect_error) {
            die("Conexión fallida: " . $mysqli->connect_error);
        }

        // Obtener datos para Capital Social
        $queryCapital = "SELECT pd.cargo, pd.abono, cc.nombreCuenta
                         FROM partidaDetalle pd
                         JOIN partidas p ON p.partidaId = pd.partidaId
                         JOIN catalogocuentas cc ON cc.cuentaId = pd.cuentaId
                         WHERE LEFT(cc.numeroCuenta, 1) = '3'
                           AND p.fechacontable BETWEEN '$fechaInicio' AND '$fechaFin'
                         ORDER BY cc.numeroCuenta ASC";
        $resultCapital = $mysqli->query($queryCapital);
        if ($resultCapital) {
            while ($row = $resultCapital->fetch_assoc()) {
                $data['Capital Social'][] = $row;
            }
        }

        // Obtener datos para Reserva Legal
        $queryReserva = "SELECT pd.cargo, pd.abono, cc.nombreCuenta
                         FROM partidaDetalle pd
                         JOIN partidas p ON p.partidaId = pd.partidaId
                         JOIN catalogocuentas cc ON cc.cuentaId = pd.cuentaId
                         WHERE LEFT(cc.numeroCuenta, 1) = '4'
                           AND p.fechacontable BETWEEN '$fechaInicio' AND '$fechaFin'
                         ORDER BY cc.numeroCuenta ASC";
        $resultReserva = $mysqli->query($queryReserva);
        if ($resultReserva) {
            while ($row = $resultReserva->fetch_assoc()) {
                $data['Reserva Legal'][] = $row;
            }
        }

        // Obtener datos para Resultados Acumulados
        $queryResultados = "SELECT pd.cargo, pd.abono, cc.nombreCuenta
                            FROM partidaDetalle pd
                            JOIN partidas p ON p.partidaId = pd.partidaId
                            JOIN catalogocuentas cc ON cc.cuentaId = pd.cuentaId
                            WHERE LEFT(cc.numeroCuenta, 1) = '5'
                              AND p.fechacontable BETWEEN '$fechaInicio' AND '$fechaFin'
                            ORDER BY cc.numeroCuenta ASC";
        $resultResultados = $mysqli->query($queryResultados);
        if ($resultResultados) {
            while ($row = $resultResultados->fetch_assoc()) {
                $data['Resultados Acumulados'][] = $row;
            }
        }

        $mysqli->close();
        return $data;
    }

    function FancyTable($data)
    {
        $this->SetFont('Arial', 'B', 12);

        foreach ($data as $section => $entries) {
            $this->Cell(0, 10, $section, 0, 1, 'L');
            $this->SetFont('Arial', '', 12);

            $total = 0;
            foreach ($entries as $entry) {
                $this->Cell(0, 10, $entry['nombreCuenta'] . ': ' . $entry['cargo'] . ' - ' . $entry['abono'], 0, 1, 'L');
                $total += $entry['cargo'] - $entry['abono'];
            }

            $this->Ln(5);
        }

        // Calculate and display total patrimonio
        $totalPatrimonio = $this->calculateTotalPatrimonio($data);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Total Patrimonio', 0, 1, 'L');
        $this->Cell(0, 10, '$ ' . number_format($totalPatrimonio, 2), 0, 1, 'L');
    }

    function calculateTotalPatrimonio($data)
    {
        $total = 0;
        foreach ($data as $entries) {
            foreach ($entries as $entry) {
                $total += $entry['cargo'] - $entry['abono'];
            }
        }
        return $total;
    }
}

$pdf = new PDF($fechaInicioFormatted, $fechaFinFormatted, $anio);
$pdf->AliasNbPages();
$pdf->AddPage();
$data = $pdf->LoadData($fechaInicio, $fechaFin);

$pdf->FancyTable($data);

$pdf->Output();
?>