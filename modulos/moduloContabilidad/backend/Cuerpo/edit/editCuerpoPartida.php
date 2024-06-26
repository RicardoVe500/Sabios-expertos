<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['partidaDetalleId'])) {
    $partidaDetalleId = $_POST['partidaDetalleId'];
    $partidaId = $_POST["partidaId"]; // Asegúrate de que este valor se envía correctamente desde el formulario
    $cuentaId = $_POST["cuentaId"];
    $tipoComprobanteId = $_POST["tipoComprobanteId"];
    $cargo = $_POST["cargo"] ?? 0;
    $abono = $_POST["abono"] ?? 0;
    $numeroComprobante = $_POST["numeroComprobante"];
    $fechaComprobante = $_POST["fechaComprobante"];
    $concepto = $_POST["concepto"];
    $fechaHoraActual = date("Y-m-d H:i:s"); 
  
    $query = "UPDATE partidaDetalle SET cuentaId = $cuentaId, tipoComprobanteId = $tipoComprobanteId, 
    cargo = $cargo, abono = $abono, numeroComprobante = $numeroComprobante, 
    fechaComprobante = '$fechaComprobante', concepto = '$concepto', fechaModifica = '$fechaHoraActual'
    WHERE partidaDetalleId = $partidaDetalleId";
  
    $result = mysqli_query($con, $query);
        
    if (!$result) {
        echo "Error en la consulta: " . mysqli_error($con);
    } else {
        // Calcular los nuevos totales de cargo y abono para la partida
        $querySum = "SELECT SUM(cargo) AS totalCargo, SUM(abono) AS totalAbono FROM partidaDetalle WHERE partidaId = '$partidaId'";
        $resultSum = mysqli_query($con, $querySum);

        if ($rowSum = mysqli_fetch_assoc($resultSum)) {
            $totalCargo = $rowSum['totalCargo'] ? $rowSum['totalCargo'] : 0;
            $totalAbono = $rowSum['totalAbono'] ? $rowSum['totalAbono'] : 0;
            $diferencia = $totalCargo - $totalAbono;
            

            // Actualizar los totales y la diferencia en la tabla Partidas
            $updateQuery = "UPDATE Partidas SET debe = '$totalCargo', haber = '$totalAbono', diferencia = '$diferencia' WHERE partidaId = '$partidaId'";
            $updateResult = mysqli_query($con, $updateQuery);

            if (!$updateResult) {
                echo "Error al actualizar los totales de Partidas: " . mysqli_error($con);
            } else {
                echo "Partida y totales actualizados correctamente.";

                // Si la diferencia es 0, actualizar el estado a 2
                if ($diferencia == 0) {
                    $updateEstadoQuery = "UPDATE Partidas SET estadoId = 2 WHERE partidaId = '$partidaId'";
                    $updateEstadoResult = mysqli_query($con, $updateEstadoQuery);

                    if (!$updateEstadoResult) {
                        echo "Error al actualizar el estado de la Partida: " . mysqli_error($con);
                    } else {
                        echo "Estado de la Partida actualizado a cerrado.";
                    }
                }else{
                    $updateEstadoQuery = "UPDATE Partidas SET estadoId = 1 WHERE partidaId = '$partidaId'";
                    $updateEstadoResult = mysqli_query($con, $updateEstadoQuery);
                    if (!$updateEstadoResult) {

                        echo "Error al actualizar el estado de la Partida: " . mysqli_error($con);
                    } else {
                        echo "Estado de la Partida actualizado a cerrado.";
                    }
                }
            }
        } else {
            echo "Error al calcular los nuevos totales: " . mysqli_error($con);
        }
    }
}
?>
