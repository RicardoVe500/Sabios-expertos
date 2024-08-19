<?php
include("../../../../../lib/config/conect.php");

if (!isset($con)) {
    die(json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']));
}

// Verificar si existen partidas abiertas (no cerradas)
$partidasAbiertasQuery = mysqli_query($con, "SELECT COUNT(*) as abiertas 
                                             FROM partidas 
                                             WHERE estadoId != 3"); // Asume que '3' es el estado cerrado
if (!$partidasAbiertasQuery) {
    die(json_encode(['success' => false, 'message' => 'Error en la consulta de partidas abiertas: ' . mysqli_error($con)]));
}

$partidasAbiertas = mysqli_fetch_assoc($partidasAbiertasQuery);

if ($partidasAbiertas['abiertas'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Te faltan partidas diarias o mensuales por cerrar.']);
    exit;
}

// Obtener el año actual para utilizarlo dinámicamente
$anioActual = date('Y');

// Iniciar transacción
mysqli_begin_transaction($con);

try {
    // Suponiendo que ya tienes un movimientoId válido
    $movimientoId = 1; // Asegúrate de que este ID exista en la tabla `movimientos`

    // Verificar si las cuentas ya existen para no duplicar
    $checkCuentaGanancias = mysqli_query($con, "SELECT cuentaId FROM catalogocuentas WHERE numeroCuenta = '1283'");
    if (mysqli_num_rows($checkCuentaGanancias) == 0) {
        // Crear la cuenta "Ganancias Retenidas" si no existe
        $query = "INSERT INTO catalogocuentas (numeroCuenta, nombreCuenta, movimientoId, usuarioAgrega, fechaAgrega) 
                  VALUES ('1283', 'Ganancias Retenidas', $movimientoId, 'admin', NOW())";
        if (!mysqli_query($con, $query)) {
            throw new Exception("Error al crear la cuenta Ganancias Retenidas: " . mysqli_error($con));
        }
        $cuentaGananciasId = mysqli_insert_id($con);
    } else {
        $cuentaGananciasId = mysqli_fetch_assoc($checkCuentaGanancias)['cuentaId'];
    }

    $checkCuentaPerdidas = mysqli_query($con, "SELECT cuentaId FROM catalogocuentas WHERE numeroCuenta = '1284'");
    if (mysqli_num_rows($checkCuentaPerdidas) == 0) {
        // Crear la cuenta "Pérdidas" si no existe
        $query = "INSERT INTO catalogocuentas (numeroCuenta, nombreCuenta, movimientoId, usuarioAgrega, fechaAgrega) 
                  VALUES ('1284', 'Pérdidas', $movimientoId, 'admin', NOW())";
        if (!mysqli_query($con, $query)) {
            throw new Exception("Error al crear la cuenta Pérdidas: " . mysqli_error($con));
        }
        $cuentaPerdidasId = mysqli_insert_id($con);
    } else {
        $cuentaPerdidasId = mysqli_fetch_assoc($checkCuentaPerdidas)['cuentaId'];
    }

    // Calcular la utilidad o pérdida del ejercicio
    $resultado = mysqli_query($con, "SELECT SUM(d.debe - d.haber) as ganancias 
                                     FROM detalle d 
                                     JOIN partidas p ON d.partidaId = p.partidaId
                                     WHERE p.fechacontable BETWEEN '$anioActual-01-01' AND '$anioActual-12-31'");
    
    // Verificar si la consulta fue exitosa
    if (!$resultado) {
        throw new Exception("Error en la consulta SQL: " . mysqli_error($con));
    }

    $row = mysqli_fetch_assoc($resultado);
    $ganancias = $row['ganancias'];

    // Determinar la cuenta de destino
    $cuentaId = ($ganancias > 0) ? $cuentaGananciasId : $cuentaPerdidasId;
    $cargo = ($ganancias > 0) ? $ganancias : 0;
    $abono = ($ganancias < 0) ? abs($ganancias) : 0;

    // Insertar la partida del cierre anual
    $query = "INSERT INTO partidas (codigoPartida, fechacontable, concepto, usuarioAgrega, fechaAgrega)
              VALUES ('CIERRE_ANUAL_$anioActual', NOW(), 'Cierre Anual $anioActual', 'admin', NOW())";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al insertar en la tabla partidas: " . mysqli_error($con));
    }
    
    $partidaId = mysqli_insert_id($con);

    // Inserción en partidaDetalle, agregando tipoComprobanteId
    $tipoComprobanteId = 1; // Asume un valor válido de tipoComprobanteId
    $query = "INSERT INTO partidaDetalle (partidaId, cuentaId, cargo, abono, concepto, usuarioAgrega, fechaAgrega, tipoComprobanteId)
              VALUES ($partidaId, $cuentaId, $cargo, $abono, 'Cierre Anual $anioActual', 'admin', NOW(), $tipoComprobanteId)";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al insertar en la tabla partidaDetalle: " . mysqli_error($con));
    }

    // Registrar el cierre en la tabla 'cierre'
    $query = "INSERT INTO cierre (periodoId, fechaCierre, usuarioAgrega) 
              VALUES ($anioActual, NOW(), 'admin')";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al insertar en la tabla cierre: " . mysqli_error($con));
    }

    // Commit de la transacción
    mysqli_commit($con);
    echo json_encode(['success' => true, 'message' => 'Cierre anual realizado exitosamente y ganancias/pérdidas registradas.']);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}







/* include("../../../../../lib/config/conect.php");

if (!isset($con)) {
    die(json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']));
}

// Verificar si existen partidas abiertas (no cerradas)
$partidasAbiertasQuery = mysqli_query($con, "SELECT COUNT(*) as abiertas 
                                             FROM partidas 
                                             WHERE estadoId != 3"); // Asume que '4' es el estado cerrado
if (!$partidasAbiertasQuery) {
    die(json_encode(['success' => false, 'message' => 'Error en la consulta de partidas abiertas: ' . mysqli_error($con)]));
}

$partidasAbiertas = mysqli_fetch_assoc($partidasAbiertasQuery);

if ($partidasAbiertas['abiertas'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Te faltan partidas diarias o mensuales por cerrar.']);
    exit;
}

// Obtener el año actual para utilizarlo dinámicamente
$anioActual = date('Y');

// Iniciar transacción
mysqli_begin_transaction($con);

try {
    // Suponiendo que ya tienes un movimientoId válido
    $movimientoId = 1; // Asegúrate de que este ID exista en la tabla `movimientos`

    // Verificar si las cuentas ya existen para no duplicar
    $checkCuentaGanancias = mysqli_query($con, "SELECT cuentaId FROM catalogocuentas WHERE numeroCuenta = '1283'");
    if (mysqli_num_rows($checkCuentaGanancias) == 0) {
        // Crear la cuenta "Ganancias Retenidas" si no existe
        $query = "INSERT INTO catalogocuentas (numeroCuenta, nombreCuenta, movimientoId, usuarioAgrega, fechaAgrega) 
                  VALUES ('1283', 'Ganancias Retenidas', $movimientoId, 'admin', NOW())";
        if (!mysqli_query($con, $query)) {
            throw new Exception("Error al crear la cuenta Ganancias Retenidas: " . mysqli_error($con));
        }
        $cuentaGananciasId = mysqli_insert_id($con);
    } else {
        $cuentaGananciasId = mysqli_fetch_assoc($checkCuentaGanancias)['cuentaId'];
    }

    $checkCuentaPerdidas = mysqli_query($con, "SELECT cuentaId FROM catalogocuentas WHERE numeroCuenta = '1284'");
    if (mysqli_num_rows($checkCuentaPerdidas) == 0) {
        // Crear la cuenta "Pérdidas" si no existe
        $query = "INSERT INTO catalogocuentas (numeroCuenta, nombreCuenta, movimientoId, usuarioAgrega, fechaAgrega) 
                  VALUES ('1284', 'Pérdidas', $movimientoId, 'admin', NOW())";
        if (!mysqli_query($con, $query)) {
            throw new Exception("Error al crear la cuenta Pérdidas: " . mysqli_error($con));
        }
        $cuentaPerdidasId = mysqli_insert_id($con);
    } else {
        $cuentaPerdidasId = mysqli_fetch_assoc($checkCuentaPerdidas)['cuentaId'];
    }

    // Calcular la utilidad o pérdida del ejercicio
    $resultado = mysqli_query($con, "SELECT SUM(d.debe - d.haber) as ganancias 
                                     FROM detalle d 
                                     JOIN partidas p ON d.partidaId = p.partidaId
                                     WHERE p.fechacontable BETWEEN '$anioActual-01-01' AND '$anioActual-12-31'");
    
    // Verificar si la consulta fue exitosa
    if (!$resultado) {
        throw new Exception("Error en la consulta SQL: " . mysqli_error($con));
    }

    $row = mysqli_fetch_assoc($resultado);
    $ganancias = $row['ganancias'];

    // Determinar la cuenta de destino
    $cuentaId = ($ganancias > 0) ? $cuentaGananciasId : $cuentaPerdidasId;
    $cargo = ($ganancias > 0) ? $ganancias : 0;
    $abono = ($ganancias < 0) ? abs($ganancias) : 0;

    // Insertar la partida del cierre anual
    $query = "INSERT INTO partidas (codigoPartida, fechacontable, concepto, usuarioAgrega, fechaAgrega)
              VALUES ('CIERRE_ANUAL_$anioActual', NOW(), 'Cierre Anual $anioActual', 'admin', NOW())";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al insertar en la tabla partidas: " . mysqli_error($con));
    }
    
    $partidaId = mysqli_insert_id($con);

    // Inserción en partidaDetalle, agregando tipoComprobanteId
    $tipoComprobanteId = 1; // Asume un valor válido de tipoComprobanteId
    $query = "INSERT INTO partidaDetalle (partidaId, cuentaId, cargo, abono, concepto, usuarioAgrega, fechaAgrega, tipoComprobanteId)
              VALUES ($partidaId, $cuentaId, $cargo, $abono, 'Cierre Anual $anioActual', 'admin', NOW(), $tipoComprobanteId)";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al insertar en la tabla partidaDetalle: " . mysqli_error($con));
    }

    // Registrar el cierre en la tabla 'cierre'
    $query = "INSERT INTO cierre (periodoId, fechaCierre, usuarioAgrega) 
              VALUES ($anioActual, NOW(), 'admin')";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al insertar en la tabla cierre: " . mysqli_error($con));
    }

    // Commit de la transacción
    mysqli_commit($con);
    echo json_encode(['success' => true, 'message' => 'Cierre anual realizado exitosamente y ganancias/pérdidas registradas.']);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} */

