<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST['id'])) {

    $partidaId = $_POST['id'];

    $fetchQuery = "SELECT * FROM partidaDetalle WHERE partidaId = $partidaId";
    $fetchResult = mysqli_query($con, $fetchQuery);
    $datosEliminados = mysqli_fetch_assoc($fetchResult);

  //Se hace una comprobacion si hay datos que estan relacionados con la tabla hija.
    $checkQuery = "SELECT COUNT(*) AS num_hijos FROM partidaDetalle WHERE partidaId = $partidaId";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

  //Si e resultado es mayor que 1 eso indica que hay datos relacionados y se manda un mensaje de error.
    if ($row['num_hijos'] > 0) {
      echo json_encode(['success' => false, 'message' => 'No se puede eliminar esta partida porque tiene Movimientos.']);
  } else {
    //Si el resultado es nulo entonces procede ha hacer la delete.
      $query = "DELETE FROM partidas WHERE partidaId = $partidaId ";
      $result = mysqli_query($con, $query);

      if ($result) {
         // Preparar datos para la bitácora incluyendo todos los detalles del registro eliminado
         $datos = [
          "partidaId" => $partidaId,
          "Usuario elimino" => $usuario_sesion ,
          "accion" => "Eliminacion_Partida",
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
      
        include("mayorizacion.php");

        echo json_encode(['success' => true, 'message' => 'Tipo de partida eliminado exitosamente.']);
    } else {
        die("Error en la consulta: " . mysqli_error($con));
    }

  }

}

?>
