<?php
include("../../../../../lib/config/conect.php");
 
// Se Capturan los datos 
  $tipoPartidaId = $_POST["tipoPartidaId"];
  $estadoId = 1;
  $concepto = $_POST["concepto"];
  $fechacontable = $_POST["fechacontable"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 

//Se hace la consulta para poder obtener el campo abrebiacion de la tabla tipo partida
  $consultaTipoPartida = "SELECT abreviacion FROM tipoPartida WHERE tipoPartidaId='$tipoPartidaId'";
  $resultadoTipoPartida = mysqli_query($con, $consultaTipoPartida);
  $rowTipoPartida = mysqli_fetch_assoc($resultadoTipoPartida);
  $abreviacion = $rowTipoPartida['abreviacion'];

  //Se establece el formato del mes y el año 
  $mesActual = date("m");
  $anoActual = date("y");

 //se hace un conteo del maximo de campos que hay para que de esa manera le asigne un numero y pueda hacer el conteo 
  $consultaUltimoCodigo = "SELECT MAX(codigoPartida) AS ultimoCodigo FROM Partidas WHERE MONTH(fechacontable)='$mesActual' AND RIGHT(YEAR(fechacontable), 2)='$anoActual' AND tipoPartidaId='$tipoPartidaId'";
  $resultadoUltimoCodigo = mysqli_query($con, $consultaUltimoCodigo);
  $rowUltimoCodigo = mysqli_fetch_assoc($resultadoUltimoCodigo);

  //se hace la corroboracion si el campo que se obtubo anteriormente tine un dato o si es nulo
if ($rowUltimoCodigo['ultimoCodigo'] != null) {
    $ultimoCodigo = $rowUltimoCodigo['ultimoCodigo'];
    $numeroSecuencial = substr($ultimoCodigo, -3) + 1;
    $numeroSecuencial = str_pad($numeroSecuencial, 3, '0', STR_PAD_LEFT);
} else {
    $numeroSecuencial = "001";
}
  //Se concatena todos los datos que se han recolectado  
  $codigoPartida = $abreviacion . '-' . $mesActual . $anoActual . '-' . $numeroSecuencial;

  //Se hace la insercion a l abase de datos  
  $query = "INSERT INTO Partidas(tipoPartidaId, estadoId, codigoPartida, concepto , fechacontable, fechaActual, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$tipoPartidaId','$estadoId','$codigoPartida','$concepto','$fechacontable','$fechaHoraActual','','$fechaHoraActual','','$fechaHoraActual')";

  
  $result = mysqli_query($con, $query);

  //Manejo de errores
  if (!$result) {
    echo "Error en la consulta".mysqli_error($con);
      
  }


?>