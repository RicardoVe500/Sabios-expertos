<?php

//Conexion a la base de datos
include("../../../../lib/config/conect.php");

// Verificación de partidas balanceadas
$sql = "SELECT partidaId FROM partidas WHERE debe <> haber";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Partida no balanceada: " . $row["partidaId"] . "<br>";
    }
} else {
    echo "Todas las partidas están balanceadas.<br>";
}

// Consolidación de movimientos
$sql = "SELECT 
            pd.cuentaId,
            SUM(pd.cargo) AS total_cargos,
            SUM(pd.abono) AS total_abonos,
            (SUM(pd.cargo) - SUM(pd.abono)) AS saldo
        FROM partidadetalle pd
        JOIN partidas p ON pd.partidaId = p.partidaId
        WHERE p.estadoId = 1  -- Asegúrate de usar el estado correcto
        GROUP BY pd.cuentaId";

$result = $con->query($sql);

$cuentas = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cuentas[] = $row;
    }
} else {
    echo "No se encontraron movimientos.<br>";
}

// Actualización del libro mayor
foreach ($cuentas as $cuenta) {
    $cuentaId = $cuenta['cuentaId'];
    $totalCargos = $cuenta['total_cargos'];
    $totalAbonos = $cuenta['total_abonos'];
    $saldo = $cuenta['saldo'];
    $detalles = "Mayorización de la cuenta $cuentaId";

    $sql = "INSERT INTO mayorizacion (fecha, hora, detalles, estado, usuarioCrea, fechaCrea)
            VALUES (CURDATE(), CURTIME(), '$detalles', 'activo', 'usuario', NOW())
            ON DUPLICATE KEY UPDATE
                detalles = VALUES(detalles),
                estado = VALUES(estado),
                usuarioEdita = 'usuario',  -- Cambia esto por el usuario actual
                fechaEdita = NOW()";

    if ($con->query($sql) === TRUE) {
        echo "Cuenta $cuentaId mayorizada correctamente.<br>";
    } else {
        echo "Error al mayorizar la cuenta $cuentaId: " . $con->error . "<br>";
    }
}

// Marcar partidas como mayorizadas
$sql = "UPDATE partidas SET estadoId = 2 WHERE estadoId = 1";  // Cambia los valores de estado según corresponda

if ($con->query($sql) === TRUE) {
    echo "Partidas actualizadas a estado mayorizado.<br>";
} else {
    echo "Error al actualizar partidas: " . $con->error . "<br>";
}