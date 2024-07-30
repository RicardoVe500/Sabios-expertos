<?php
require_once '../../../../../lib/config/conect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['email'])) {
        $email = $_POST['email'];
        $clave = $_POST['clave'];
        $currentPassword = $_POST['currentPassword']; // Nueva variable para la contraseña actual
        $sessionEmail = $_SESSION['email'];

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($clave) && !empty($currentPassword)) {
            // Verificar la contraseña actual
            $sqlCheckPassword = "SELECT clave FROM usuarios WHERE email = ?";
            $stmtCheckPassword = $con->prepare($sqlCheckPassword);
            $stmtCheckPassword->bind_param("s", $sessionEmail);
            $stmtCheckPassword->execute();
            $resultCheckPassword = $stmtCheckPassword->get_result();

            if ($resultCheckPassword->num_rows > 0) {
                $row = $resultCheckPassword->fetch_assoc();
                if (password_verify($currentPassword, $row['clave'])) {
                    // Verificar si el correo electrónico ya existe en otro usuario
                    $sqlCheckEmail = "SELECT usuarioId FROM usuarios WHERE email = ? AND email != ?";
                    $stmtCheckEmail = $con->prepare($sqlCheckEmail);
                    $stmtCheckEmail->bind_param("ss", $email, $sessionEmail);
                    $stmtCheckEmail->execute();
                    $resultCheckEmail = $stmtCheckEmail->get_result();

                    if ($resultCheckEmail->num_rows > 0) {
                        echo json_encode(["status" => "error", "message" => "El correo electrónico ya está en uso"]);
                    } else {
                        // Encriptar la nueva contraseña
                        $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);

                        // Actualizar la base de datos
                        $sql = "UPDATE usuarios SET email = ?, clave = ? WHERE email = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("sss", $email, $hashedPassword, $sessionEmail);

                        if ($stmt->execute()) {
                            // Actualizar el email en la sesión
                            $_SESSION['email'] = $email;
                            echo json_encode(["status" => "success", "message" => "Perfil actualizado correctamente"]);
                        } else {
                            echo json_encode(["status" => "error", "message" => "Error al actualizar el perfil"]);
                        }

                        $stmt->close();
                    }

                    $stmtCheckEmail->close();
                } else {
                    echo json_encode(["status" => "error", "message" => "La contraseña actual es incorrecta"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
            }

            $stmtCheckPassword->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Correo inválido, contraseña nueva o contraseña actual vacía"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Sesión no válida"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no válido"]);
}

$con->close();
?>