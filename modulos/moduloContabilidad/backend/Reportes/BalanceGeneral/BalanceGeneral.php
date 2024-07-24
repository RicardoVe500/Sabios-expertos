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
