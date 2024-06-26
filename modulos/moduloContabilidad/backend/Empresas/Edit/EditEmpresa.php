<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST["empresaId"])) {

    $empresaId = $_POST["empresaId"];
    $fechaHoraActual = date("Y-m-d H:i:s");
    $fechaActual = date("Y-m-d");

    // Obtener los datos actuales antes de la modificación
    $selectQuery = "SELECT nombre, direccion, correo, telefono FROM empresa WHERE empresaId = $empresaId";
    $selectResult = mysqli_query($con, $selectQuery);
    $datosAntiguos = mysqli_fetch_assoc($selectResult);

    $nombre = $_POST["nombre"];
    $direccion = $_POST["direccion"];
    $correo = $_POST["correo"];
    $telefono = $_POST["telefono"];

    // Actualizar los datos
    $updateQuery = "UPDATE empresa SET nombre = '$nombre', direccion = '$direccion', correo = '$correo', telefono = '$telefono', fechaModifica = '$fechaHoraActual' WHERE empresaId = $empresaId";
    $result = mysqli_query($con, $updateQuery);

    if (!$result) {
        echo "Error en la consulta: " . mysqli_error($con);
    } else {
        // Preparar datos para la bitácora
        $datosBitacora = [
            "accion" => "Modificado_tipo_partida",
            "datosAntiguos" => $datosAntiguos,
            "datosNuevos" => [
                "Nombre Empresa" => $nombre,
                "Direccion" => $direccion,
                "Correo" => $correo,
                "telefono" => $telefono

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
