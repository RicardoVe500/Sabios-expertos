<?php

include("../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];
$fechaActualHoras = date('y-m-d h:i:s');
$solofecha = date('y-m-d');
$cuentaId = $_POST['cuentaId'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Consulta inicial para sumar cargo y abono
$query = "SELECT 
            SUM(pd.cargo) AS ttcargo, 
            SUM(pd.abono) AS ttabono,
            cc.cuentaId,
            cc.tipoSaldoId
          FROM partidaDetalle pd
          JOIN catalogocuentas cc ON pd.cuentaId = cc.cuentaId
          JOIN partidas p ON pd.partidaId = p.partidaId
          WHERE cc.cuentaId = '$cuentaId'
          GROUP BY cc.cuentaId";

$result = $con->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ttcargo = $row['ttcargo'];
        $ttabono = $row['ttabono'];
        $tipoSaldoId = $row['tipoSaldoId'];


        if ( $tipoSaldoId == 1) {
            $saldofinal = $ttcargo - $ttabono;
        }else{
            $saldofinal =  $ttabono - $ttcargo;
        }


        // Verificar si existe el cuentaId en la tabla saldo
        $checkQuery = "SELECT cuentaId, SaldoAnterior FROM saldo WHERE cuentaId = '$cuentaId'";
        $checkResult = $con->query($checkQuery);


        if ($saldofinal<0) {
            //$saldofinal = $saldofinal * -1;

            if ($checkResult->num_rows > 0) {

           
               
                $oldSaldo = $checkResult->fetch_assoc()['SaldoAnterior'];
                // Si existe, hacemos UPDATE
                $updateQuery = "UPDATE saldo SET  debe = '$ttcargo', haber = '$ttabono', saldo = '$saldofinal', fecha = '$solofecha', saldoDia = '$saldofinal', SaldoAnterior = '$oldSaldo' WHERE cuentaId = '$cuentaId'";
                if (!$con->query($updateQuery)) {
                    echo "Error al actualizar datos: " . $con->error;
                }




            } else {
          
                // Si no existe, hacemos INSERT
                $insertQuery = "INSERT INTO saldo (cuentaId, debe, haber, fecha, saldo, saldoDia, SaldoAnterior) VALUES ('$cuentaId', '$ttcargo', '$ttabono', '$solofecha', '$saldofinal', '$saldofinal', '$saldofinal')";
                if (!$con->query($insertQuery)) {
                    echo "Error al insertar datos: " . $con->error;
                }




                
                
        }
        

        }else{

            if ($checkResult->num_rows > 0) {

               
               
                $oldSaldo = $checkResult->fetch_assoc()['SaldoAnterior'];

                // Si existe, hacemos UPDATE
                $updateQuery = "UPDATE saldo SET debe = '$ttcargo', haber = '$ttabono', saldo = '$saldofinal', fecha = '$solofecha', saldoDia = '$saldofinal', SaldoAnterior = '$oldSaldo' WHERE cuentaId = '$cuentaId'";
                if (!$con->query($updateQuery)) {
                    echo "Error al actualizar datos: " . $con->error;
                }
            } else {
                
         
                // Si no existe, hacemos INSERT
                $insertQuery = "INSERT INTO saldo (cuentaId, debe, haber, fecha, saldo, saldoDia, SaldoAnterior) VALUES ('$cuentaId', '$ttcargo', '$ttabono', '$solofecha', '$saldofinal', '$saldofinal', '$saldofinal')";
                if (!$con->query($insertQuery)) {
                    echo "Error al insertar datos: " . $con->error;
                }
        }


        }


    }
    echo json_encode(['status' => 'success', 'message' => 'Proceso completado correctamente']);

} else {
    echo json_encode(['status' => 'error', 'message' => 'No hay datos que procesar']);

}

?>