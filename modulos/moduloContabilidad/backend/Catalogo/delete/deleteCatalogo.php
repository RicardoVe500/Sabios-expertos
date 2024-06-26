<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {

    $cuentaId = $_POST['id'];

    $fetchQuery = "SELECT * FROM catalogoCuentas WHERE cuentaId = $cuentaId";
    $fetchResult = mysqli_query($con, $fetchQuery);
    $datosEliminados = mysqli_fetch_assoc($fetchResult);


    $query = "DELETE FROM catalogoCuentas WHERE cuentaId = $cuentaId ";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die ("Error en la consulta".mysqli_error($con));
          
      } else{$fechajson = date("Y-m-d");
      // Preparar datos para la bitácora
      $datos = [
        "cuentaId" => $cuentaId,
                "accion" => "Eliminacion_Catalogo",
                "datosEliminados" => $datosEliminados
      ];
     
    $jsonDatos = json_encode($datos);
    $fechajson = date("Y-m-d"); 

    // Insertar o actualizar la bitácora
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

}



