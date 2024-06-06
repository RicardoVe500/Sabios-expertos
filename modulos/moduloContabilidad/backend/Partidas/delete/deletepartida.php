<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {

    $partidaId = $_POST['id'];

    $query = "DELETE FROM partidas WHERE partidaId = $partidaId ";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die ("Error en la consulta".mysqli_error($con));
          
      }

}


