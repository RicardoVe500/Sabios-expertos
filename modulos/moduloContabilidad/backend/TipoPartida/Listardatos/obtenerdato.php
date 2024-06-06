<?php
include("../../../../../lib/config/conect.php");


if (isset($_POST['id'])) {
    $tipoPartidaId = $_POST['id'];

    $query = "SELECT * FROM tipoPartida WHERE tipoPartidaId = {$tipoPartidaId} "; 
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(
            "tipoPartidaId"=>$row["tipoPartidaId"],
            "nombrePartida"=>$row["nombrePartida"],
            "abreviacion"=>$row["abreviacion"],
            "descripcion"=>$row["descripcion"],
            "usuarioAgrega"=>$row["usuarioAgrega"],
            "fechaAgrega"=>$row["fechaAgrega"],
            "usuarioModifica"=>$row["usuarioModifica"],
            "fechaModifica"=>$row["fechaModifica"]
        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}