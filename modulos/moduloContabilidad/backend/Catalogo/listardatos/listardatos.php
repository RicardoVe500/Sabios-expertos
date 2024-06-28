<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT 
            catalogoCuentas.cuentaId,
            movimientos.movimiento,
            catalogoCuentas.numeroCuenta,
            catalogoCuentas.nivelCuenta,
            catalogoCuentas.nombreCuenta
            FROM 
            catalogoCuentas 
            LEFT JOIN 
            movimientos  ON catalogoCuentas.movimientoId = movimientos.movimientoId
            WHERE 
            catalogoCuentas.nivelCuenta = 1;
          ";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }


  $data = array();

    while ($row = mysqli_fetch_array($result)) {
        $data[] = array(
            "cuentaId"=>$row["cuentaId"],
            "movimiento"=>$row["movimiento"],
            "numeroCuenta"=>$row["numeroCuenta"],
            "nivelCuenta"=>$row["nivelCuenta"],
            "nombreCuenta"=>$row["nombreCuenta"],
        );
    }

    echo json_encode(array("data" => $data));


?>
