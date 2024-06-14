<?php
include("../../../../../lib/config/conect.php");


  $nombrePartida = $_POST["nombrePartida"];
  $abreviacion = $_POST["abreviacion"];
  $descripcion = $_POST["descripcion"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 

  
  $query = "INSERT INTO tipoPartida(nombrePartida, abreviacion, descripcion, usuarioAgrega , fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$nombrePartida','$abreviacion','$descripcion','','$fechaHoraActual','','$fechaHoraActual')";

  $result = mysqli_query($con, $query);

  if (!$result) {
    echo "Error en la consulta".mysqli_error($con);
      
  }else{
    $fechajson = date("Y-m-d");
    // Preparar datos para la bitácora
    $datos = [
      "accion" => "ADD",
      "datosIngresados" => [
          "nombrePartida" => $nombrePartida,
          "abreviacion" => $abreviacion,
          "descripcion" => $descripcion,
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
  }


?>