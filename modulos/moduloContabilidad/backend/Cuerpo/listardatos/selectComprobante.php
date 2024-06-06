<?php
include("../../../../../lib/config/conect.php");

// Obtener el término de búsqueda de la petición GET y sanitizarlo
$searchTerm = isset($_GET['searchTerm']) ? mysqli_real_escape_string($con, $_GET['searchTerm']) : '';

// Modificar la consulta para incluir el término de búsqueda
$query = "SELECT * FROM tipoComprobante";
if (!empty($searchTerm)) {
    $query .= " WHERE nombreComprobante LIKE '%{$searchTerm}%'";
}

    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(

            "tipoComprobanteId"=>$row["tipoComprobanteId"],
            "nombreComprobante"=>$row["nombreComprobante"],

        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
