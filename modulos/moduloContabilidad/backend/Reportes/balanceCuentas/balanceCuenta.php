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
        $this->SetFont('Arial', '', 10);
        // Movernos a la derecha nuevamente
        $this->Cell(80);
        // Sub-título: Departamento de contabilidad
        $this->Cell(30,10,'Departamento de contabilidad',0,0,'C');
        // Salto de línea
        $this->Ln(5);

        // Movernos a la derecha
        $this->Cell(80);
        // Sub-título: Catálogo de Cuentas
        $this->Cell(30,10,'Balance de Comprobacion',0,0,'C');
        // Salto de línea
        $this->Ln(5);

        // Movernos a la derecha
        $this->Cell(80);
        // Fecha de impresión
        $date = date('d-m-Y H:i:s');
        $this->Cell(30,10,'Fecha de impresion: ' . $date,0,0,'C');
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

    // Cargar datos
    function LoadData($con)
    {
        $query = "SELECT cc.nombreCuenta, cc.tipoSaldoId, cc.nivelCuenta, 
                    SUM(d.debe) as totaldebe, 
                    SUM(d.haber) as totalhaber
                    FROM
                    detalle d
                    JOIN catalogocuentas cc on d.cuentaId = cc.cuentaId
                    GROUP by cc.cuentaId;";

        $result = mysqli_query($con, $query);

        if (!$result) {
            die("Error en la consulta: " . mysqli_error($con));
        }

        $data = [];
        while ($row = mysqli_fetch_row($result)) {
            $data[] = $row;
        }
        return $data;
    }

    // Tabla coloreada
    function FancyTable($header, $data)
    {
        // Colores, ancho de línea y fuente en negrita
      
    }
}

$pdf = new PDF();

?>
