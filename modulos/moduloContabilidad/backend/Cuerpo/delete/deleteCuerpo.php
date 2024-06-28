<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {

    $partidaDetalleId = $_POST['id'];
    $partidaId = $_POST["partidaId"];

    $deleteQuery = "DELETE FROM partidaDetalle WHERE partidaDetalleId = '$partidaDetalleId'";
    $deleteResult = mysqli_query($con, $deleteQuery);
    
    if (!$deleteResult) {
        die("Error al eliminar el detalle de la partida: " . mysqli_error($con));
    } else {
        // Calcular los nuevos totales de cargo y abono para la partida
        $querySum = "SELECT SUM(cargo) AS totalCargo, SUM(abono) AS totalAbono FROM partidaDetalle WHERE partidaId = '$partidaId'";
        $resultSum = mysqli_query($con, $querySum);

        if ($rowSum = mysqli_fetch_assoc($resultSum)) {
            $totalCargo = $rowSum['totalCargo'] ? $rowSum['totalCargo'] : 0;
            $totalAbono = $rowSum['totalAbono'] ? $rowSum['totalAbono'] : 0;
            $diferencia = $totalCargo - $totalAbono;

            // Actualizar los totales en la tabla Partidas
            $updateQuery = "UPDATE Partidas SET debe = '$totalCargo', haber = '$totalAbono', diferencia = '$diferencia' WHERE partidaId = '$partidaId'";
            $updateResult = mysqli_query($con, $updateQuery);

            if (!$updateResult) {
                echo "Error al actualizar los totales de Partidas: " . mysqli_error($con);
            } else {
                echo "Partida y totales actualizados correctamente después de la eliminación.";

                // Si la diferencia es 0, actualizar el estado a 2 o si es diferente a 0 el estado es 1
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
