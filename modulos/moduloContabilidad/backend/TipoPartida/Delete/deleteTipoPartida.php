<?php 
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];
header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $tipoPartidaId = $_POST['id'];

    $fetchQuery = "SELECT nombrePartida, abreviacion, descripcion FROM tipoPartida WHERE tipoPartidaId = $tipoPartidaId";
    $fetchResult = mysqli_query($con, $fetchQuery);
    $datosEliminados = mysqli_fetch_assoc($fetchResult);

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
            // Preparar datos para la bitácora incluyendo todos los detalles del registro eliminado
            $datos = [
                "tipoPartidaId" => $tipoPartidaId,
                "Usuario que elimino" => $usuario_sesion,
                "accion" => "Eliminacion_tipo_partida",
                "datosEliminados" => $datosEliminados
            ];
            $jsonDatos = json_encode($datos);
            $fechajson = date("Y-m-d"); 

            // Insertar o actualizar la bitácora
            $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechajson'";
            $resultBitacora = mysqli_query($con, $queryBitacora);
            if ($row = mysqli_fetch_assoc($resultBitacora)) {
                // Actualiza el registro existente
                $datosExistentes = json_decode($row["detalle"], true);
                $datosExistentes[] = $datos;
                $jsonDatos = json_encode($datosExistentes);
                $updateQuery = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
                mysqli_query($con, $updateQuery);
            } else {
                // Crea un nuevo registro en la bitácora
                $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechajson', '$jsonDatos')";
                mysqli_query($con, $insertQuery);
            }

            echo json_encode(['success' => true, 'message' => 'Tipo de partida eliminado exitosamente.']);
        } else {
            die("Error en la consulta: " . mysqli_error($con));
        }
        
    }
}
?>
