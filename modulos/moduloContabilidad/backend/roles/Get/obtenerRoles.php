<?php
// Obtener roles

// Crear conexión a la base de datos
require_once '../../../../../lib/config/conect.php';

if (isset($con)) {
    $query = "SELECT tipoUsuarioId, nombreTipo, descripcion FROM tipousuario";
    $result = mysqli_query($con, $query);

    if ($result) {
        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $roles]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al obtener roles']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se estableció la conexión a la base de datos']);
}