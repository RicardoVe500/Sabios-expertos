<?php
include("../../../../../lib/config/conect.php");



$query = "SELECT * FROM movimientos";

    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(

            "movimientoId"=>$row["movimientoId"],
            "movimiento"=>$row["movimiento"],

        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
