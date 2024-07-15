<?php

include("../../../../../lib/config/conect.php");



$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST["fechaCierre"])) {
    $periodoId = $_POST["periodoId"];
    $comprobacion = 3;
    $estadoId = 4;
    $fechaCierre = $_POST["fechaCierre"];

    // Verificar el estado de todas las partidas que comiencen con el código especificado
    $checkQuery = "SELECT COUNT(*) as total FROM partidas WHERE fechacontable = '$fechaCierre' AND estadoId != $comprobacion";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['total'] > 0) {
        // Si hay alguna partida que no está en el estadoId 3, no se procede con la actualización
        echo json_encode(array("success" => false, "message" => "Existen partidas que aun estan abiertas."));
    } else {
        // Si todas las partidas están en el estadoId 3, procedemos a actualizar el periodo
        $updateQuery = "INSERT INTO cierre(estadoId, periodoId, fechaCierre) 
                 VALUES ('$estadoId', '$periodoId', '$fechaCierre')";

        $result = mysqli_query($con, $updateQuery);

        if (!$result) {
            $error = mysqli_error($con);
            echo json_encode(array("success" => false, "message" => "Error en la consulta: " . $error));
        } else {
            echo json_encode(array("success" => true, "message" => "Se ha cerrado el periodo"));
        }
    }
} else {
    echo json_encode(array("success" => false, "message" => "Faltan datos necesarios para la operación"));
}


