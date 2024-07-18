<?php

include("../../../../../lib/config/conect.php");

$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST["fechaCierre"]) && isset($_POST["periodoId"])) {
    $periodoId = mysqli_real_escape_string($con, $_POST["periodoId"]);
    $fechaCierre = mysqli_real_escape_string($con, $_POST["fechaCierre"]);
    $comprobacion = 3; // Estado de comprobación
    $estadoId = 4; // Estado de cierre

    // Verificar si ya existe una fecha de cierre para el periodo dado
    $existingCierreQuery = "SELECT COUNT(*) as total FROM cierre WHERE fechaCierre = '$fechaCierre' AND periodoId = '$periodoId'";
    $existingCierreResult = mysqli_query($con, $existingCierreQuery);
    $existingCierreRow = mysqli_fetch_assoc($existingCierreResult);

    if ($existingCierreRow['total'] > 0) {
        echo json_encode(array("success" => false, "message" => "La fecha de cierre ya está registrada."));
    } else {
        // Verificar si existen partidas para la fecha especificada
        $checkPartidasExistQuery = "SELECT COUNT(*) as total FROM partidas WHERE fechacontable = '$fechaCierre' AND estadoId = $comprobacion";
        $checkPartidasExistResult = mysqli_query($con, $checkPartidasExistQuery);
        $partidasExistRow = mysqli_fetch_assoc($checkPartidasExistResult);

        if ($partidasExistRow['total'] == 0) {
            echo json_encode(array("success" => false, "message" => "No existen partidas con la fecha especificada en estado de comprobación."));
        } else {
            // Verificar que todas las partidas para la fecha especificada estén en el estado de comprobación
            $checkQuery = "SELECT COUNT(*) as total FROM partidas WHERE fechacontable = '$fechaCierre' AND estadoId != $comprobacion";
            $checkResult = mysqli_query($con, $checkQuery);
            $row = mysqli_fetch_assoc($checkResult);

            if ($row['total'] > 0) {
                echo json_encode(array("success" => false, "message" => "Existen partidas que aún están abiertas o en estado distinto de comprobación."));
            } else {
                // Si todas las partidas están en el estado de comprobación, procedemos a cerrar el periodo
                $updateQuery = "INSERT INTO cierre (estadoId, periodoId, fechaCierre) VALUES ('$estadoId', '$periodoId', '$fechaCierre')";
                $result = mysqli_query($con, $updateQuery);

                if (!$result) {
                    $error = mysqli_error($con);
                    echo json_encode(array("success" => false, "message" => "Error en la consulta: " . $error));
                } else {
                    echo json_encode(array("success" => true, "message" => "Se ha cerrado el periodo"));
                }
            }
        }
    }
} else {
    echo json_encode(array("success" => false, "message" => "Faltan datos necesarios para la operación"));
}
