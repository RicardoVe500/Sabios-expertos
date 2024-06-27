<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT empresaId, nombre, direccion, correo, telefono FROM empresa";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $data = array();

    while ($row = mysqli_fetch_array($result)) {
        $data[] = array(
            "empresaId"=>$row["empresaId"],
            "nombre"=>$row["nombre"],
            "direccion"=>$row["direccion"],
            "correo"=>$row["correo"],
            "telefono"=>$row["telefono"]
        );
    }

    echo json_encode(array("data" => $data));


?>