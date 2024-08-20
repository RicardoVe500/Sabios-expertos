<?php
include("../../../../lib/config/conect.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Realiza la consulta
$query = "SELECT s.saldoId, s.cuentaId, s.debe, s.haber, s.fecha, s.saldo, cc.numeroCuenta,
         cc.cuentaDependiente, cc.nombreCuenta, cc.nivelCuenta, cc.tipoSaldoId, ts.nombreTipo
            FROM saldo s
            LEFT JOIN catalogocuentas cc ON s.cuentaId = cc.cuentaId
            LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId;";

$resultselect = mysqli_query($con, $query);

if ($resultselect) {
    
    if ($resultselect->num_rows > 0) {
        $data = array();
        
        while ($row = $resultselect->fetch_assoc()) {
            $formattedRow = array(
                "InformacionDeCuenta" => array(
                    "Nombre Cuenta"        => $row["nombreCuenta"],
                    "numero Cuenta"        => $row["numeroCuenta"],
                    "cuenta Dependiente"   => $row["cuentaDependiente"],
                    "Saldos" => array(
                        "Tipo saldo"         => $row["nombreTipo"],
                        "debe"               => $row["debe"],
                        "haber"              => $row["haber"],
                        "saldo"              => $row["saldo"],
                    )
                ),
                // Puedes agregar más campos aquí si es necesario
            );
            $data[] = $formattedRow;
        }
        
        $jsonData = json_encode($data);
        
        // Año que quieres insertar
        $year = date("Y"); 
        
        $insertQuery = "INSERT INTO saldosanuales (saldocierre, anio) VALUES ('$jsonData', $year)";

        $resultInsert = mysqli_query($con, $insertQuery);
    
        if ($resultInsert) {
            echo json_encode(['status' => 'success', 'message' => 'Inserción realizada correctamente']);

            $directoryPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $year;
            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            // Guarda el archivo JSON en la carpeta del año actual
            $filePath = $directoryPath . '/saldosAnuales.json';
            file_put_contents($filePath, $jsonData);

            // Elimina todos los datos de las tablas `saldos` y `partidaDetalle`
            $deleteSaldos = "DELETE FROM saldo";
            $deletePartidaDetalle = "DELETE FROM partidaDetalle";

            mysqli_query($con, $deleteSaldos);
            mysqli_query($con, $deletePartidaDetalle);

        } else {
            $errorMessage = mysqli_error($con);
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay datos para insertar']);
    }
} else {
    $errorMessage = mysqli_error($con);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
}

?>
