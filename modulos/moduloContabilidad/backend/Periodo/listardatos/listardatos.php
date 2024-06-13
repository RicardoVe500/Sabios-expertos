<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT * FROM periodo";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $data = array();

    while ($row = mysqli_fetch_array($result)) {
        $data[] = array(
            "periodoId"=>$row["periodoId"],
            "anio"=>$row["anio"],
            "mes"=>$row["mes"],
            "estadoId"=>$row["estadoId"],
        );
    }

    echo json_encode(array("data" => $data));


?>
