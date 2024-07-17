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
        $query = "SELECT 
                    cc.cuentaId,
                    cc.numeroCuenta,
                    cc.nombreCuenta,
                    SUM(pd.cargo) AS ttcargo, 
                    SUM(pd.abono) AS ttabono,
                    cc.tipoSaldoId
                FROM partidaDetalle pd
                JOIN catalogocuentas cc ON pd.cuentaId = cc.cuentaId
                JOIN partidas p ON pd.partidaId = p.partidaId
                GROUP BY cc.cuentaId";

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
        $w = array(40, 50, 65, 35);  // Anchos de las celdas ajustados
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Restauración de colores y fuentes para los datos
        $this->SetFillColor(192, 192, 192);  // Gris para fondo de nivel 1
        $this->SetTextColor(0);
        
        $fill = false;
        $totalTtCargo = 0;
        $totalTtAbono = 0;

        foreach ($data as $row) {
            if ($row[3] == 1) {  // Nivel de cuenta 1
                $this->SetFont('', 'B');  // Fuente en negrita
                $this->SetFillColor(224, 224, 224);  // Fondo gris
            } else {
                $this->SetFont('');  // Fuente normal
                $this->SetFillColor(255, 255, 255);  // Fondo blanco
            }

            $this->Cell($w[0], 6, $row[1], 'LR', 0, 'L', true);
            $this->Cell($w[1], 6, $row[2], 'LR', 0, 'L', true);
            $this->Cell($w[2], 6, number_format($row[3], 2), 'LR', 0, 'R', true);
            $this->Cell($w[3], 6, number_format($row[4], 2), 'LR', 0, 'R', true);
            $this->Ln();
            
            // Acumular totales
            $totalTtCargo += $row[3];
            $totalTtAbono += $row[4];

        }

        // Línea de cierre
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();

        // Mostrar totales
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($w[0], 6, '', 0, 0, 'R', false);
        $this->Cell($w[1], 6, 'Totales:', 0, 0, 'R', false);
        $this->Cell($w[2], 6, number_format($totalTtCargo, 2), 1, 0, 'R', false);
        $this->Cell($w[3], 6, number_format($totalTtAbono, 2), 1, 0, 'R', false);
    }
}

$pdf = new PDF();

// Iniciar la creación del PDF
$pdf->AliasNbPages();
$pdf->AddPage();

// Obtener los datos
$data = $pdf->LoadData($con);

// Definir los encabezados de la tabla
$headers = array(utf8_decode('Número Cuenta'),  'Nombre Cuenta', 'Debe', 'Haber');

// Dibujar la tabla
$pdf->FancyTable($headers, $data);

// Cerrar la conexión de base de datos
mysqli_close($con);

// Generar el PDF
$pdf->Output();
?>
