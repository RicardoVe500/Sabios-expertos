<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

$partidaId = $_POST["partidaId"];
$cuentaId = $_POST["selectcuentas"];
$tipoComprobanteId = $_POST["selectcomprobante"];
$cargo = $_POST["debeCuerpo"] ?? 0;
$abono = $_POST["haberCuerpo"] ?? 0;
$saldo = 0;
$numeroComprobante = $_POST["numeroComprobante"];
$fechaComprobante = $_POST["fechaComprobante"];
$concepto = $_POST["conceptoespecifico"];
$fechaHoraActual = date("Y-m-d H:i:s"); 

$queryInsert = "INSERT INTO partidaDetalle (partidaId, cuentaId, tipoComprobanteId, cargo, abono, saldo, numeroComprobante, fechaComprobante, concepto, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
VALUES ('$partidaId','$cuentaId','$tipoComprobanteId','$cargo','$abono','$saldo','$numeroComprobante','$fechaComprobante','$concepto','$usuario_sesion','$fechaHoraActual','$usuario_sesion','$fechaHoraActual')";

$resultInsert = mysqli_query($con, $queryInsert);

if (!$resultInsert) {
    die("Error en la inserción: " . mysqli_error($con));
} else {
    $querySum = "SELECT SUM(cargo) AS totalCargo, SUM(abono) AS totalAbono FROM partidaDetalle WHERE partidaId = '$partidaId'";
    $resultSum = mysqli_query($con, $querySum);

        $fechajson = date("Y-m-d");
        // Preparar datos para la bitácora
        $datos = [
          "accion" => "Agrego_Movimiento_Partida",
          "Usuario agrego" => $usuario_sesion,
          "datosIngresados" => [
              "partidaId" => $partidaId,
              "cuentaId" => $cuentaId,
              "cargo" => $cargo,
              "abono" => $abono,
              "fechaComprobante" => $fechaComprobante,
              "concepto" => $concepto,
          ]
      ];
      $jsonDatos = json_encode($datos);
    
      // Verificar si ya existe un registro para el día actual
      $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechajson'";
      $resultBitacora = mysqli_query($con, $queryBitacora);
      if ($row = mysqli_fetch_assoc($resultBitacora)) {
          // Actualiza el registro existente
          $datosExistentes = json_decode($row["detalle"], true);
          $datosExistentes[] = $datos;
          $jsonDatos = json_encode($datosExistentes);
          $updateQuery = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
          mysqli_query($con, $updateQuery);
      } else {
          // Crea un nuevo registro en la bitácora
          $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechajson', '$jsonDatos')";
          mysqli_query($con, $insertQuery);
      }
      
    

    if ($row = mysqli_fetch_assoc($resultSum)) {
        $totalCargo = $row['totalCargo'];
        $totalAbono = $row['totalAbono'];
        $diferencia = $totalCargo - $totalAbono;

        $queryUpdate = "UPDATE Partidas SET debe = '$totalCargo', haber = '$totalAbono', diferencia = '$diferencia' WHERE partidaId = '$partidaId'";
        $resultUpdate = mysqli_query($con, $queryUpdate);

        if (!$resultUpdate) {
            echo "Error al actualizar Partidas: " . mysqli_error($con);
        } else {
            echo "Partida y totales actualizados correctamente.";

            if ($diferencia == 0) {
                $queryEstado = "UPDATE Partidas SET estadoId = 2 WHERE partidaId = '$partidaId'";
                $resultEstado = mysqli_query($con, $queryEstado);

                if (!$resultEstado) {
                    echo "Error al actualizar el estado de la Partida: " . mysqli_error($con);
                } else {
                    echo "Estado de la Partida actualizado a cerrado.";
                } 
            }else{
                $queryEstado = "UPDATE Partidas SET estadoId = 1 WHERE partidaId = '$partidaId'";
                $resultEstado = mysqli_query($con, $queryEstado);

                if (!$resultEstado) {
                    echo "Error al actualizar el estado de la Partida: " . mysqli_error($con);
                } else {
                    echo "Estado de la Partida actualizado a cerrado.";
                } 

            }
        }
    } else {
        echo "Error al calcular totales: " . mysqli_error($con);
    }
}
?>
