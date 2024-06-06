<?php
include("../../../../../lib/config/conect.php");



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
                       VALUES ('$movimientos','','','','','','','','','$nuevoNumeroCuenta','$dependiente','$nivelcuenta','$nombrecuenta','','$fechaHoraActual','','$fechaHoraActual')";

       $resultInsert = mysqli_query($con, $queryInsert);

        if (!$resultInsert) {
            echo "Todo bien";
        }
   } else {
       echo "Error al obtener el nivel de cuenta más alto: " . mysqli_error($con);
   }
  

?>