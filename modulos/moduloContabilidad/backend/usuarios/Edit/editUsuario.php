<?php

// Iniciar Sesión
// session_start();

// Crear conexión a la BD
require_once '../../../../../lib/config/conect.php';

// Si hay un error en la conexión mostrar este mensaje
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

// Crea enlace existentes en la base de datos.
if (isset($_POST['usuarioId']) && isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['email']) && isset($_POST['clave']) && isset($_POST['rol'])) {
    $usuarioId = $_POST['usuarioId'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $clave = $_POST['clave'];
    $tipoUsuarioId = (int)$_POST['rol']; // Rol del usuario

    // Validar que el nombre y los apellidos no contengan números
    if (preg_match('/[0-9]/', $nombre) || preg_match('/[0-9]/', $apellidos)) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre y los apellidos no deben contener números.']);
        exit;
    }

    // Si el campo clave está vacío, ocurre el error.
    if (empty($clave)) {
        echo json_encode(['status' => 'error', 'message' => 'Debes confirmar tu contraseña']);
        exit;
    }

    $clave_segura = password_hash($clave, PASSWORD_BCRYPT, ['cost' => 4]);

    // Verificar si el correo ya existe para otro usuario
    $queryEmail = $con->prepare("SELECT usuarioId FROM usuarios WHERE email = ? AND usuarioId != ?");
    $queryEmail->bind_param('si', $email, $usuarioId);
    $queryEmail->execute();
    $resultadoEmail = $queryEmail->get_result();

    if ($resultadoEmail->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado por otro usuario.']);
        exit;
    }

    $queryEmail->close();

    // Consultar datos existentes para la bitácora
    $queryDatosExistentes = $con->prepare("SELECT nombre, apellidos, email, tipoUsuarioId FROM usuarios WHERE usuarioId = ?");
    $queryDatosExistentes->bind_param('i', $usuarioId);
    $queryDatosExistentes->execute();
    $resultado = $queryDatosExistentes->get_result();
    $datosAntiguos = $resultado->fetch_assoc();
    $queryDatosExistentes->close();

    // Preparar datos para la bitácora
    $fechaActual = date("Y-m-d");
    $datosBitacora = [
        "accion" => "Actualizacion_Usuario",
        "datosAntiguos" => $datosAntiguos,
        "datosNuevos" => [
            "nombre" => $nombre,
            "apellidos" => $apellidos,
            "email" => $email,
            "tipoUsuarioId" => $tipoUsuarioId
        ],
        "fechaHora" => $fechaActual
    ];

    // Verificar si ya existe un registro para el día actual en la bitácora
    $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechaActual'";
    $resultBitacora = mysqli_query($con, $queryBitacora);
    if ($row = mysqli_fetch_assoc($resultBitacora)) {
        $datosExistentes = json_decode($row["detalle"], true);
        if (!is_array($datosExistentes)) { // Asegurarse de que es un array
            $datosExistentes = [];
        }
        $datosExistentes[] = $datosBitacora;
        $jsonDatos = json_encode($datosExistentes);
        $updateQueryBitacora = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
        mysqli_query($con, $updateQueryBitacora);
    } else {
        $datosArray = [$datosBitacora]; // Asegúrate de que es un array
        $jsonDatos = json_encode($datosArray);
        $insertQueryBitacora = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechaActual', '$jsonDatos')";
        mysqli_query($con, $insertQueryBitacora);
    }

    // Actualizar usuario
    $query = $con->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, clave = ?, tipoUsuarioId = ? WHERE usuarioId = ?");
    $query->bind_param('ssssii', $nombre, $apellidos, $email, $clave_segura, $tipoUsuarioId, $usuarioId);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado exitosamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario: ' . $query->error]);
    }

    $query->close();
    $con->close();
}
?>