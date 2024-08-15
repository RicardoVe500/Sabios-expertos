<?php
include("../../../../../lib/config/conect.php");


$query = "SELECT 
            catalogoCuentas.cuentaId,
            movimientos.movimiento,
            catalogoCuentas.numeroCuenta,
            catalogoCuentas.nivelCuenta,
            catalogoCuentas.nombreCuenta,
            catalogoCuentas.tipoSaldoId,
            tipoDeSaldo.nombreTipo
            FROM 
            catalogoCuentas 
            LEFT JOIN 
            movimientos  ON catalogoCuentas.movimientoId = movimientos.movimientoId
            LEFT JOIN 
            tipoDeSaldo  ON catalogoCuentas.tipoSaldoId = tipoDeSaldo.tipoSaldoId
            WHERE 
            catalogoCuentas.nivelCuenta = 1;";

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
            "tipoSaldoId"=>$row["tipoSaldoId"],
            "nombreTipo"=>$row["nombreTipo"],

        );
    }

    echo json_encode(array("data" => $data));


?>
