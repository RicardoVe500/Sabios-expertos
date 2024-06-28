<?php

// Conexión a la base de datos 
require_once '../../../../../lib/config/conect.php';

header('Content-Type: application/json');

if (isset($con) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nombreTipo = mysqli_real_escape_string($con, $input['nombreTipo'] ?? '');

    if (empty($nombreTipo)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo nombreTipo es obligatorio']);
        exit;
    }

    $query = "DELETE FROM tipoUsuario WHERE nombreTipo = '$nombreTipo'";
    if (mysqli_query($con, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Rol eliminado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar rol']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se estableció la conexión a la base de datos']);
}