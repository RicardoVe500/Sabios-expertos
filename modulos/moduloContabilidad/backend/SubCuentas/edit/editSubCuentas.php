<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST["cuentaId"])) {
   
  $cuentaId = $_POST["cuentaId"];
  $nombreCuenta = $_POST["nombreCuenta"];
  $numeroCuenta = $_POST["numeroCuenta"];
  $fechaHoraActual = date("Y-m-d H:i:s"); 
  $movimientos = $_POST["movimientos"];

  $query = "UPDATE catalogoCuentas SET movimientoId =  $movimientos, numeroCuenta = $numeroCuenta, nombreCuenta = '$nombreCuenta', fechaModifica = '$fechaHoraActual' 
  WHERE cuentaId = $cuentaId ";

  $result = mysqli_query($con, $query);
      
  if (!$result) {
  echo "Error en la consulta".mysqli_error($con);
            
  }
        
}

