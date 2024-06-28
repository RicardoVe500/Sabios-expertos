<?php
require_once '../../../../../lib/config/conect.php';
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario']; 

if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

if (isset($_POST['nombre'], $_POST['apellidos'], $_POST['email'], $_POST['clave'], $_POST['rol'])) {
    // Recoger los valores del formulario de registro
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidos = mysqli_real_escape_string($con, $_POST['apellidos']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $clave = mysqli_real_escape_string($con, $_POST['clave']);
    $tipoUsuarioId = (int)$_POST['rol'];
    $fecha = date("Y-m-d H:i:s");

    // Validar que el nombre y los apellidos no contengan números
    if (preg_match('/[0-9]/', $nombre)) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre no debe contener números']);
        exit;
    }

    if (preg_match('/[0-9]/', $apellidos)) {
        echo json_encode(['status' => 'error', 'message' => 'Los apellidos no deben contener números']);
        exit;
    }

    if (!$tipoUsuarioId) {
        echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar un rol']);
        exit;
    }

    $clave_segura = password_hash($clave, PASSWORD_BCRYPT, ['cost' => 4]);

    // Insertar los datos en la base de datos
    $query = "INSERT INTO usuarios (nombre, apellidos, email, clave, tipoUsuarioId) VALUES ('$nombre', '$apellidos', '$email', '$clave_segura', $tipoUsuarioId)";
    if (mysqli_query($con, $query)) {
        // Obtener el ID del usuario insertado
        $usuarioId = mysqli_insert_id($con);

        // Obtener los datos ingresados
        $datosIngresados = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'tipoUsuarioId' => $tipoUsuarioId,
            'fecha' => $fecha,
            'usuario' => $usuario_sesion // Agregar usuario de sesión
        ];

        // Preparar datos para la bitácora
        $datosBitacora = [
            "accion" => "Registro_Usuario",
            "datosIngresados" => $datosIngresados
        ];
        $jsonDatosBitacora = json_encode($datosBitacora);

        $fechaJson = date("Y-m-d");
        $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechaJson'";
        $resultBitacora = mysqli_query($con, $queryBitacora);
        if ($row = mysqli_fetch_assoc($resultBitacora)) {
            // Actualizar el registro existente en la bitácora
            $datosExistentes = json_decode($row["detalle"], true);
            $datosExistentes[] = $datosBitacora;
            $jsonDatosBitacora = json_encode($datosExistentes);
            $updateQuery = "UPDATE bitacora SET detalle = '$jsonDatosBitacora' WHERE bitacoraId = {$row['bitacoraId']}";
            mysqli_query($con, $updateQuery);
        } else {
            // Crear un nuevo registro en la bitácora
            $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechaJson', '$jsonDatosBitacora')";
            mysqli_query($con, $insertQuery);
        }

        // Devolver respuesta con los datos ingresados
        echo json_encode(['status' => 'success', 'message' => 'Registro exitoso.', 'datosIngresados' => $datosIngresados]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar los datos: ' . mysqli_error($con)]);
    }
    
    // Cerrar la conexión
    mysqli_close($con);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son requeridos.']);
}
?>
