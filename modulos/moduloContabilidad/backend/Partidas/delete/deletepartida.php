<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {

    $partidaId = $_POST['id'];
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
        echo json_encode(['success' => true, 'message' => 'Tipo de partida eliminado exitosamente.']);
    } else {
        die("Error en la consulta: " . mysqli_error($con));
    }

  }

}

?>
