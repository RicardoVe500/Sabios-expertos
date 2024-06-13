<?php
session_start(); // Iniciar la sesión de PHP

if (isset($_POST['periodoId'], $_POST['mes'], $_POST['anio'])) {
    $periodoId = $_POST['periodoId'];
    $mes = $_POST['mes'];
    $anio = $_POST['anio'];
 
    // Guardar los datos en la sesión directamente, no en un array asociativo por ID
    $_SESSION['periodo'] = array(
        'id' => $periodoId, // Puedes guardar el ID si aún es necesario
        'mes' => $mes,
        'anio' => $anio
    );

    echo json_encode(array("status" => "success", "message" => "Periodo guardado con éxito"));
} else {
    echo json_encode(array("status" => "error", "message" => "Datos incompletos"));
}
?>
