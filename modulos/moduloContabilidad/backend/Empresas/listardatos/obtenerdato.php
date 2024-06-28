<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {
    $empresaId = $_POST['id'];

    $query = "SELECT * FROM empresa WHERE empresaId = {$empresaId} "; 
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(
            "empresaId"=>$row["empresaId"],
            "nombre"=>$row["nombre"],
            "correo"=>$row["correo"],
            "direccion"=>$row["direccion"],
            "telefono"=>$row["telefono"],
            "usuarioAgrega"=>$row["usuarioAgrega"],
            "fechaAgrega"=>$row["fechaAgrega"],
            "usuarioModifica"=>$row["usuarioModifica"],
            "fechaModifica"=>$row["fechaModifica"]
        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}