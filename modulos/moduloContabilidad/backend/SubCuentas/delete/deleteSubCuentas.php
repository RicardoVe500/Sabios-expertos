<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {

    $cuentaId = $_POST['id'];

    $query = "DELETE FROM catalogoCuentas WHERE cuentaId = $cuentaId ";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die ("Error en la consulta".mysqli_error($con));
          
      }

}
