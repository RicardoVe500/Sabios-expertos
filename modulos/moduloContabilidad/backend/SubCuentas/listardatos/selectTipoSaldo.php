<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT * FROM tipoDeSaldo";

    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(

            "tipoSaldoId"=>$row["tipoSaldoId"],
            "nombreTipo"=>$row["nombreTipo"],

        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
