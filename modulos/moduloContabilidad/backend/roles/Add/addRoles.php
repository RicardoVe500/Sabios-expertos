<?php
require_once '../../../../../lib/config/conect.php';

if (isset($con) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nombreTipo = mysqli_real_escape_string($con, $input['nombreTipo']);
    $descripcion = mysqli_real_escape_string($con, $input['descripcion']);
    $fechaAgrega = date('Y-m-d H:i:s');
    $usuarioAgrega = 'default_user';

    if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== 1 || $_SESSION['nombreTipo'] !== 'Administrador') {
        echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
        exit;
    }

    // Verificar si el rol ya existe en la base de datos
    $query = "SELECT COUNT(*) as total FROM tipousuario WHERE nombreTipo = '$nombreTipo'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $total = $row['total'];

    if ($total > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Este rol ya existe']);
        exit;
    }

    // Si el rol no existe, procedemos a insertarlo
    $query_insert = "INSERT INTO tipousuario (nombreTipo, descripcion, usuarioAgrega, fechaAgrega) VALUES ('$nombreTipo', '$descripcion', '$usuarioAgrega', '$fechaAgrega')";
    if (mysqli_query($con, $query_insert)) {
        echo json_encode(['status' => 'success', 'message' => 'Rol agregado correctamente']);
    } else {
        echo json_encode(['status' => 'warning', 'message' => 'Error al agregar rol']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se estableció la conexión a la base de datos']);
}