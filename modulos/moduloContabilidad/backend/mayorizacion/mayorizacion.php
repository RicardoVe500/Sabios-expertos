<?php
include("../../../../lib/config/conect.php");

$usuario_sesion = $_SESSION['usuario'];
$mes = $SESSION['periodo']['mes'];
$anio = $SESSION['periodo']['anio'];
$fechaActualHoras = date('y-m-d h:i:s');
$solofecha = date('y-m-d');
$fecha = $_POST['fechacontable']; // la fecha que traemos de la tabla Partidas

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$selecCtsMayores = mysqli_query($con, 
    "SELECT cc.cuentaId, cc.numeroCuenta, cc.nombreCuenta, cc.cuentaDependiente, cc.nivelCuenta, cc.tipoSaldoId, ts.nombreTipo
    FROM catalogocuentas cc
    LEFT JOIN  tipoDeSaldo ts ON cc.tipoSaldoId = ts.tipoSaldoId
    WHERE cc.nivelCuenta = 3" )or die('ERROR consulta: '.mysqli_error($con));

$cuentaId = array();
$cuentaDependiente = array();
$cuentaMayor = array();
$nombreCuenta = array();
$cargo = array();
$abono = array();
$tipoSaldoId = array();
$saldo = array();
$index = 0;
$json = array();

    while ($cuentasMayDato = mysqli_fetch_assoc($selecCtsMayores)) {
        $selectSaldos = mysqli_query($con,
            "SELECT pd.partidaDetalleId, 
            SUM(pd.cargo) AS ttcargo, 
            SUM(pd.abono) AS ttabono,
            cc.cuentaId,
            cc.cuentaDependiente,
            p.partidaId,
            SUBSTRING(cc.numeroCuenta, 1, 4) AS cuentaMayor
            FROM partidaDetalle pd
            JOIN catalogocuentas cc ON pd.cuentaId = cc.cuentaId
            JOIN partidas p ON pd.partidaId = p.partidaId
            WHERE SUBSTRING(cc.numeroCuenta, 1, 4) = $cuentasMayDato[numeroCuenta] and p.fechacontable = '$fecha'
            AND estadoId = 3"
        )or die("001 ". mysqli_error($con));

        $datasaldos = mysqli_fetch_assoc($selectSaldos);
        if ($cuentasMayDato['tipoSaldoId'] == 1) {
            $saldo1 = $datasaldos['ttcargo'] - $datasaldos['ttabono'];
        }else{
            $saldo1 = $datasaldos['ttabono'] - $datasaldos['ttcargo'] ;
        }
        $saldo1 = $datasaldos['ttcargo'] - $datasaldos['ttabono'];
        if ($saldo1<0) {
            $saldo1 = $saldo1 * -1;
        }

        if ($datasaldos["ttcargo"] == 0 && $datasaldos["ttcargo"] == 0 && $saldo1 == 0) {
            //se omite ya que no me muestra nada si esta en 0
        }
        else{

            $cuentaId[$index] = $cuentasMayDato["cuentaId"];
            $cuentaDependiente[$index] = $cuentasMayDato["cuentaDependiente"];
            $cuentaMayor[$index] = $cuentasMayDato["numeroCuenta"];
            $nombreCuenta[$index] = $cuentasMayDato["nombreCuenta"];
            $cargo[$index] = $datasaldos["ttcargo"];
            $abono[$index] = $datasaldos["ttabono"];
            $tipoSaldoId[$index] = $cuentasMayDato["tipoSaldoId"];
            $saldo[$index] = $saldo1;
            $index ++;

            
        }

    }

 
/*
    $selPart = mysqli_query($con,
        "SELECT partidaId
        FROM partidas
        WHERE fechacontable = '$fecha'
        AND estadoId = 3"
    );

    $numParts = mysqli_num_rows($selPart);

    if($numParts == 0){
        foreach ($cuentaId as $dato => $value) {
            if($tipoSaldoId[$dato]== 1){
                $saldoDeudor = $saldo[$dato];
                $saldoAcreedor = 0;
            }else{
                $saldoDeudor = 0;
                $saldoAcreedor = $saldo[$dato];
            }
            $jsonDetalle = array();
            $jsonDetalle["cuentaId"] = $cuentaId[$dato];
            $jsonDetalle["cuentaDependiente"] = $cuentaDependiente[$dato];
            $jsonDetalle["cuentaMayor"] = $cuentaMayor[$dato];
            $jsonDetalle["nombreCuenta"] = $nombreCuenta[$dato];
            $jsonDetalle["totalCargo"] = round($cargo[$dato],2);
            $jsonDetalle["totalabono"] = round($abono[$dato],2);
            $jsonDetalle["saldoDeudor"] = round($saldoDeudor,2); //duda
            $jsonDetalle["saldoAcreedor"] = round($saldoAcreedor,2); //duda
            $jsonDetalle["estadoId"] = 0;
            $jsonDetalle["usuarioCrea"] = $usuario_sesion;

            $id = "Cuenta".$cuentaMayor[$dato]."";

            $json[$id] = $jsonDetalle;

            $updPartidas = mysqli_query($con, 
            "UPDATE partidas SET mayorizada = 2 
            WHERE fechacontable = '$fecha' AND (estadoId = 2 or estadoId = 3) 
            AND mayorizada = 1") or die(' updPartidas => '.mysqli_error($con));
        }

        $json = json_encode($json, JSON_UNESCAPED_SLASHES);
        if ($datMay["existe"]>=1) {
            $updMayorizacion = mysqli_query($son,
            "UPDATE mayorizacion SET
                detalles = '$json',
                cambio = 0,
                usuarioAgregar =  '$usuario_sesion'
                fechaEdita = '$fechaActualHoras'
            WHERE fecha = $solofecha") or die('insMayotizacion => '.mysqli_error($con));
        }else{
            $insMayorizacion = mysqli_query($con, 
            "INSERT INTO  mayorizacion
            (
                fecha, detalles,
                estado,usuarioCrea,fechaCrea
            )
            VALUES
            (
                '$fecha','$json'
                '0','$usuario_sesion',$fechaActualHoras 
            )
            ")
            or die(' instMayotizacion => '.mysqli_error($con));
        }

    }
*/