<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];


if (isset($_POST['numeroCuenta']) || isset($_POST['nombreCuenta'])) {
  
  $nivelcuenta =  $_POST["nivelCuenta"] + 1;
  $movimientos = $_POST["movimientos"];
  $numerocuenta = $_POST["numeroCuenta"];
  $nombrecuenta = $_POST["nombreCuenta"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 
  $nuevoNumeroCuenta = $numerocuenta . '01';

  $prueba = 0;
  if($nivelcuenta == 2){ 
    while(true){
        $prueba++;
        $nuevoNumeroCuenta = $numerocuenta . $prueba;
        $queryCheck = "SELECT COUNT(*) as count FROM catalogoCuentas WHERE numeroCuenta = '$nuevoNumeroCuenta'";
        $resultCheck = mysqli_query($con, $queryCheck);
        $rowCheck = mysqli_fetch_assoc($resultCheck);
        if ($rowCheck['count'] == 0) {
            break; 
        }
    }
  }else{
  $contador = 1;
  while (true) {
      $queryCheck = "SELECT COUNT(*) as count FROM catalogoCuentas WHERE numeroCuenta = '$nuevoNumeroCuenta'";
      $resultCheck = mysqli_query($con, $queryCheck);
      $rowCheck = mysqli_fetch_assoc($resultCheck);
      if ($rowCheck['count'] > 0) {
          $contador++;
          $sufijo = str_pad($contador, 2, '0', STR_PAD_LEFT); 
          $nuevoNumeroCuenta = $numerocuenta . $sufijo;
      } else {
          break;
      }
  }
}
  $dependiente = substr($nuevoNumeroCuenta, 0, -2);

    if ($dependiente == null) {
        $dependiente = $numerocuenta;
    }

       $queryInsert = "INSERT INTO catalogoCuentas(movimientoId, n1, n2, n3, n4, n5, n6, n7, n8, numeroCuenta, cuentaDependiente, nivelCuenta, nombreCuenta, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
                       VALUES ('$movimientos','','','','','','','','','$nuevoNumeroCuenta','$dependiente','$nivelcuenta','$nombrecuenta','$usuario_sesion','$fechaHoraActual','$usuario_sesion','$fechaHoraActual')";

       $resultInsert = mysqli_query($con, $queryInsert);

        if (!$resultInsert) {
            $fechajson = date("Y-m-d");
  // Preparar datos para la bitácora
        $datos = [
            "accion" => "Agrego_SubCuenta",
            "Usuario agrega" => $usuario_sesion,
            "datosIngresados" => [
                "nuevoNumeroCuenta" => $nuevoNumeroCuenta,
                "nivelcuenta" => $nivelcuenta,
                "nombrecuenta" => $nombrecuenta,
                "dependiente" => $dependiente,
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

            echo "Todo bien";
        }
   } else {
       echo "Error al obtener el nivel de cuenta más alto: " . mysqli_error($con);
   }
  

?>