<?php 
include("../../../../../lib/config/conect.php");

header('Content-Type: application/json'); // Asegúrate de que PHP envíe la respuesta como JSON

$partidaId = $_POST["partidaId"]; // Recibir el valor del ID de la partida

$updateEstadoQuery = "UPDATE Partidas SET estadoId = 3 WHERE partidaId = '$partidaId'";
$updateEstadoResult = mysqli_query($con, $updateEstadoQuery);

if (!$updateEstadoResult) {
    // Devuelve un JSON con success = false y el mensaje de error
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar el estado de la Partida: " . mysqli_error($con)
    ]);
} else {
    // Devuelve un JSON con success = true y un mensaje de éxito
    echo json_encode([
        "success" => true,
        "message" => "Estado de la Partida actualizado a cerrado."
    ]);
}
?>
