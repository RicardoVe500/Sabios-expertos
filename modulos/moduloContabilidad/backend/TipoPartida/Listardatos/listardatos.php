<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT * FROM tipoPartida";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $data = array();

    while ($row = mysqli_fetch_array($result)) {
        $data[] = array(
            "tipoPartidaId"=>$row["tipoPartidaId"],
            "nombrePartida"=>$row["nombrePartida"],
            "abreviacion"=>$row["abreviacion"],
            "descripcion"=>$row["descripcion"],
        );
    }

    echo json_encode(array("data" => $data));


?>
