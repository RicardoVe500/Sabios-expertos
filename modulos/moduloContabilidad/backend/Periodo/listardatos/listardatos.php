<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT 
    periodo.periodoId, 
    periodo.anio, 
    periodo.mes, 
    periodo.estadoId,
    estado.estado
FROM 
    periodo
LEFT JOIN 
    estado ON periodo.estadoId = estado.estadoId;";

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
            "estado"=>$row["estado"],

        );
    }

    echo json_encode(array("data" => $data));


?>
