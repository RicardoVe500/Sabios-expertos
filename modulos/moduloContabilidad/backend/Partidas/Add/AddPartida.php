<?php
include("../../../../../lib/config/conect.php");
 
// Obtener movimientoId enviado desde JavaScript
  $tipoPartidaId = $_POST["tipoPartidaId"];
  $estadoId = 1;

  $concepto = $_POST["concepto"];
  $fechacontable = $_POST["fechacontable"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 


  $consultaTipoPartida = "SELECT abreviacion FROM tipoPartida WHERE tipoPartidaId='$tipoPartidaId'";
  $resultadoTipoPartida = mysqli_query($con, $consultaTipoPartida);
  $rowTipoPartida = mysqli_fetch_assoc($resultadoTipoPartida);
  $abreviacion = $rowTipoPartida['abreviacion'];

  $mesActual = date("m");
  $anoActual = date("y");

 
  $consultaUltimoCodigo = "SELECT MAX(codigoPartida) AS ultimoCodigo FROM Partidas WHERE MONTH(fechacontable)='$mesActual' AND RIGHT(YEAR(fechacontable), 2)='$anoActual' AND tipoPartidaId='$tipoPartidaId'";
  $resultadoUltimoCodigo = mysqli_query($con, $consultaUltimoCodigo);
  $rowUltimoCodigo = mysqli_fetch_assoc($resultadoUltimoCodigo);

if ($rowUltimoCodigo['ultimoCodigo'] != null) {
    $ultimoCodigo = $rowUltimoCodigo['ultimoCodigo'];
    $numeroSecuencial = substr($ultimoCodigo, -3) + 1;
    $numeroSecuencial = str_pad($numeroSecuencial, 3, '0', STR_PAD_LEFT);
} else {
    $numeroSecuencial = "001";
}
      
  $codigoPartida = $abreviacion . '-' . $mesActual . $anoActual . '-' . $numeroSecuencial;

   
  $query = "INSERT INTO Partidas(tipoPartidaId, estadoId, codigoPartida, concepto , fechacontable, fechaActual, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$tipoPartidaId','$estadoId','$codigoPartida','$concepto','$fechacontable','$fechaHoraActual','','$fechaHoraActual','','$fechaHoraActual')";

  $result = mysqli_query($con, $query);

  if (!$result) {
    echo "Error en la consulta".mysqli_error($con);
      
  }


?>