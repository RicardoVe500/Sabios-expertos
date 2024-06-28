<?php
require_once '../../../../../lib/config/conect.php';
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario'];

if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

if (isset($_POST['usuarioId'])) {
    $usuarioId = $_POST['usuarioId'];

    if (!filter_var($usuarioId, FILTER_VALIDATE_INT)) {
        echo json_encode(['status' => 'error', 'message' => 'ID de usuario no válido.']);
        exit;
    }

    // Primero, recuperar los datos del usuario a eliminar
    $selectQuery = $con->prepare("SELECT nombre, apellidos, email FROM usuarios WHERE usuarioId = ?");
    $selectQuery->bind_param('i', $usuarioId);
    $selectQuery->execute();
    $resultado = $selectQuery->get_result();
    if ($usuarioData = $resultado->fetch_assoc()) {
        // Proceder a eliminar el usuario
        $query = $con->prepare("DELETE FROM usuarios WHERE usuarioId = ?");
        $query->bind_param('i', $usuarioId);

        if ($query->execute()) {
            $fechaHoraActual = date("Y-m-d H:i:s");
            // Preparar datos para la bitácora
            $datos = [
                "accion" => "Elimino_Usuario",
                "usuario" => $usuario_sesion,
                "datosEliminados" => $usuarioData,
            ];
            $jsonDatos = json_encode($datos);

            $fechaJson = date("Y-m-d");
            $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechaJson'";
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
                $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechaJson', '$jsonDatos')";
                mysqli_query($con, $insertQuery);
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Usuario eliminado exitosamente.', 'usuarioEliminado' => $usuarioData]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el usuario: ' . $query->error]);
        }
        $query->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró el usuario especificado.']);
    }
    $selectQuery->close();
    $con->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no recibido.']);
}
?>
