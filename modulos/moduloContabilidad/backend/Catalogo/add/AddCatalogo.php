<?php
include("../../../../../lib/config/conect.php");


  
// Obtener movimientoId enviado desde JavaScript
  $movimientos = 1;
  $nivelcuenta = 1;
  $numerocuenta = $_POST["numeroCuenta"];
  $nombrecuenta = $_POST["nombreCuenta"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 

  

  $query = mysqli_query($con,"INSERT INTO catalogoCuentas(movimientoId, n1, n2, n3, n4, n5, n6, n7, n8, numeroCuenta, cuentaDependiente, nivelCuenta, nombreCuenta, usuarioAgrega, fechaAgrega, usuarioModifica, fechaModifica) 
  VALUES ('$movimientos','','','','','','','','','$numerocuenta','','$nivelcuenta','$nombrecuenta','','$fechaHoraActual','','$fechaHoraActual')") or die('ERROR INS USUARIO: '.mysqli_error($con));

echo "1";


?>

