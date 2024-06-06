<?php
include("../../../../../lib/config/conect.php");


$searchTerm = isset($_GET['searchTerm']) ? mysqli_real_escape_string($con, $_GET['searchTerm']) : '';

// Modificar la consulta para incluir el término de búsqueda
    $query = "SELECT * FROM catalogoCuentas WHERE movimientoId = 1";
    if (!empty($searchTerm)) {
        $query .= " AND (nombreCuenta LIKE '%{$searchTerm}%' OR numeroCuenta LIKE '%{$searchTerm}%')";
    }

    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(

            "cuentaId"=>$row["cuentaId"],
            "numeroCuenta"=>$row["numeroCuenta"],
            "nombreCuenta"=>$row["nombreCuenta"],

        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
