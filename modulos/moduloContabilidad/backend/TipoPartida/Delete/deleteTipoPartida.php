<?php
include("../../../../../lib/config/conect.php");
header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $tipoPartidaId = $_POST['id'];

    // Primero verificar si hay registros hijos asociados
    $checkQuery = "SELECT COUNT(*) AS num_hijos FROM partidas WHERE tipoPartidaId = $tipoPartidaId";
    $checkResult = mysqli_query($con, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['num_hijos'] > 0) {
        // Si hay registros hijos, no se puede eliminar y se envía un mensaje
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar este tipo de partida porque tiene partidas asociadas.']);
    } else {
        // Si no hay registros hijos, proceder a eliminar
        $query = "DELETE FROM tipoPartida WHERE tipoPartidaId = $tipoPartidaId";
        $result = mysqli_query($con, $query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Tipo de partida eliminado exitosamente.']);
        } else {
            die("Error en la consulta: " . mysqli_error($con));
        }
    }
}
?>
