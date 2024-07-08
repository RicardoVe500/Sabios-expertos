<?php
include("../../../../../lib/config/conect.php");
require_once '../../../../../lib/config/verificarSesion.php';
$usuario_sesion = $_SESSION['usuario'];

// Se Capturan los datos
$tipoPartidaId = $_POST["tipoPartidaId"];
$estadoId = 1;
$concepto = $_POST["concepto"];
$fechacontable = $_POST["fechacontable"];
$fechaHoraActual = date("Y-m-d H:i:s");

// Se establece el formato del mes y el año
$mesActual = $_SESSION['periodo']['mes'];
$anoActual = date("Y"); // Se usa el año completo para reiniciar cada año

// Se hace un conteo del máximo de campos que hay para asignar un número y reiniciar cada año
$consultaUltimoCodigo = "SELECT MAX(codigoPartida) AS ultimoCodigo FROM Partidas WHERE YEAR(fechacontable)='$anoActual' AND tipoPartidaId='$tipoPartidaId'";
$resultadoUltimoCodigo = mysqli_query($con, $consultaUltimoCodigo);
$rowUltimoCodigo = mysqli_fetch_assoc($resultadoUltimoCodigo);

// Se verifica si el campo obtenido anteriormente tiene un dato o si es nulo
if ($rowUltimoCodigo['ultimoCodigo'] != null) {
    $ultimoCodigo = $rowUltimoCodigo['ultimoCodigo'];
    $numeroSecuencial = substr($ultimoCodigo, -6) + 1;
    $numeroSecuencial = str_pad($numeroSecuencial, 6, '0', STR_PAD_LEFT);
} else {
    $numeroSecuencial = "000001";
}

// Se concatena todos los datos que se han recolectado
$codigoPartida = $mesActual . substr($anoActual, -2) . '-' . $numeroSecuencial;

// Se hace la inserción a la base de datos
$query = "INSERT INTO Partidas(tipoPartidaId, estadoId, codigoPartida, concepto, fechacontable, fechaActual, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) VALUES ('$tipoPartidaId','$estadoId','$codigoPartida','$concepto','$fechacontable','$fechaHoraActual','$usuario_sesion','$fechaHoraActual','$usuario_sesion','$fechaHoraActual')";

$result = mysqli_query($con, $query);

// Manejo de errores
if (!$result) {
    echo "Error en la consulta" . mysqli_error($con);
} else {
    $fechajson = date("Y-m-d");
    // Preparar datos para la bitácora
    $datos = [
        "accion" => "Agrego_Partida",
        "Usuario que agrego" => $usuario_sesion,
        "datosIngresados" => [
            "tipoPartidaId" => $tipoPartidaId,
            "estadoId" => $estadoId,
            "concepto" => $concepto,
            "fechacontable" => $fechacontable,
            "fechaHoraActual" => $fechaHoraActual,
        ]
    ];
    $jsonDatos = json_encode($datos);

    // Verificar si ya existe un registro para el día actual
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
}

?>
