<?php

//Iniciar Sesion
session_start();
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

    // Query que redirige a la base de datos.
    $query = $con->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, clave = ?, tipoUsuarioId = ? WHERE usuarioId = ?");
    $query->bind_param('ssssii', $nombre, $apellidos, $email, $clave_segura, $tipoUsuarioId, $usuarioId);

    // Crear un error si el código se repite
    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado exitosamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario: ' . $query->error]);
    }

    $query->close();
    $con->close();
} 












/* //Si hay un error en la conexion mostrar este mensaje
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

//Crea enlace existentes en la base de datos.
if (isset($_POST['usuarioId']) && isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['email']) && isset($_POST['clave'])) {
    $usuarioId = $_POST['usuarioId'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $clave = $_POST['clave'];

    //Si el campo Nombre o Apellido se inserta un numero, ocurre el error!!.
    if (preg_match('/[0-9]/', $nombre) || preg_match('/[0-9]/', $apellidos)) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre y los apellidos no deben contener números.']);
        exit;
    } 
    
    //Si el campo clave esta vacio, ocurre el error.
    elseif (empty($clave)){
        echo json_encode(['status' => 'error', 'message' => 'Debes confirmar tu contaseña']);
        exit;
    }

    //Se crea un password hash como en agregar usuario para evitar observar la password del usuario
    $clave_segura = password_hash($clave, PASSWORD_BCRYPT, ['cost' => 4]);

    //Query que redirige a la base de datos.
    $query = $con->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, clave = ? WHERE usuarioId = ?");
    $query->bind_param('ssssi', $nombre, $apellidos, $email, $clave_segura, $usuarioId);

    //Crear un error si el codigo se repite

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado exitosamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario: ' . $query->error]);
    }

    $query->close();
    $con->close();
} 
 */