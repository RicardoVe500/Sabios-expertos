<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];


  
// Obtener movimientoId enviado desde JavaScript
  $movimientos = 2;
  $nivelcuenta = 1;
  $numerocuenta = $_POST["numeroCuenta"];
  $nombrecuenta = $_POST["nombreCuenta"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 

  
  $query = mysqli_query($con,"INSERT INTO catalogoCuentas(movimientoId, n1, n2, n3, n4, n5, n6, n7, n8, numeroCuenta, cuentaDependiente, nivelCuenta, nombreCuenta, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$movimientos','','','','','','','','','$numerocuenta','','$nivelcuenta','$nombrecuenta','$usuario_sesion ','$fechaHoraActual','$usuario_sesion ','$fechaHoraActual')") or die('ERROR INS USUARIO: '.mysqli_error($con));
 

echo "1";

  $fechajson = date("Y-m-d");
  // Preparar datos para la bitácora
  $datos = [
    "accion" => "Agrego_Cuenta",
    "Usuario que agrego" => $usuario_sesion,
    "datosIngresados" => [
        "numerocuenta" => $numerocuenta,
        "nivelcuenta" => $nivelcuenta,
        "nombrecuenta" => $nombrecuenta,
        "movimientos" => $movimientos,
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

?>

