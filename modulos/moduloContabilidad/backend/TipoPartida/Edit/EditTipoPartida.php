<?php
include("../../../../../lib/config/conect.php");
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST["tipoPartidaId"])) {
    $tipoPartidaId = $_POST["tipoPartidaId"];
    $fechaHoraActual = date("Y-m-d H:i:s");
    $fechaActual = date("Y-m-d");

    // Obtener los datos actuales antes de la modificación
    $selectQuery = "SELECT nombrePartida, abreviacion, descripcion FROM tipoPartida WHERE tipoPartidaId = $tipoPartidaId";
    $selectResult = mysqli_query($con, $selectQuery);
    $datosAntiguos = mysqli_fetch_assoc($selectResult);

    $nombrePartida = $_POST["nombrePartida"];
    $abreviacion = $_POST["abreviacion"];
    $descripcion = $_POST["descripcion"];

    // Actualizar los datos
    $updateQuery = "UPDATE tipoPartida SET nombrePartida = '$nombrePartida', abreviacion = '$abreviacion', descripcion = '$descripcion', fechaModifica = '$fechaHoraActual', usuarioModifica = '$usuario_sesion' WHERE tipoPartidaId = $tipoPartidaId";
    $result = mysqli_query($con, $updateQuery);

    if (!$result) {
        echo "Error en la consulta: " . mysqli_error($con);
    } else {
        // Preparar datos para la bitácora
        $datosBitacora = [
            "accion" => "Modificado_tipo_partida",
            "Usuario que modifica" => "$usuario_sesion",
            "datosAntiguos" => $datosAntiguos,
            "datosNuevos" => [
                "nombrePartida" => $nombrePartida,
                "abreviacion" => $abreviacion,
                "descripcion" => $descripcion
            ],
            "fechaHora" => $fechaHoraActual
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
    }
}
?>
