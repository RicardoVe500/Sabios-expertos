<?php

require_once("../../../../../lib/fpdf/fpdf.php");
require_once("../../../../../lib/fpdf/mc_table.php");

$fechaInicio = mysqli_real_escape_string($con, $_POST['fechadesde']);
$fechaFin = mysqli_real_escape_string($con, $_POST['fechahasta']);

class PDF extends PDF_MC_Table
{
        
    
    function Header()
    {
        $this->Image('../../../../../lib/img/images.png', 10, 7, 30);
        $this->SetFont('Arial','B',12);
        $this->Cell(80);
        $this->Cell(30,10,'SABIOS Y EXPERTOS',0,0,'C');
        $this->Ln(5);

        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(30,10,'Departamento de contabilidad',0,0,'C');
        $this->Ln(5);

        $this->Cell(80);
        $this->Cell(30,10,'Balance Preeliminar',0,0,'C');
        $this->Ln(5);

        $this->Cell(80);
        $date = date('d-m-Y H:i:s');
        $this->Cell(30,10,'Fecha de impresion: ' . $date,0,0,'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function LoadData()
    {
        $data = ['Activos' => [], 'Pasivos y Capital' => []];
        $mysqli = new mysqli('localhost', 'root', '', 'tesis');
        $query = "SELECT pd.partidaId, pd.cargo, pd.abono, pd.cuentaId, cc.nombreCuenta, cc.tipoSaldoId, p.fechacontable, cc.nivelCuenta,
                    CASE WHEN LEFT(cc.numeroCuenta, 1) = '1' THEN 'Activo'
                        WHEN LEFT(cc.numeroCuenta, 1) = '2' OR LEFT(cc.numeroCuenta, 1) = '3' THEN 'Pasivo y Capital'
                        END AS Tipo
                    FROM partidaDetalle pd
                    JOIN partidas p ON p.partidaId = pd.partidaId
                    JOIN catalogocuentas cc ON cc.cuentaId = pd.cuentaId
                    WHERE p.fechacontable BETWEEN 'fechadesde' AND 'fechahasta'
                    ORDER by cc.numeroCuenta ASC;";
        
        if ($result = $mysqli->query($query)) {
            while ($row = $result->fetch_assoc()) {
                if ($row['Tipo'] == 'Activo') {
                    $data['Activos'][] = $row;
                } else {
                    $data['Pasivos y Capital'][] = $row;
                }
            }
            $result->free();
        }
        $mysqli->close();
        return $data;
    }

    

    function FancyTable($header, $data)
        {
           
        }
        

}

// Crear una nueva instancia del documento PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Cargar los datos
$data = $pdf->LoadData();

// Añadir la tabla al PDF
$pdf->FancyTable(['Activos', 'Pasivos y Patrimonio'], $data);

// Generar el PDF
$pdf->Output(); // Para abrir en el navegador
?>
