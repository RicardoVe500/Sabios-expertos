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
    $this->Cell(30,10,'Catalogo de Cuentas',0,0,'C');
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
        $query = "SELECT nombreCuenta, numeroCuenta, cuentaDependiente, nivelCuenta FROM catalogoCuentas ORDER BY numeroCuenta";
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
    $this->SetFillColor(255, 255, 255);  // Rojo para cabecera, lo cambiamos a gris después
    $this->SetTextColor(0);
    $this->SetDrawColor(255, 255, 255); 
    $this->SetFont('', 'B');  // Fuente en negrita para cabecera

    // Cabecera
    $w = array(75, 50, 50, 15);  // Anchos de las celdas ajustados
    for ($i = 0; $i < count($header); $i++) {
        $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    }
    $this->Ln();

    // Restauración de colores y fuentes para los datos
    $this->SetFillColor(192, 192, 192);  // Gris para fondo de nivel 1
    $this->SetTextColor(0);
    
    $fill = false;
    foreach ($data as $row) {
        if ($row[3] == 1) {  // Nivel de cuenta 1
            $this->SetFont('', 'B');  // Fuente en negrita
            $this->SetFillColor(224, 224, 224);  // Fondo gris
        } else {
            $this->SetFont('');  // Fuente normal
            $this->SetFillColor(255, 255, 255);  // Fondo blanco
        }

        $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', true);
        $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', true);
        $this->Cell($w[2], 6, $row[2], 'LR', 0, 'R', true);
        $this->Cell($w[3], 6, $row[3], 'LR', 0, 'R', true);
        $this->Ln();
    }

    // Línea de cierre
    $this->Cell(array_sum($w), 0, '', 'T');
    }
}



$pdf = new PDF();

// Iniciar la creación del PDF
$pdf->AliasNbPages();
$pdf->AddPage();

// Obtener los datos
$data = $pdf->LoadData($con);

// Definir los encabezados de la tabla

$headers = array('Nombre Cuenta',  utf8_decode('Número Cuenta'), 'Cuenta Dependiente', 'Nivel');

// Dibujar la tabla
$pdf->FancyTable($headers, $data);

// Cerrar la conexión de base de datos
mysqli_close($con);

// Generar el PDF
$pdf->Output();

?>