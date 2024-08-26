<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

// Obtener datos enviados desde formulario
$numerocuenta = $_POST["numeroCuenta"];
$nombrecuenta = $_POST["nombreCuenta"];
$tipoSaldo = $_POST["selectTipoSaldo"];
$fechaHoraActual = date("Y-m-d H:i:s"); 

// Verificar si ya existe el numeroCuenta o nombreCuenta
$verificarQuery = "SELECT numeroCuenta, nombreCuenta FROM catalogoCuentas WHERE numeroCuenta = '$numerocuenta' OR nombreCuenta = '$nombrecuenta'";
$resultadoVerificacion = mysqli_query($con, $verificarQuery);

if (mysqli_num_rows($resultadoVerificacion) > 0) {
    echo "2";
} else {
    // Inserción del nuevo registro si no existe
    $movimientos = 2;
    $nivelcuenta = 1;
    $query = "INSERT INTO catalogoCuentas(movimientoId, tipoSaldoId, numeroCuenta, cuentaDependiente, nivelCuenta, nombreCuenta, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
              VALUES ('$movimientos','$tipoSaldo','$numerocuenta','','$nivelcuenta','$nombrecuenta','$usuario_sesion','$fechaHoraActual','$usuario_sesion','$fechaHoraActual')";
    mysqli_query($con, $query) or die('ERROR INS USUARIO: '.mysqli_error($con));
    echo "1";

    // Bitácora de acción
    $fechajson = date("Y-m-d");
    $datos = [
      "datosIngresados" => [
        "accion" => "Agrego_Cuenta",
        "Usuario que agrego" => $usuario_sesion,
            "numerocuenta" => $numerocuenta,
            "nivelcuenta" => $nivelcuenta,
            "nombrecuenta" => $nombrecuenta,
            "movimientos" => $movimientos,
            "fechaHoraActual" => $fechaHoraActual,
        ]
    ];
    
    $jsonDatos = json_encode($datos);
    
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
