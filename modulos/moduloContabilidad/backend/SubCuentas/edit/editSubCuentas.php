<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

if (isset($_POST["cuentaId"])) {
   
  $cuentaId = $_POST["cuentaId"];

    $selectQuery = "SELECT numeroCuenta, nombreCuenta FROM catalogoCuentas WHERE cuentaId = $cuentaId";
    $selectResult = mysqli_query($con, $selectQuery);
    $datosAntiguos = mysqli_fetch_assoc($selectResult);

  $nombreCuenta = $_POST["nombreCuenta"];
  $numeroCuenta = $_POST["numeroCuenta"];
  $tipoSaldo = $_POST["tipoSaldo"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 
  $movimientos = $_POST["movimientos"];

  $query = "UPDATE catalogoCuentas SET tipoSaldoId = $tipoSaldo, movimientoId =  $movimientos, numeroCuenta = $numeroCuenta, nombreCuenta = '$nombreCuenta', fechaModifica = '$fechaHoraActual', usuarioModifica = '$usuario_sesion'  
  WHERE cuentaId = $cuentaId ";

  $result = mysqli_query($con, $query);
      
  if (!$result) {
  echo "Error en la consulta".mysqli_error($con);
            
  }else{
    // Preparar datos para la bitácora
    $datosBitacora = [
      "accion" => "Modificado_SubCuentas",
      "Usuario Modifica" => $usuario_sesion,
      "datosAntiguos" => $datosAntiguos,
      "datosNuevos" => [
          "movimientos" => $movimientos,
          "numeroCuenta" => $numeroCuenta,
          "nombreCuenta" => $nombreCuenta,
      ],
      "fechaHora" => $fechaHoraActual
  ];
  $fechaActual = date("Y-m-d");

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
        


