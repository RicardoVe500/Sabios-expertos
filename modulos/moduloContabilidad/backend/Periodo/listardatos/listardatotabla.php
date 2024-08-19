<?php
include("../../../../../lib/config/conect.php");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (isset($_SESSION['periodo'])) {
    $mes = $_SESSION['periodo']['mes'];
    $anio = $_SESSION['periodo']['anio'];
  
}

$query = "SELECT 
    periodo.periodoId, 
    periodo.anio, 
    periodo.mes, 
    periodo.estadoId,
    estado.estado
FROM 
    periodo
LEFT JOIN 
    estado ON periodo.estadoId = estado.estadoId
WHERE
     periodo.mes = '$mes';";

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
