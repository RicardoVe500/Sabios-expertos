<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST['id'])) {

    $partidaDetalleId = $_POST['id'];
    $partidaId = $_POST["partidaId"];
 
    // Captura los detalles antes de eliminar
    $selectQuery = "SELECT pd.concepto, pd.cuentaId, 
    cc.nombreCuenta, cc.nivelCuenta, cc.numeroCuenta, 
    pd.cargo, pd.abono
    FROM partidaDetalle pd
    JOIN catalogocuentas cc 
    ON pd.cuentaId = cc.cuentaId WHERE partidaDetalleId = '$partidaDetalleId'";
    $selectResult = mysqli_query($con, $selectQuery);
    $detallePartida = mysqli_fetch_assoc($selectResult);

    $datosEliminacion = [
        "Partida Detalle Eliminado" => [
        "accion" => "Eliminacion_Movimiento_Partida",
        "Usuario eliminó" => $usuario_sesion,
        "datosEliminados" => $detallePartida
     ],
    ];

    $jsonDatosEliminacion = json_encode($datosEliminacion);

    // Eliminar el detalle de partida
    $deleteQuery = "DELETE FROM partidaDetalle WHERE partidaDetalleId = '$partidaDetalleId'";
    $deleteResult = mysqli_query($con, $deleteQuery);
    
    if (!$deleteResult) {
        die("Error al eliminar el detalle de la partida: " . mysqli_error($con));
    } else {
        // Registrar en la bitácora
        $fechajson = date("Y-m-d");
        $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechajson'";
        $resultBitacora = mysqli_query($con, $queryBitacora);

        if ($row = mysqli_fetch_assoc($resultBitacora)) {
            // Actualiza el registro existente
            $datosExistentes = json_decode($row["detalle"], true);
            $datosExistentes[] = $datosEliminacion;
            $jsonDatos = json_encode($datosExistentes);
            $updateQuery = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
            mysqli_query($con, $updateQuery);
        } else {
            // Crea un nuevo registro en la bitácora
            $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechajson', '$jsonDatosEliminacion')";
            mysqli_query($con, $insertQuery);
        }

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
                // Actualizar el estado de la partida
                if ($diferencia == 0) {
                    $updateEstadoQuery = "UPDATE Partidas SET estadoId = 2 WHERE partidaId = '$partidaId'";
                } else {
                    $updateEstadoQuery = "UPDATE Partidas SET estadoId = 1 WHERE partidaId = '$partidaId'";
                }
                $updateEstadoResult = mysqli_query($con, $updateEstadoQuery);

                if (!$updateEstadoResult) {
                    echo "Error al actualizar el estado de la Partida: " . mysqli_error($con);
                } else {
                    echo $diferencia == 0 ? "Estado de la Partida actualizado a cerrado." : "Estado de la Partida actualizado.";
                }
            }
        } else {
            echo "Error al calcular los nuevos totales: " . mysqli_error($con);
        }
    }
}
?>
