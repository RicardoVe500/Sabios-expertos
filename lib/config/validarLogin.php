<?php


// Conexión a la base de datos
require_once 'conect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario y proteger contra inyección SQL
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $clave = mysqli_real_escape_string($con, $_POST['clave']);

    // Usar una declaración preparada para evitar inyección SQL
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $usuario = $result->fetch_assoc(); // Cambié $email a $usuario

        // Comprobar la contraseña
        if (password_verify($clave, $usuario['clave'])) {
            // Guardar los datos del usuario en la sesión
            $_SESSION['logueado'] = 1;
            $_SESSION['usuario'] = $usuario['nombre'] . ' ' . $usuario['apellidos'];
            $_SESSION['email'] = $usuario['email'];
            
            // Obtener el rol del usuario
            $tipousuario_id = $usuario['tipoUsuarioId']; // Busqueda de tipousuario (Su ID)
            $_SESSION['tipoUsuarioId'] = $tipousuario_id; // Guardar el ID del rol en la sesión

            // Obtener el nombre del rol
            $sqlRol = "SELECT nombreTipo FROM tipousuario WHERE tipoUsuarioId = ?";
            $stmtRol = $con->prepare($sqlRol);
            $stmtRol->bind_param('i', $tipousuario_id);
            $stmtRol->execute();
            $resultRol = $stmtRol->get_result();

            if ($resultRol && $resultRol->num_rows == 1) {
                $rol = $resultRol->fetch_assoc();
                $_SESSION['nombreTipo'] = $rol['nombreTipo']; // Guardar el nombre del rol en la sesión

                echo json_encode(['status' => 'success', 'message' => 'Login exitoso']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el rol del usuario']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Correo o contraseña son incorrectos']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Los datos no coinciden. Por favor, verifica e inténtalo de nuevo']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida']);
}







/* //Conexion a la base de datos
require_once 'conect.php';

// Verificar si se enviaron datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $clave = mysqli_real_escape_string($con, $_POST['clave']);

    // Usar una declaración preparada para evitar inyección SQL
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $email = $result->fetch_assoc();

        // Comprobar la contraseña
        $verify = (password_verify($clave, $email['clave']));
            // Utilizar una sesión para guardar los datos del usuario logueado
            if ($verify) {
                $_SESSION['logueado'] = 1;
                $_SESSION['usuario'] = $email['nombre'].' '.$email['apellidos'];
                $_SESSION['email'] = $email['email'];
            
                // Obtener el rol del usuario desde la base de datos
                $rol = $email['tipoUsuarioId']; // Asumiendo que tienes el campo 'rol' en tu tabla de usuarios
            
                $_SESSION['nombreTipo'] = $rol; // Guardar el rol en la sesión
            
                echo json_encode(['status' => 'success', 'message' => 'Login exitoso']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Correo o Contraseña son incorrectas']);
            }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Los datos no coinciden. Por favor, verifica e inténtalo de nuevo']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida']);
} */

