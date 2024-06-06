<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST["cuentaId"])) {
   
        $cuentaId = $_POST["cuentaId"];
        $numeroCuenta = $_POST["numeroCuenta"];
        $nombreCuenta = $_POST["nombreCuenta"];
        $fechaHoraActual = date("Y-m-d H:i:s"); 

        $query = "UPDATE catalogoCuentas SET numeroCuenta = $numeroCuenta, nombreCuenta = '$nombreCuenta', fechaModifica = '$fechaHoraActual' 
        WHERE cuentaId = $cuentaId ";

        $result = mysqli_query($con, $query);
      
        if (!$result) {
          echo "Error en la consulta".mysqli_error($con);
            
        }
        
      }

