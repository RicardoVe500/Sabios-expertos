<?php
include("../../../../../lib/config/conect.php");

if (!isset($con)) {
    die(json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']));
}

// Obtener el año actual para utilizarlo dinámicamente
$anioActual = date('Y');

// Iniciar transacción
mysqli_begin_transaction($con);

try {
    // Calcular la utilidad o pérdida del ejercicio
    $resultado = mysqli_query($con, "SELECT SUM(COALESCE(d.debe, 0) - COALESCE(d.haber, 0)) as ganancias 
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
    $cuentaId = ($ganancias > 0) ? 1283 : 1284; // 1283 = Ganancias Retenidas, 1284 = Pérdidas
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

    // Vaciar la tabla saldo primero para poder vaciar catalogocuentas
    $query = "DELETE FROM saldo";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al vaciar la tabla saldo: " . mysqli_error($con));
    }

    // Vaciar la tabla partidaDetalle primero para poder vaciar catalogocuentas
    $query = "DELETE FROM partidaDetalle";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al vaciar la tabla partidaDetalle: " . mysqli_error($con));
    }

    // Vaciar la tabla detalle para poder vaciar catalogocuentas
    $query = "DELETE FROM detalle";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al vaciar la tabla detalle: " . mysqli_error($con));
    }

    // Vaciar la tabla catalogocuentas después de vaciar partidaDetalle y detalle
    $query = "DELETE FROM catalogocuentas";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al vaciar la tabla catalogocuentas: " . mysqli_error($con));
    }

    // Vaciar la tabla tipopartida
    $query = "DELETE FROM tipopartida";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al vaciar la tabla tipopartida: " . mysqli_error($con));
    }

    // Vaciar la tabla periodo
    $query = "DELETE FROM periodo";
    if (!mysqli_query($con, $query)) {
        throw new Exception("Error al vaciar la tabla periodo: " . mysqli_error($con));
    }

    // Commit de la transacción
    mysqli_commit($con);
    echo json_encode(['success' => true, 'message' => 'Cierre completo realizado exitosamente y sistema preparado para el nuevo año.']);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
