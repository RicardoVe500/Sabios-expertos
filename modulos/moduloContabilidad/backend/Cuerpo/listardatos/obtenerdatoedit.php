<?php
include("../../../../../lib/config/conect.php");

if (isset($_POST['id'])) {
$partidaDetalleId = $_POST["id"];

$query = "SELECT 
pd.partidaDetalleId,
p.partidaId,
p.codigoPartida,
tc.tipoComprobanteId,
tc.nombreComprobante,
cc.cuentaId, 
 CONCAT(cc.numeroCuenta, ' | ', cc.nombreCuenta) AS cuenta,
pd.cargo,
pd.abono,
pd.saldo,
pd.numeroComprobante,
pd.fechaComprobante,
pd.concepto
FROM 
partidaDetalle pd
LEFT JOIN 
partidas p ON pd.partidaId = p.partidaId
LEFT JOIN 
catalogocuentas cc ON pd.cuentaId = cc.cuentaId
LEFT JOIN 
tipoComprobante tc ON pd.tipoComprobanteId = tc.tipoComprobanteId
WHERE 
pd.partidaDetalleId = '$partidaDetalleId'";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $json = array();


    while ($row = mysqli_fetch_array($result)) {
        $json[] = array(
            
            "partidaDetalleId"=>$row["partidaDetalleId"],
            "partidaId"=>$row["partidaId"],
            "codigoPartida"=>$row["codigoPartida"],
            "tipoComprobanteId"=>$row["tipoComprobanteId"],
            "nombreComprobante"=>$row["nombreComprobante"],
            "cuentaId"=>$row["cuentaId"],
            "cuenta"=>$row["cuenta"],
            "cargo"=>$row["cargo"],
            "abono"=>$row["abono"],
            "saldo"=>$row["saldo"],
            "numeroComprobante"=>$row["numeroComprobante"],
            "fechaComprobante"=>$row["fechaComprobante"],
            "concepto"=>$row["concepto"],

        );
    }

    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
  }