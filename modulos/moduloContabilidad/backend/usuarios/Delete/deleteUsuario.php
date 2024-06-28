<?php
require_once '../../../../../lib/config/conect.php';

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

    $query = $con->prepare("DELETE FROM usuarios WHERE usuarioId = ?");
    $query->bind_param('i', $usuarioId);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Usuario eliminado exitosamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el usuario: ' . $query->error]);
    }

    $query->close();
    $con->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no recibido.']);
}
?>
