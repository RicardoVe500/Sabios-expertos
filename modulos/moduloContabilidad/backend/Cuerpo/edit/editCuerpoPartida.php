<?php
include("../../../../../lib/config/conect.php");
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST['partidaDetalleId'])) {
    $partidaDetalleId = $_POST['partidaDetalleId'];
    $partidaId = $_POST["partidaId"];
    $cuentaId = $_POST["cuentaId"];
    $tipoComprobanteId = $_POST["tipoComprobanteId"];
    $cargo = $_POST["cargo"] ?? 0;
    $abono = $_POST["abono"] ?? 0;
    $numeroComprobante = $_POST["numeroComprobante"];
    $fechaComprobante = $_POST["fechaComprobante"];
    $concepto = $_POST["concepto"];
    $fechaHoraActual = date("Y-m-d H:i:s");

    // Captura los detalles antes de la actualización
    $selectQuery = "SELECT * FROM partidaDetalle WHERE partidaDetalleId = '$partidaDetalleId'";
    $selectResult = mysqli_query($con, $selectQuery);
    $detalleAnterior = mysqli_fetch_assoc($selectResult);

    // Ejecuta la actualización
    $updateQuery = "UPDATE partidaDetalle SET cuentaId = $cuentaId, tipoComprobanteId = $tipoComprobanteId, 
    cargo = $cargo, abono = $abono, numeroComprobante = $numeroComprobante, 
    fechaComprobante = '$fechaComprobante', concepto = '$concepto', fechaModifica = '$fechaHoraActual'
    WHERE partidaDetalleId = $partidaDetalleId";
    $updateResult = mysqli_query($con, $updateQuery);

    if (!$updateResult) {
        echo "Error en la consulta: " . mysqli_error($con);
    } else {
        // Captura los detalles después de la actualización
        $selectUpdatedQuery = "SELECT * FROM partidaDetalle WHERE partidaDetalleId = '$partidaDetalleId'";
        $selectUpdatedResult = mysqli_query($con, $selectUpdatedQuery);
        $detalleActualizado = mysqli_fetch_assoc($selectUpdatedResult);

        // Preparar y registrar la bitácora
        $datosBitacora = [
            "accion" => "Edicion_Movimiento_Partida",
            "Usuario modificó" => $usuario_sesion,
            "Datos Antiguos" => $detalleAnterior,
            "Datos Nuevos" => $detalleActualizado
        ];
        $jsonDatosBitacora = json_encode($datosBitacora);
        $fechajson = date("Y-m-d");
        $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechajson'";
        $resultBitacora = mysqli_query($con, $queryBitacora);

        if ($row = mysqli_fetch_assoc($resultBitacora)) {
            // Actualiza el registro existente en la bitácora
            $datosExistentes = json_decode($row["detalle"], true);
            $datosExistentes[] = $datosBitacora;
            $jsonDatos = json_encode($datosExistentes);
            $updateBitacoraQuery = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
            mysqli_query($con, $updateBitacoraQuery);
        } else {
            // Crea un nuevo registro en la bitácora
            $insertBitacoraQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechajson', '$jsonDatosBitacora')";
            mysqli_query($con, $insertBitacoraQuery);
        }

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
                    echo "Estado de la Partida actualizado.";
                }
            }
        } else {
            echo "Error al calcular los nuevos totales: " . mysqli_error($con);
        }
    }
}
?>
