<?php
session_start(); 

if (isset($_SESSION['periodo'])) {
    unset($_SESSION['periodo']); 

    echo json_encode(array("status" => "success", "message" => "Período eliminado con éxito"));
} else {
    echo json_encode(array("status" => "error", "message" => "No hay un período para eliminar"));
}
?>
