<?php
// MAYORIZACION

// Conectar a la base de datos
/* include("../../../../../lib/config/conect.php"); */
include("../../../../lib/config/conect.php");

// Validar variables requeridas
if (!isset($periodoId) || !isset($fecha)) {
    die("Faltan variables requeridas.");
}

// Consulta del periodo
$check_periodo = mysqli_query($con, "SELECT mes, anio FROM periodo WHERE periodoId = $periodoId LIMIT 1")
    or die("Error en la consulta del periodo: " . mysqli_error($con));

// Validar consulta
if (!$check_periodo) {
    die("Error en la consulta del periodo: " . mysqli_error($con));
}

// Consultar partidas saldadas del día
$query_saldadas = "
    SELECT p.partidaId, 
           SUM(pd.cargo) AS cargo, 
           SUM(pd.abono) AS abono, 
           c.cuentaId, 
           c.numeroCuenta,
           substring(c.numeroCuenta, 1, 4) AS cuentaMayor
    FROM partidas p
    JOIN partidadetalle pd ON p.partidaId = pd.partidaId
    JOIN catalogocuentas c ON pd.cuentaId = c.cuentaId
    WHERE substring(c.numeroCuenta, 1, 4) = '{$contableMayores['numeroCuenta']}' 
          AND fechaAgrega = '$fecha'
          AND periodoId = $periodoId 
          AND saldo = 1
    GROUP BY c.cuentaId
";

$saldadas = mysqli_query($con, $query_saldadas)
    or die("Error en la consulta de partidas saldadas: " . mysqli_error($con));

// Validar resultado de la consulta
if ($saldadas) {
    while ($saldos = mysqli_fetch_assoc($saldadas)) {
        if ($contableMayores['saldo'] == 'D') {
            $saldo1 = $saldos['cargo'] - $saldos['abono']; // Deudor = Cargo - Abono
        } else {
            $saldo1 = $saldos['abono'] - $saldos['cargo']; // Acreedor = Abono - Cargo
        }

        if ($saldo1 < 0) {
            $saldo1 = $saldo1 * -1;
        }

        // Inicialización de arreglos y variables
        $index = $index ?? 0; // Validar $index
        $cuentaId[$index]               = $contableMayores["cuentaId"];
        $cuentaDependiente[$index]      = $contableMayores["cuentaDependiente"];
        $numeroCuenta[$index]           = $contableMayores["numeroCuenta"];
        $nombreCuenta[$index]           = $contableMayores["nombreCuenta"];
        $cargo[$index]                  = $saldos["cargo"];
        $abono[$index]                  = $saldos["abono"];
        $tipoSaldo[$index]              = $contableMayores["saldo"];
        $saldo[$index]                  = $saldo1;
        $index++;

        // Asignación de saldo deudor y acreedor de la cuenta a variables
        if ($tipoSaldo[$index - 1] == "D") {
            $saldoDeudor = $saldo[$index - 1];
            $saldoAcreedor = 0;
        } else {
            $saldoDeudor = 0;
            $saldoAcreedor = $saldo[$index - 1];
        }

        $jsonDetalles = [
            "cuentaId" => $cuentaId[$index - 1],
            "cuentaDependiente" => $cuentaDependiente[$index - 1],
            "numeroCuenta" => $numeroCuenta[$index - 1],
            "nombreCuenta" => $nombreCuenta[$index - 1],
            "fechaActual" => $fechaActual,
            "cargo" => round($cargo[$index - 1], 2),
            "abono" => round($abono[$index - 1], 2),
            "debe" => round($saldoDeudor, 2),
            "haber" => round($saldoAcreedor, 2),
            "estado" => 0,
            "usuarioCrea" => $_SESSION["usuarios"]
        ];

        $id = "cuenta" . $numeroCuenta[$index - 1];
        $json[$id] = $jsonDetalles;
    }

    $jsonEncoded = json_encode($json);

    // Validar existencia de mayorizacion para actualizar o insertar
    $query_mayor_existe = "SELECT COUNT(*) as cuenta FROM mayorizacion WHERE fecha = '$fecha'";
    $mayor_existe = mysqli_query($con, $query_mayor_existe)
        or die('Error al verificar existencia de mayorizacion: ' . mysqli_error($con));
    $mayor_data = mysqli_fetch_assoc($mayor_existe);

    if ($mayor_data['cuenta'] > 0) {
        // Actualizar mayorizacion
        $updateMayor = mysqli_query($con, "
            UPDATE mayorizacion SET 
                detalles = '$jsonEncoded',
                cambio = 0,
                usuarioEdita = '{$_SESSION['usuarios']}',
                fechaEdita = '$fechaActual'
            WHERE fecha = '$fecha'
        ") or die('Error en actualización de mayorizacion: ' . mysqli_error($con));
    } else {
        // Insertar nueva mayorizacion
        $insertMayor = mysqli_query($con, "
            INSERT INTO mayorizacion (
                fecha, hora, detalles, estado, usuarioCrea, fechaCrea
            ) VALUES (
                '$fecha', CURRENT_TIMESTAMP, '$jsonEncoded', '0', '{$_SESSION['usuarios']}', '$fechaActual'
            )
        ") or die('Error en inserción de mayorizacion: ' . mysqli_error($con));
    }
} else {
    die("No se encontraron saldos para las partidas.");
}
?>
