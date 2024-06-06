<?php
include("../../../../../lib/config/conect.php");


$partidaId = $_POST["partidaId"];
$cuentaId = $_POST["selectcuentas"];

$tipoComprobanteId = $_POST["selectcomprobante"];
$cargo = $_POST["debeCuerpo"];
$abono = $_POST["haberCuerpo"];
$saldo = 1000;
$numeroComprobante = $_POST["numeroComprobante"];
$fechaComprobante = $_POST["fechaComprobante"];
$concepto = $_POST["conceptoespecifico"];
$fechaHoraActual = date("Y-m-d H:i:s"); 
  

  $queryInsert = "INSERT INTO partidaDetalle (partidaId, cuentaId ,tipoComprobanteId, cargo, abono , saldo, numeroComprobante, fechaComprobante, concepto, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$partidaId','$cuentaId','$tipoComprobanteId','$cargo','$abono','$saldo','$numeroComprobante','$fechaComprobante','$concepto','','$fechaHoraActual','','$fechaHoraActual')";


  $resultInsert = mysqli_query($con, $queryInsert);

if (!$resultInsert) {
    die("Error en la inserción: " . mysqli_error($con));
} else {
  // Calcular los totales de cargo y abono para la partida
  $querySum = "SELECT SUM(cargo) AS totalCargo, SUM(abono) AS totalAbono FROM partidaDetalle WHERE partidaId = '$partidaId'";
  $resultSum = mysqli_query($con, $querySum);

  if ($row = mysqli_fetch_assoc($resultSum)) {
      $totalCargo = $row['totalCargo'];
      $totalAbono = $row['totalAbono'];
      $diferencia = $totalCargo - $totalAbono;

      // Actualizar los totales en la tabla Partidas
      $queryUpdate = "UPDATE Partidas SET debe = '$totalCargo', haber = '$totalAbono', diferencia = '$diferencia' WHERE partidaId = '$partidaId'";
      $resultUpdate = mysqli_query($con, $queryUpdate);

      if (!$resultUpdate) {
          echo "Error al actualizar Partidas: " . mysqli_error($con);
      } else {
          echo "Partida y totales actualizados correctamente.";
      }
  } else {
      echo "Error al calcular totales: " . mysqli_error($con);
  }
}
?>
