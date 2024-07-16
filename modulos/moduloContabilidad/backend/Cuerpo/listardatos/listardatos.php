<?php
include("../../../../../lib/config/conect.php");


$partidaId = $_POST["partidaId"];
$codigoPartida = $_POST["codigoPartida"];

$query = "SELECT 
pd.partidaDetalleId,
p.partidaId,
p.codigoPartida,
tc.nombreComprobante,
 CONCAT(cc.numeroCuenta, ' | ', cc.nombreCuenta) AS cuenta,
pd.cuentaId,
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
p.partidaId = '$partidaId' and p.codigoPartida = '$codigoPartida';";

$result = mysqli_query($con, $query);


  if (!$result) {
    die ("Error en la consulta".mysqli_error($con));
      
  }

  $data = array();

    while ($row = mysqli_fetch_array($result)) {
        $data[] = array(
            
            "partidaDetalleId"=>$row["partidaDetalleId"],
            "partidaId"=>$row["partidaId"],
            "cuentaId"=>$row["cuentaId"],
            "codigoPartida"=>$row["codigoPartida"],
            "nombreComprobante"=>$row["nombreComprobante"],
            "cuenta"=>$row["cuenta"],
            "cargo"=>$row["cargo"],
            "abono"=>$row["abono"],
            "saldo"=>$row["saldo"],
            "numeroComprobante"=>$row["numeroComprobante"],
            "fechaComprobante"=>$row["fechaComprobante"],
            "concepto"=>$row["concepto"],
        );
    }

    echo json_encode(array("data" => $data));

?>
