<?php
require_once '../../../../../lib/config/conect.php';


if(isset($_POST['email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    
    $query = "SELECT u.usuarioId, u.nombre, u.apellidos, u.email, u.tipoUsuarioId, r.nombreTipo
          FROM usuarios u
          LEFT JOIN tipousuario r ON u.tipoUsuarioId = r.tipoUsuarioId
          WHERE u.email LIKE '%$email%'";
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $users = [];
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $users]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron usuarios con ese correo.']);
    }
    mysqli_close($con);
}





//"SELECT * FROM usuarios WHERE email LIKE '%$email%'"




/* $email = mysqli_real_escape_string($con, $_POST['email']);

$query = "SELECT u.usuarioId, u.nombre, u.apellidos, u.email, u.tipoUsuarioId, r.nombreTipo AS rol
          FROM usuarios u
          JOIN tipousuario r ON u.tipoUsuarioId = r.tipoUsuarioId
          WHERE u.email LIKE '%$email%'";

$result = mysqli_query($con, $query);

$usuarios = array();
while ($row = mysqli_fetch_assoc($result)) {
    $usuarios[] = $row;
}

if (count($usuarios) > 0) {
    echo json_encode(['status' => 'success', 'data' => $usuarios]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontraron usuarios.']);
}

mysqli_close($con);
 */