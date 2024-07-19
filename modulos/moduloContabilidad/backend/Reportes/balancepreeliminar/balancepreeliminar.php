<?php

require_once("../../../../../lib/fpdf/fpdf.php");
require_once("../../../../../lib/fpdf/mc_table.php");

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
        $query = "SELECT cc.nombreCuenta, s.saldo, cc.nivelCuenta,
                         CASE WHEN LEFT(cc.numeroCuenta, 1) = '1' THEN 'Activo'
                              WHEN LEFT(cc.numeroCuenta, 1) = '2' OR LEFT(cc.numeroCuenta, 1) = '3' THEN 'Pasivo y Capital'
                         END AS Tipo
                  FROM catalogocuentas cc
                  JOIN saldo s ON cc.cuentaId = s.cuentaId
                  ORDER BY cc.numeroCuenta ASC";
        
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
        $this->SetFont('Arial', '', 12);
        $this->Cell(90, 10, 'Activos', 1, 0, 'C');
        $this->Cell(90, 10, 'Pasivos y Capital', 1, 0, 'C');
        $this->Ln();
    
        $totalActivos = 0;
        $totalPasivosCapital = 0;
        $maxRows = max(count($data['Activos']), count($data['Pasivos y Capital']));
        for ($i = 0; $i < $maxRows; $i++) {
            // Para activos
            if (isset($data['Activos'][$i])) {
                if ($data['Activos'][$i]['nivelCuenta'] == 3) {
                    $this->SetFont('Arial', 'B', 10);
                } else {
                    $this->SetFont('Arial', '', 10);
                }
                $this->Cell(70, 10, $data['Activos'][$i]['nombreCuenta'], 1);
                $saldoActivo = number_format($data['Activos'][$i]['saldo'], 2);
                $this->Cell(20, 10, '$' . $saldoActivo, 1, 0, 'R');
                $totalActivos += $data['Activos'][$i]['saldo'];
            } else {
                $this->Cell(90, 10, '', 0);
            }
    
            // Para pasivos y capital
            if (isset($data['Pasivos y Capital'][$i])) {
                if ($data['Pasivos y Capital'][$i]['nivelCuenta'] == 3) {
                    $this->SetFont('Arial', 'B', 10);
                } else {
                    $this->SetFont('Arial', '', 10);
                }
                $this->Cell(70, 10, $data['Pasivos y Capital'][$i]['nombreCuenta'], 1);
                $saldoPasivo = number_format($data['Pasivos y Capital'][$i]['saldo'], 2);
                $this->Cell(20, 10, '$' . $saldoPasivo, 1, 0, 'R');
                $totalPasivosCapital += $data['Pasivos y Capital'][$i]['saldo'];
            } else {
                $this->Cell(90, 10, '', 0);
            }
            $this->Ln();
        }
    
        // Mostrar totales al final
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(70, 10, 'Total Activos:', 1);
        $this->Cell(20, 10, '$' . number_format($totalActivos, 2), 1, 0, 'R');
        $this->Cell(70, 10, 'Total Pasivos y Patrimonio:', 1);
        $this->Cell(20, 10, '$' . number_format($totalPasivosCapital, 2), 1, 0, 'R');
        $this->Ln();
    
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
$pdf->Output();// Para abrir en el navegador
?>
