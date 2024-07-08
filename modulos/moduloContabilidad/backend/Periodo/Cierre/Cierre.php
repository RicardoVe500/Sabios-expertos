<?php
include("../../../../../lib/config/conect.php");
require_once '../../../../../lib/config/verificarSesion.php';


$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST["id"])) {
    $periodoId = $_POST["id"];
    $comprobacion = 3;
    $estadoId = 4;
    $mes = $_SESSION['periodo']['mes']; 
    $anio = substr($_SESSION['periodo']['anio'], -2);



    // Formato esperado del código de partida, por ejemplo: 0724...
    $codigoPartida = $mes . $anio; 


    // Verificar el estado de todas las partidas que comiencen con el código especificado
    $checkQuery = "SELECT COUNT(*) as total FROM partidas WHERE codigopartida LIKE '$codigoPartida%' AND estadoId != $comprobacion";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['total'] > 0) {
        // Si hay alguna partida que no está en el estadoId 3, no se procede con la actualización
        echo json_encode(array("success" => false, "message" => "Existen partidas que aun estan abiertas."));
    } else {
        // Si todas las partidas están en el estadoId 3, procedemos a actualizar el periodo
        $updateQuery = "UPDATE periodo SET estadoId = $estadoId WHERE periodoId = $periodoId";
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
?>
