<?php
include("../../../../../lib/config/conect.php");
header('Content-Type: application/json');
$usuario_sesion = $_SESSION['usuario'];
$fechaHoraActual = date("Y-m-d H:i:s"); 

if (isset($_POST['id'])) {
    $empresaId = $_POST['id'];

    $fetchQuery = "SELECT * FROM empresa WHERE empresaId = $empresaId";
    $fetchResult = mysqli_query($con, $fetchQuery);
    $datosEliminados = mysqli_fetch_assoc($fetchResult);

    
    $query = "DELETE FROM empresa WHERE empresaId = $empresaId";
    $result = mysqli_query($con, $query);

        if ($result) {
            // Preparar datos para la bitácora incluyendo todos los detalles del registro eliminado
            $datos = [
                "Empresa Eliminada"=>[
                    "accion" => "Eliminacion_Empresa",
                    "Usuario Elimina" => $usuario_sesion,
                    "Fecha Eliminacion" => $fechaHoraActual,
                    "datosEliminados" => $datosEliminados
                ],
            ];
            $jsonDatos = json_encode($datos);
            $fechajson = date("Y-m-d"); 

            // Insertar o actualizar la bitácora
            $queryBitacora = "SELECT bitacoraId, detalle FROM bitacora WHERE fecha = '$fechajson'";
            $resultBitacora = mysqli_query($con, $queryBitacora);
            if ($row = mysqli_fetch_assoc($resultBitacora)) {
                // Actualiza el registro existente
                $datosExistentes = json_decode($row["detalle"], true);
                $datosExistentes[] = $datos;
                $jsonDatos = json_encode($datosExistentes);
                $updateQuery = "UPDATE bitacora SET detalle = '$jsonDatos' WHERE bitacoraId = {$row['bitacoraId']}";
                mysqli_query($con, $updateQuery);
            } else {
                // Crea un nuevo registro en la bitácora
                $insertQuery = "INSERT INTO bitacora(fecha, detalle) VALUES ('$fechajson', '$jsonDatos')";
                mysqli_query($con, $insertQuery);
            }

            echo json_encode(['success' => true, 'message' => 'Empresa eliminado exitosamente.']);
        } else {
            die("Error en la consulta: " . mysqli_error($con));
        }
        
    }

?>
