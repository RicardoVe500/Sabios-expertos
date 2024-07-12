<?php
include("../../../../../lib/config/conect.php");


if (isset($_POST['id'])) {
    $cuentaId = $_POST['id'];

    $query = "SELECT cc.cuentaId, cc.movimientoId, cc.tipoSaldoId,
    ts.nombreTipo, cc.numeroCuenta, cc.cuentaDependiente, cc.nivelCuenta,
    cc.nombreCuenta, cc.usuarioAgrega, cc.fechaAgrega, cc.usuarioModifica,
    cc.fechaModifica  FROM catalogoCuentas cc LEFT JOIN tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId
     WHERE cuentaId = {$cuentaId} ";
    $result = mysqli_query($con, $query); 

    if (!$result) {
        die("Error en la consulta".mysqli_error($con));
        
    }
    
    $json = array();

    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(
            "cuentaId"=>$row["cuentaId"],
            "movimientoId"=>$row["movimientoId"],
            "tipoSaldoId"=>$row["tipoSaldoId"],
            "nombreTipo"=>$row["nombreTipo"],
            "numeroCuenta"=>$row["numeroCuenta"],
            "cuentaDependiente"=>$row["cuentaDependiente"],
            "nivelCuenta"=>$row["nivelCuenta"],
            "nombreCuenta"=>$row["nombreCuenta"],
            "usuarioAgrega"=>$row["usuarioAgrega"],
            "fechaAgrega"=>$row["fechaAgrega"],
            "usuarioModifica"=>$row["usuarioModifica"],
            "fechaModifica"=>$row["fechaModifica"]
        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}